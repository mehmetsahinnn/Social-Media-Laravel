<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Psy\Util\Str;

class PostController extends Controller
{
    public function showCreateForm(){
        return view("create-post");
    }
    public function storeNewPost(Request $request){
        $incomingFields = $request->validate([
            'title' => 'required',
            'body'  => 'required'
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth() -> id();

        $newPost=  Post::create($incomingFields);

        return redirect("/post/{$newPost->id}")->with('success', 'You made it!!!');
    }

    public function viewSinglePost(Post $post){
        $post['body'] = \Illuminate\Support\Str::markdown($post->body);
        return view('single-post', ['post'=>$post]);
    }

}
