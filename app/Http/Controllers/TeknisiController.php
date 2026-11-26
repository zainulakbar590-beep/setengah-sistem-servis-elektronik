<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class TeknisiController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403, 'Hanya admin yang dapat mengelola akun teknisi.');

        $teknisis = User::role('teknisi')
            ->latest()
            ->get(['id', 'name', 'email', 'created_at']);

        return Inertia::render('Teknisi/Index', [
            'teknisis' => $teknisis,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403, 'Hanya admin yang dapat menambahkan teknisi.');

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $teknisi = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (method_exists($teknisi, 'assignRole')) {
            $teknisi->assignRole('teknisi');
        }

        return back()->with('success', 'Akun teknisi berhasil ditambahkan.');
    }
}