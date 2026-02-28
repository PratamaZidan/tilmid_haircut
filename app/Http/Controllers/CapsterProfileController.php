<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CapsterProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user(); // capster login
        return view('capster.profile', compact('user'));
    }

    public function updateProfile(Request $r)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $r->validate([
            'name' => ['required','string','max:100'],
            'username' => [
                'required','string','max:50',
                Rule::unique('users','username')->ignore($user->id),
            ],
            'phone' => ['required','string','max:30'],
            'instagram' => ['nullable','string','max:80'],
        ]);

        $data['instagram'] = $data['instagram'] ? ltrim(trim($data['instagram']), '@') : null;
        
        $user->update($data);

        return back()->with('ok_profile', 'Profile berhasil disimpan.');
    }

    public function updatePassword(Request $r)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $r->validate([
            'password' => ['nullable','string','min:6','confirmed'],
        ]);

        // kalau kosong, tidak ngapa-ngapain
        if (empty($data['password'])) {
            return back()->with('ok_password', 'Password tidak diubah.');
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('ok_password', 'Password berhasil diupdate.');
    }
}