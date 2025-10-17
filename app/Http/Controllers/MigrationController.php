<?php

namespace App\Http\Controllers;

use App\Models\CompanyRule;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MigrationController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $controllers = Position::has('holder')->with('holder')->get();
        $approvers_list = User::orderBy('name')->get();
        $userDepartment = Auth::user()->department ?? 'N/A';

        return view('migration.create', [
            'controllers' => $controllers,
            'approvers_list' => $approvers_list,
            'userDepartment' => $userDepartment,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category' => 'required|string|max:255',
            'number' => 'required|string|max:255',
            'document_name' => 'required|string|max:255',
            'reason_of_revision' => 'required|string',
            'effective_date' => 'required|date',
            'file_path_temp' => 'required|string',
            'controller_1_id' => 'nullable|exists:users,id',
            'controller_2_id' => 'nullable|exists:users,id',
            'controller_3_id' => 'nullable|exists:users,id',
            'controller_4_id' => 'nullable|exists:users,id',
            'controller_5_id' => 'nullable|exists:users,id',
            'approver_1_id' => 'nullable|exists:users,id',
            'approver_2_id' => 'nullable|exists:users,id',
            'approver_3_id' => 'nullable|exists:users,id',
        ]);

        $creator = Auth::user();
        $creatorId = $creator->id;

        $filePath = null;
        if ($request->filled('file_path_temp')) {
            $tempPath = $request->input('file_path_temp');
            if (Storage::disk('public')->exists($tempPath)) {
                $newPath = 'company-rules-files/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
                $filePath = $newPath;
            }
        }

        $rule = new CompanyRule;

        $rule->fill($validatedData);
        $rule->effective_date = !empty($validatedData['effective_date']) ? $validatedData['effective_date'] : null;
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
        
        // Set status directly to Approved, ignoring any controllers
        $rule->status = 'Approved';
        
        $rule->save();
        $rule->logActivity('Created via Data Migration and Auto-Approved');

        return redirect()->route('company-rules.index')->with('success', 'Document has been migrated and approved successfully!');
    }
}
