<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CommentController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function store(Request $request,Post $post){
        $data = $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        $post->comments()->create([ 
            'comment' => $data['comment'],
            'user_id' => auth()->id(),
        ]);

    }

    public function destroy(Comment $comment){

        if(auth()->user()->id != $comment->user_id){
            abort(403, 'Unauthorized action.');
        }

        $postId = $comment->post_id;
        $comment->delete();


        return redirect('/posts/' . $postId);

    }


}