<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PositionController extends Controller
{
    // Menampilkan daftar semua jabatan.
    public function index(): View
    {
        $positions = Position::with('holder')->orderBy('position_title')->get();

        return view('positions.index', compact('positions'));
    }

    // Menampilkan formulir untuk membuat jabatan baru.
    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('positions.create', compact('users'));
    }

    // Menyimpan jabatan baru ke database.
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'position_title' => 'required|string|max:255|unique:positions',
            'holder_id' => 'required|exists:users,id',
        ]);

        Position::create($request->all());

        return redirect()->route('positions.index')->with('success', 'Position created successfully.');
    }

    // Menampilkan formulir untuk mengedit jabatan.
    public function edit(Position $position): View
    {
        $users = User::orderBy('name')->get();

        return view('positions.edit', compact('position', 'users'));
    }

    // Memperbarui jabatan yang ada di database.
    public function update(Request $request, Position $position): RedirectResponse
    {
        $request->validate([
            'position_title' => 'required|string|max:255|unique:positions,position_title,'.$position->id,
            'holder_id' => 'required|exists:users,id',
        ]);

        $position->update($request->all());

        return redirect()->route('positions.index')->with('success', 'Position updated successfully.');
    }

    // Menghapus jabatan dari database.
    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()->route('positions.index')->with('success', 'Position deleted successfully.');
    }
}
