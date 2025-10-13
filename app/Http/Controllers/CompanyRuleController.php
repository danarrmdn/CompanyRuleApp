<?php

namespace App\Http\Controllers;

use App\Models\CompanyRule;
use App\Models\Position;
use App\Models\User;
use App\Notifications\DocumentStatusUpdated;
use App\Notifications\NewDocumentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompanyRuleController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'all'); // Default to 'all'
        $data = [];

        switch ($view) {
            case 'latest':
                $approvedRules = CompanyRule::with(['creator', 'approver1', 'approver2', 'approver3'])
                                        ->where('status', 'Approved')
                                        ->where('is_obsolete', false)
                                        ->orderBy('document_name')
                                        ->get();
                $data['latestDocuments'] = $approvedRules->groupBy('category');
                $data['categories'] = [
                    '00 Policy & Principle: PP',
                    '10 Management System: MS',
                    '20 Business & Operation: BO',
                    '90 Others: OT',
                ];
                break;

            case 'obsolete':
                $data['obsoleteDocuments'] = CompanyRule::with(['creator', 'approver1', 'approver2', 'approver3'])
                                                    ->where('is_obsolete', true)
                                                    ->orderBy('created_at', 'desc')
                                                    ->paginate(15);
                break;

            case 'all':
            default:
                $query = CompanyRule::with(['creator', 'approver1', 'approver2', 'approver3']);
                if ($status = $request->input('status')) {
                    if ($status === 'approved') {
                        $query->where('status', 'Approved')->where('is_obsolete', false);
                    } elseif ($status === 'pending') {
                        $query->where('status', 'like', 'Pending%')->where('is_obsolete', false);
                    } else {
                        $query->where('status', $status)->where('is_obsolete', false);
                    }
                }
                $data['allDocuments'] = $query->orderBy('created_at', 'desc')->paginate(15);
                break;
        }

        return view('company-rules.index', compact('view', 'data'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        $totalDocuments = CompanyRule::count();
        $pendingDocuments = CompanyRule::where('status', 'like', 'Pending%')->count();
        $sendBackDocuments = CompanyRule::where('status', 'Send Back')->count();
        $draftDocuments = CompanyRule::where('status', 'Draft')->count();
        $approvedDocuments = CompanyRule::where('status', 'Approved')->where('is_obsolete', false)->count();
        $obsoleteDocuments = CompanyRule::where('is_obsolete', true)->count();
        $rejectedDocuments = CompanyRule::where('status', 'Rejected')->count();

        return view('dashboard', compact(
            'user',
            'totalDocuments',
            'pendingDocuments',
            'sendBackDocuments',
            'draftDocuments',
            'approvedDocuments',
            'obsoleteDocuments',
            'rejectedDocuments'
        ));
    }

    public function create()
    {
        $controllers = Position::has('holder')->with('holder')->get();
        $approvers_list = User::orderBy('name')->get();
        $userDepartment = Auth::user()->department ?? 'N/A';

        return view('company-rules.create', [
            'controllers' => $controllers,
            'approvers_list' => $approvers_list,
            'userDepartment' => $userDepartment,
        ]);
    }

    public function createRevision(Request $request)
    {
        $userDepartment = Auth::user()->department;

        $revisableRules = CompanyRule::where('status', 'Approved')
            ->where('is_obsolete', false)
            ->whereHas('creator', function ($query) use ($userDepartment) {
                $query->where('department', $userDepartment);
            })
            ->latest()
            ->get();

        $controllers = Position::has('holder')->with('holder')->get();
        $approvers_list = User::orderBy('name')->get();

        return view('company-rules.revision', compact('revisableRules', 'controllers', 'approvers_list'));
    }

    public function revise(CompanyRule $rule)
    {
        $controllers = Position::has('holder')->with('holder')->get();
        $approvers_list = User::orderBy('name')->get();
        $userDepartment = $rule->department;

        $nextVersion = ($rule->version ?? 0) + 1;

        return view('company-rules.revision', [
            'rule' => $rule,
            'controllers' => $controllers,
            'approvers_list' => $approvers_list,
            'userDepartment' => $userDepartment,
            'nextVersion' => $nextVersion,
        ]);
    }

    public function storeRevision(Request $request, CompanyRule $rule)
    {
        $isDraft = $request->input('is_draft');

        $rules = [
            'category' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:255',
            'document_name' => 'nullable|string|max:255',
            'reason_of_revision' => 'nullable|string',
            'effective_date' => 'nullable|date',
            'file_path_temp' => 'nullable|string',
            'controller_1_id' => 'nullable|exists:users,id',
            'controller_2_id' => 'nullable|exists:users,id',
            'controller_3_id' => 'nullable|exists:users,id',
            'controller_4_id' => 'nullable|exists:users,id',
            'controller_5_id' => 'nullable|exists:users,id',
            'approver_1_id' => 'nullable|exists:users,id',
            'approver_2_id' => 'nullable|exists:users,id',
            'approver_3_id' => 'nullable|exists:users,id',
        ];

        if (! $isDraft) {
            $rules['category'] = 'required|string|max:255';
            $rules['number'] = 'required|string|max:255';
            $rules['document_name'] = 'required|string|max:255';
            $rules['reason_of_revision'] = 'required|string';
            $rules['effective_date'] = 'required|date';
            $rules['file_path_temp'] = 'required|string';
            $rules['approver_1_id'] = 'required_without_all:approver_2_id,approver_3_id|nullable|exists:users,id';
        }

        $validatedData = $request->validate($rules, [
            'approver_1_id.required_without_all' => 'Please select at least one approver.',
            'file.required' => 'The PDF file is required when submitting.',
        ]);

        // Create new rule (the revision)
        $newRule = new CompanyRule;
        $newRule->fill($validatedData);

        $filePath = null;
        if ($request->filled('file_path_temp')) {
            $tempPath = $request->input('file_path_temp');
            if (Storage::disk('public')->exists($tempPath)) {
                $newPath = 'company-rules-files/'.basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
                $filePath = $newPath;
            }
        }

        $newRule->file_path = $filePath;
        $newRule->creator_id = $rule->creator_id; // Keep the original creator
        $newRule->version = $rule->version + 1;
        $newRule->previous_version_id = $rule->id;
        $newRule->is_obsolete = false;

        if ($isDraft) {
            $newRule->status = 'Draft';
            $newRule->save();
            $newRule->logActivity('Created as Draft Revision');

            return redirect()->route('company-rules.index')->with('success', 'Document revision has been saved as a draft.');
        }

        // Logic for submission
        $nextControllerId = null;
        $nextStatus = 'Approved';

        for ($i = 1; $i <= 5; $i++) {
            $controllerField = 'controller_'.$i.'_id';
            if (! empty($newRule->{$controllerField})) {
                $nextControllerId = $newRule->{$controllerField};
                $nextStatus = 'Pending Approval '.$i;
                break;
            }
        }

        $newRule->status = $nextStatus;
        $newRule->save();
        $newRule->logActivity('Submitted Revision for Approval');

        // Notify all users about the new document
        $users = User::all();
        Notification::send($users, new NewDocumentNotification($newRule));

        if ($nextControllerId) {
            $controllerToNotify = User::find($nextControllerId);
            if ($controllerToNotify) {
                $message = "A new revision for document '{$newRule->document_name}' requires your approval.";
                $actionUrl = route('approvals.index');
                $controllerToNotify->notify(new DocumentStatusUpdated($newRule->id, $message, $actionUrl));
            }
        }

        return redirect()->route('company-rules.index')->with('success', 'Document revision has been submitted for approval!');
    }

    public function uploadTempFile(Request $request)
    {
        if ($request->hasFile('file')) {
            try {
                $validator = validator(['file' => $request->file('file')], [
                    'file' => 'max:5000',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'error' => 'File size exceeds the maximum limit of 5MB.',
                    ], 413);
                }

                $file = $request->file('file');
                $path = $file->store('tmp', 'public');

                return response()->json(['path' => $path], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'File upload failed: '.$e->getMessage()], 500);
            }
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    }

    public function store(Request $request)
    {
        $isDraft = $request->input('is_draft');

        $rules = [
            'category' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:255',
            'document_name' => 'nullable|string|max:255',
            'reason_of_revision' => 'nullable|string',
            'effective_date' => 'nullable|date',
            'file_path_temp' => 'nullable|string',
            'controller_1_id' => 'nullable|exists:users,id',
            'controller_2_id' => 'nullable|exists:users,id',
            'controller_3_id' => 'nullable|exists:users,id',
            'controller_4_id' => 'nullable|exists:users,id',
            'controller_5_id' => 'nullable|exists:users,id',
            'approver_1_id' => 'nullable|exists:users,id',
            'approver_2_id' => 'nullable|exists:users,id',
            'approver_3_id' => 'nullable|exists:users,id',
        ];

        if (! $isDraft) {
            $rules['category'] = 'required|string|max:255';
            $rules['number'] = 'required|string|max:255';
            $rules['document_name'] = 'required|string|max:255';
            $rules['reason_of_revision'] = 'required|string';
            $rules['effective_date'] = 'required|date';
            $rules['file_path_temp'] = 'required|string';
            $rules['approver_1_id'] = 'required_without_all:approver_2_id,approver_3_id|nullable|exists:users,id';
        }

        $validatedData = $request->validate($rules, [
            'approver_1_id.required_without_all' => 'Please select at least one approver.',
            'file.required' => 'The PDF file is required when submitting.',
        ]);

        $creator = Auth::user();
        $creatorId = $creator->id;

        $filePath = null;
        if ($request->filled('file_path_temp')) {
            $tempPath = $request->input('file_path_temp');
            if (Storage::disk('public')->exists($tempPath)) {
                $newPath = 'company-rules-files/'.basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
                $filePath = $newPath;
            }
        }

        $rule = new CompanyRule;

        $rule->fill($validatedData);
        $rule->effective_date = ! empty($validatedData['effective_date']) ? $validatedData['effective_date'] : null;
        $rule->file_path = $filePath;
        $rule->creator_id = $creatorId;
        $rule->controller_1_id = $request->input('controller_1_id');
        $rule->controller_2_id = $request->input('controller_2_id');
        $rule->controller_3_id = $request->input('controller_3_id');
        $rule->controller_4_id = $request->input('controller_4_id');
        $rule->controller_5_id = $request->input('controller_5_id');
        $rule->approver_1_id = $request->input('approver_1_id');
        $rule->approver_2_id = $request->input('approver_2_id');
        $rule->approver_3_id = $request->input('approver_3_id');

        if ($isDraft) {
            $rule->status = 'Draft';
            $rule->save();
            $rule->logActivity('Created as Draft');
        } else {
            $nextControllerId = null;
            $nextStatus = 'Approved';

            for ($i = 1; $i <= 5; $i++) {
                $controllerField = 'controller_'.$i.'_id';
                if (! empty($rule->{$controllerField})) {
                    $nextControllerId = $rule->{$controllerField};
                    $nextStatus = 'Pending Approval '.$i;
                    break;
                }
            }

            $rule->status = $nextStatus;
            $rule->save();
            $rule->logActivity('Submitted for Approval');

            // Notify all users about the new document
            $users = User::all();
            Notification::send($users, new NewDocumentNotification($rule));

            if ($nextControllerId) {
                $controllerToNotify = User::find($nextControllerId);
                Log::info('Attempting to notify first approver. User ID: '.$nextControllerId);
                if ($controllerToNotify) {
                    $message = "A new document '{$rule->document_name}' requires your approval.";
                    $actionUrl = route('approvals.index');
                    $controllerToNotify->notify(new DocumentStatusUpdated($rule->id, $message, $actionUrl));
                } else {
                    Log::warning('Could not find User to notify for ID: '.$nextControllerId);
                }
            }
        }

        if ($isDraft) {
            return redirect()->route('company-rules.index')->with('success', 'Document has been saved as a draft.');
        } else {
            return redirect()->route('company-rules.index')->with('success', 'Document has been submitted for approval!');
        }
    }

    public function show(CompanyRule $rule)
    {
        $this->authorize('view', $rule);

        $user = Auth::user();

        // Define who can download and print
        $isCreator = $user->id === $rule->creator_id;
        $isSameDepartment = $user->department && $rule->creator && $user->department === $rule->creator->department;

        $controllerIds = array_filter([
            $rule->controller_1_id,
            $rule->controller_2_id,
            $rule->controller_3_id,
            $rule->controller_4_id,
            $rule->controller_5_id,
        ]);
        $isController = in_array($user->id, $controllerIds);

        $approverIds = array_filter([
            $rule->approver_1_id,
            $rule->approver_2_id,
            $rule->approver_3_id,
        ]);
        $isApprover = in_array($user->id, $approverIds);

        $canDownloadAndPrint = $isCreator || $isSameDepartment || $isController || $isApprover;

        $backUrl = url()->previous();

        return view('company-rules.show', compact('rule', 'canDownloadAndPrint', 'backUrl'));
    }

    public function edit(CompanyRule $rule)
    {
        $user = auth()->user();
        $canEdit = $user->id === $rule->creator_id &&
                  in_array($rule->status, ['Draft', 'Send Back']);

        if (! $canEdit) {
            return redirect()->route('company-rules.show', $rule)
                ->with('error', 'You cannot edit this document. Only the document creator can edit it when in Draft or Send Back status.');
        }

        $controllers = Position::with('holder')->get();
        $approvers_list = User::orderBy('name')->get();

        return view('company-rules.edit', [
            'rule' => $rule,
            'controllers' => $controllers,
            'approvers_list' => $approvers_list,
        ]);
    }

    public function update(Request $request, CompanyRule $rule)
    {
        $isDraft = $request->input('is_draft');

        $validatedData = $request->validate([
            'category' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'number' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'document_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'reason_of_revision' => $isDraft ? 'nullable|string' : 'required|string',
            'effective_date' => $isDraft ? 'nullable|date' : 'required|date',
            'file' => 'nullable|string',
            'controller_1_id' => 'nullable|exists:users,id',
            'controller_2_id' => 'nullable|exists:users,id',
            'controller_3_id' => 'nullable|exists:users,id',
            'controller_4_id' => 'nullable|exists:users,id',
            'controller_5_id' => 'nullable|exists:users,id',
            'approver_1_id' => $isDraft ? 'nullable|exists:users,id' : 'required_without_all:approver_2_id,approver_3_id|nullable|exists:users,id',
            'approver_2_id' => 'nullable|exists:users,id',
            'approver_3_id' => 'nullable|exists:users,id',
        ], [
            'approver_1_id.required_without_all' => 'Please select at least one approver.',
            'file.mimes' => 'The file must be a PDF document.',
            'file.max' => 'The file size must not exceed 10MB.',
        ]);

        $originalStatus = $rule->status;

        if ($request->filled('file_path_temp')) {
            try {
                $tempPath = $request->input('file_path_temp');

                if (Storage::disk('public')->exists($tempPath)) {
                    if ($rule->file_path) {
                        Storage::disk('public')->delete($rule->file_path);
                    }

                    $newPath = 'company-rules-files/'.basename($tempPath);
                    Storage::disk('public')->move($tempPath, $newPath);
                    $validatedData['file_path'] = $newPath;
                }
            } catch (\Exception $e) {
                \Log::error('File upload error: '.$e->getMessage());

                return back()->with('error', 'Error uploading file: '.$e->getMessage());
            }
        } else {
            if (isset($validatedData['file'])) {
                unset($validatedData['file']);
            }
            $validatedData['file_path'] = $rule->file_path;
        }

        $validatedData['effective_date'] = ! empty($validatedData['effective_date']) ? $validatedData['effective_date'] : null;

        if ($isDraft) {
            $validatedData['status'] = 'Draft';
        } else {
            if ($rule->status === 'Draft' || $rule->status === 'Send Back') {
                if ($rule->status === 'Send Back' && $rule->sent_back_by_id) {
                    if ($rule->sent_back_by_id == $rule->controller_5_id) {
                        $validatedData['status'] = 'Pending Approval 5';
                    } elseif ($rule->sent_back_by_id == $rule->controller_4_id) {
                        $validatedData['status'] = 'Pending Approval 4';
                    } elseif ($rule->sent_back_by_id == $rule->controller_3_id) {
                        $validatedData['status'] = 'Pending Approval 3';
                    } elseif ($rule->sent_back_by_id == $rule->controller_2_id) {
                        $validatedData['status'] = 'Pending Approval 2';
                    } else {
                        $validatedData['status'] = 'Pending Approval 1';
                    }
                    $validatedData['sent_back_by_id'] = null;
                    $validatedData['reason'] = null;
                } else {
                    if ($request->input('approver_1_id')) {
                        $validatedData['status'] = 'Pending Approval 1';
                    } elseif ($request->input('approver_2_id')) {
                        $validatedData['status'] = 'Pending Approval 2';
                    } elseif ($request->input('approver_3_id')) {
                        $validatedData['status'] = 'Pending Approval 3';
                    } else {
                        $validatedData['status'] = 'Approved';
                    }
                }
            }
        }

        $originalAttributes = $rule->getOriginal();

        $rule->update($validatedData);

        $newAttributes = $rule->getAttributes();

        $changes = array_diff_assoc($newAttributes, $originalAttributes);

        if (!empty($changes)) {
            $rule->logActivity('Updated', $changes);
        }

        if (!$isDraft && ($originalStatus === 'Draft' || $originalStatus === 'Send Back')) {
            $rule->logActivity('Resubmitted for Approval');
        }

        if (! $isDraft) {
            if ($originalStatus === 'Draft' || $originalStatus === 'Send Back') {
                $controllerId = null;

                if ($validatedData['status'] === 'Pending Approval 1' && $rule->controller_1_id) {
                    $controllerId = $rule->controller_1_id;
                } elseif ($validatedData['status'] === 'Pending Approval 2' && $rule->controller_2_id) {
                    $controllerId = $rule->controller_2_id;
                } elseif ($validatedData['status'] === 'Pending Approval 3' && $rule->controller_3_id) {
                    $controllerId = $rule->controller_3_id;
                } elseif ($validatedData['status'] === 'Pending Approval 4' && $rule->controller_4_id) {
                    $controllerId = $rule->controller_4_id;
                } elseif ($validatedData['status'] === 'Pending Approval 5' && $rule->controller_5_id) {
                    $controllerId = $rule->controller_5_id;
                }

                if ($controllerId) {
                    $controller = User::find($controllerId);
                    if ($controller) {
                        $message = "The document '{$rule->document_name}' has been revised and requires your approval.";
                        $actionUrl = route('approvals.index');
                        $controller->notify(new DocumentStatusUpdated($rule->id, $message, $actionUrl));
                    }
                }
            }
        }

        if ($isDraft) {
            return redirect()->route('company-rules.index')->with('success', 'Draft has been updated.');
        } else {
            return redirect()->route('company-rules.index')->with('success', 'Document have been updated and resubmitted!');
        }
    }

    public function destroy(CompanyRule $rule)
    {
        $rule->logActivity('Deleted');

        if ($rule->file_path) {
            Storage::disk('public')->delete($rule->file_path);
        }
        $rule->delete();

        return redirect()->route('company-rules.index')->with('success', 'Document deleted successfully.');
    }

    public function serveFile($id)
    {
        $rule = CompanyRule::findOrFail($id);

        if (! $rule->file_path || ! Storage::disk('public')->exists($rule->file_path)) {
            abort(404, 'PDF file not found in storage.');
        }

        $path = Storage::disk('public')->path($rule->file_path);

        // Default behavior for non-obsolete files
        return response()->file($path);
    }

    public function getRuleDataAsJson(CompanyRule $rule)
    {
        return response()->json($rule);
    }

    public function getLogsJson(CompanyRule $rule)
    {
        $logs = $rule->logs()->with('user')->paginate(10);

        return response()->json($logs);
    }

    public function getNextNumber(Request $request)
    {
        $category = $request->input('category');
        if (!$category) {
            return response()->json(['error' => 'Category is required'], 400);
        }

        // Extract prefix from "XX Name: PX"
        $parts = explode(': ', $category);
        $prefix = trim(end($parts));

        if (empty($prefix)) {
            return response()->json(['error' => 'Invalid category format'], 400);
        }

        // Correctly find the latest rule by extracting and sorting the numeric part
        $latestRule = CompanyRule::where('number', 'like', $prefix . '-%')
            ->orderByRaw("CAST(NULLIF(regexp_replace(number, '[^0-9]', '', 'g'), '') AS INTEGER) DESC")
            ->first();

        $nextNumber = 1;
        if ($latestRule) {
            // Extracts the numeric part like '001' from 'OT-001'
            $numericPart = (int) preg_replace('/[^0-9]/', '', $latestRule->number);
            $nextNumber = $numericPart + 1;
        }

        return response()->json([
            'next_number' => $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT)
        ]);
    }
}
