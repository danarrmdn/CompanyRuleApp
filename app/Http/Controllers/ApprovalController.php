<?php

namespace App\Http\Controllers;

use App\Jobs\SendApprovalNotification;
use App\Models\CompanyRule;
use App\Models\User;
use App\Notifications\DocumentStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;
        $rules = CompanyRule::with('creator')
            ->where(function ($query) use ($userId) {
                $query->where('status', 'Pending Approval 1')->where('controller_1_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('status', 'Pending Approval 2')->where('controller_2_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('status', 'Pending Approval 3')->where('controller_3_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('status', 'Pending Approval 4')->where('controller_4_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('status', 'Pending Approval 5')->where('controller_5_id', $userId);
            })
            ->latest()
            ->paginate(10);

        return view('approvals.index', compact('rules'));
    }

    public function approve(CompanyRule $rule)
    {
        $approvalChain = ['controller_1_id', 'controller_2_id', 'controller_3_id', 'controller_4_id', 'controller_5_id'];
        $statusChain = [
            'controller_1_id' => 'Pending Approval 1',
            'controller_2_id' => 'Pending Approval 2',
            'controller_3_id' => 'Pending Approval 3',
            'controller_4_id' => 'Pending Approval 4',
            'controller_5_id' => 'Pending Approval 5',
        ];
        $currentField = array_search($rule->status, $statusChain);
        if ($currentField === false) {
            return redirect()->back()->with('error', 'Invalid document status.');
        }
        $currentIndex = array_search($currentField, $approvalChain);
        $nextStatus = 'Approved';
        $nextControllerId = null;

        for ($i = $currentIndex + 1; $i < count($approvalChain); $i++) {
            $nextField = $approvalChain[$i];
            if (! empty($rule->{$nextField})) {
                $nextStatus = $statusChain[$nextField];
                $nextControllerId = $rule->{$nextField};
                break;
            }
        }

        $rule->status = $nextStatus;
        $rule->save();

        $rule->logActivity("Approved. New status: {$nextStatus}");

        if ($nextStatus === 'Approved' && $rule->previous_version_id) {
            $previousRule = CompanyRule::find($rule->previous_version_id);
            if ($previousRule) {
                $previousRule->is_obsolete = true;
                $previousRule->save();
            }
        }

        $controller = auth()->user();
        $creator = $rule->creator;

        if ($creator) {
            Log::info('Attempting to notify creator. User ID: '.$creator->id);
            $creatorMessage = "Your document '{$rule->document_name}' was approved by {$controller->name}. New status: {$rule->status}.";
            $creatorActionUrl = route('company-rules.show', $rule->id);
            $creator->notify(new DocumentStatusUpdated($rule->id, $creatorMessage, $creatorActionUrl));
        } else {
            Log::warning('Could not find creator to notify for rule ID: '.$rule->id);
        }

        if ($nextControllerId) {
            Log::info('Attempting to notify next approver. User ID: '.$nextControllerId);
            $nextController = User::find($nextControllerId);
            if ($nextController) {
                $controllerMessage = "Document '{$rule->document_name}' has been approved by the previous controller and now requires your approval.";
                $controllerActionUrl = route('approvals.index');
                SendApprovalNotification::dispatch($nextController, $rule, $controllerMessage, $controllerActionUrl)->delay(now()->addSeconds(5));
            } else {
                Log::warning('Could not find next approver to notify for ID: '.$nextControllerId);
            }
        }

        return redirect()->route('approvals.index')->with('success', 'Document has been approved.');
    }

    public function reject(Request $request, CompanyRule $rule)
    {
        $validatedData = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $rule->status = 'Rejected';
        $rule->reason = $validatedData['reason'];
        $rule->save();

        $rule->logActivity('Rejected', ['reason' => $validatedData['reason']]);

        $creator = $rule->creator;
        $controller = auth()->user();
        if ($creator) {
            $message = "Your document '{$rule->document_name}' was rejected by {$controller->name}.";
            $actionUrl = route('company-rules.show', $rule->id);
            $creator->notify(new DocumentStatusUpdated($rule->id, $message, $actionUrl));
        }

        return redirect()->route('approvals.index')->with('success', 'Document has been rejected.');
    }

    public function sendBack(Request $request, CompanyRule $rule)
    {
        $validatedData = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $rule->status = 'Send Back';
        $rule->reason = $validatedData['reason'];
        $rule->sent_back_by_id = auth()->user()->id;
        $rule->save();

        $rule->logActivity('Sent Back for Revision', ['reason' => $validatedData['reason']]);

        $creator = $rule->creator;
        $controller = auth()->user();
        if ($creator) {
            $message = "Your document '{$rule->document_name}' was sent back by {$controller->name} for revision.";
            $actionUrl = route('company-rules.show', $rule->id);
            $creator->notify(new DocumentStatusUpdated($rule->id, $message, $actionUrl));
        }

        return redirect()->route('approvals.index')->with('success', 'Document has been sent back to creator.');
    }

    public static function getPendingCount()
    {
        if (! Auth::check()) {
            return 0;
        }
        $userId = auth()->user()->id;

        return CompanyRule::where(function ($query) use ($userId) {
            $query->where('status', 'Pending Approval 1')->where('controller_1_id', $userId);
        })
            ->orWhere(function ($query) use ($userId) {
                $query->where('status', 'Pending Approval 2')->where('controller_2_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('status', 'Pending Approval 3')->where('controller_3_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('status', 'Pending Approval 4')->where('controller_4_id', $userId);
            })
            ->orWhere(function ($query) use ($userId) {
                $query->where('status', 'Pending Approval 5')->where('controller_5_id', $userId);
            })
            ->count();
    }
}
