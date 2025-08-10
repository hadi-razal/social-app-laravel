<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $post = Post::with(['user'])->latest()->get();
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    
    public function store(Request $request)
    {

        $data = $request->validate([
            'image_path' => 'required|image',
            'caption' => 'nullable|string|max:255',
        ]);

        $imagePath = $request->file('image_path')->store('images', 'public');


        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image_path' => $imagePath,
        ]);

        return redirect('/profile/' . auth()->user()->id );

    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
       
        return view('posts.show', compact('post'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        
        if(auth()->user()->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('posts.edit', compact('post'));
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {

        if(auth()->user()->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'caption' => 'nullable|string|max:255',
        ]);

        $post->update($data);

        return redirect('/profile/' . auth()->user()->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if(auth()->user()->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        Storage::disk('public')->delete($post->image_path);

       
        $post->delete();


        return redirect('/profile/' . auth()->user()->id)->with('success', 'Post deleted successfully.');

    }
}
