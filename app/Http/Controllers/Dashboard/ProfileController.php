<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locales;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('dashboard.profile.edit', [
            'user' => $user,
            'countries' => Countries::getNames(),
            'locales' => Locales::getNames(),
        ]);
    }

    public function update(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable','in:male,female'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
            'locale' => ['nullable', 'string', 'size:2'],
        ]);

        $user = $request->user();

        // Create profile if it does not exist
        $profile = $user->profile ?? $user->profile()->create(['user_id' => $user->id]);

        // Fill and save
        $profile->fill($validated)->save();

        return redirect()->route('dashboard.profile.edit')
                         ->with('success', 'Profile updated successfully!');
    }
}
