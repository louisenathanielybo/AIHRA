<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'firstName' => 'nullable|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->employeeNum . ',employeeNum',
            'dob' => 'nullable|date',
            'sex' => 'nullable|in:Male,Female',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $updateData = [];
        
        if ($request->firstName) $updateData['firstName'] = $request->firstName;
        if ($request->lastName) $updateData['lastName'] = $request->lastName;
        $updateData['email'] = $request->email;
        
        if ($request->dob) {
            $updateData['dob'] = $request->dob;
            // Calculate age from date of birth
            $dob = new \DateTime($request->dob);
            $today = new \DateTime();
            $age = $today->diff($dob)->y;
            // Only update age if it's a valid value (greater than 0)
            if ($age > 0 && $age <= 120) {
                $updateData['age'] = $age;
            }
        }
        
        if ($request->sex) $updateData['sex'] = $request->sex;

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $updateData['profile_picture'] = $filename;
        }

        if (isset($updateData['email']) && $updateData['email'] !== $user->email) {
            $updateData['email_verified_at'] = null;
        }

        // Use DB update to bypass any model events that might interfere
        \DB::table('users')
            ->where('employeeNum', $user->employeeNum)
            ->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Update the user's about section.
     */
    public function updateAbout(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'about' => 'nullable|string|max:1000'
        ]);

        $user->about = $request->about;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'About section updated successfully'
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed'
        ]);

        $user = $request->user();
        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
