<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use newrelic\DistributedTracePayload;
use PhpParser\Error;
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

    public function delete(Post $post){
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'post is deleted');
    }

    public function showEditForm(Post $post){
        return view('edit-post', ['post'=>$post]);
    }

    public function actuallyUpdate(Post $post, Request $request)
    {
        $incomingFields = $request -> validate([
           'title'=>'required',
            'body'=>'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $post -> update($incomingFields);

        return  back() -> with('success','Post successfully updated');
    }
}
