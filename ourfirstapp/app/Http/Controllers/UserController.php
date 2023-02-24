<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function storeAvatar(Request $request){
        $request-> validate([
            'avatar'=>'required|image|max:3000'
        ]);
        $user = auth()->user();
        $filename = $user->id . '-' . uniqid()  . '.jpg';
        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');

        Storage::put('public/avatars'.$filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != "/fallback-avatar.jpg"){
            Storage::delete("/storage/","public/", $oldAvatar);
        }

        return back()->with('success', 'nice avatar');
    }
    public function showAvatarForm(){
        return view('avatar-form');
    }

    public function profile(User $user_page){
//        return $user_page->posts()->get();
        return view('profile-posts', ['avatar'=> $user_page->avatar ,'username'=>$user_page-> username, 'posts'=>$user_page->posts()->get(), 'PostCount'=>$user_page->posts()->count()]);
    }

//change password validation min to at least 8 later to add basic security
    public function register(Request $request){
        $incomingFields = $request -> validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users','username')],
            'email' => ['required','email' ,Rule::unique('users','email')],
            'password' => ['required', 'min:1', 'max:20', 'confirmed']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Thanks for creating account');
    }

    public function login(Request $request){
        $incomingFields = $request -> validate([
            'loginusername' => ['required'],
            'loginpassword' => ['required']
        ]);

        if (auth()->attempt([
            'username'=>$incomingFields['loginusername'],
            'password'=>$incomingFields['loginpassword'],])){
            $request -> session() -> regenerate();
            return redirect('/')->with('success', 'logged in');
        }
        else{
            return redirect('/')->with('failure', 'Invalid login');

        }
    }

    public function showCorrectHomepage()
    {
        if(auth()->check()){
            return view('homepage-feed');
        }
        else{
            return view('homepage');
        }
    }

    public function logout(){
            auth()->logout();
            return redirect('/')->with('success', 'logged out');
    }
}
