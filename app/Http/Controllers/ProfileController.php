<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(User $user)
    {
        return view('profiles.index', compact('user'));
    }




public function edit(User $user)
{
    /** @var \App\Models\User $authUser */
    $authUser = Auth::user();

    if ($authUser->id !== $user->id) {
        abort(403, 'Unauthorized action.');
    }

    return view('profiles.edit', compact('user'));
}



    public function update(Request $request, User $user){
        if (Auth::user()->id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => '|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'profile_image' => 'image|nullable|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

          if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            $imagePath = $request->file('profile_image')->store('profile', 'public');
            $data['profile_image'] = $imagePath;
        }

        $user->update($data);

        return redirect("/profile/{$user->id}");

    }



}
