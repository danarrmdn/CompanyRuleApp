<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('emp_id', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'emp_id' => ['required', 'string', 'max:255', 'unique:users,emp_id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'integer'],
            'grade' => ['nullable', 'integer'],
            'department' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer'],
            'section' => ['nullable', 'string', 'max:255'],
            'section_id' => ['nullable', 'integer'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'emp_id' => $request->emp_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles' => $request->roles,
            'grade' => $request->grade,
            'department' => $request->department,
            'department_id' => $request->department_id,
            'section' => $request->section,
            'section_id' => $request->section_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'required|integer',
            'grade' => 'nullable|integer',
            'department' => 'nullable|string|max:255',
            'department_id' => 'nullable|integer',
            'section' => 'nullable|string|max:255',
            'section_id' => 'nullable|integer',
        ]);

        $user->update($validatedData);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Reset the password for a user.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        $newPassword = 'shokubai';

        $user->update([
            'password' => Hash::make($newPassword),
            'password_change_at' => null,
        ]);

        return redirect()->route('users.index')->with('success', "Password for {$user->name} has been reset to the default password.");
    }}
