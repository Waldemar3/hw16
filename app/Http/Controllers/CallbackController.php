<?php


namespace App\Http\Controllers;


use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CallbackController
{
    public function linkGenerate(){
        $url = 'https://discord.com/api/oauth2/authorize';

        $param = [
            'client_id' => env('OAUTH_DISCORD_CLIENT_ID'),
            'redirect_uri' => env('OAUTH_DISCORD_CALLBACK_URI'),
            'response_type' => 'code',
            'scope' => 'identify email',
        ];

        $url .= '?'.http_build_query($param);

        return view('login', ['url' => $url]);
    }

    public function callback()
    {
        $response = Http::asForm()->post('https://discord.com/api/oauth2/token', [
            'client_id' => env('OAUTH_DISCORD_CLIENT_ID'),
            'client_secret' => env('OAUTH_DISCORD_CLIENT_SECRET'),
            "grant_type" => "authorization_code",
            'code' => request()->get('code'),
            'redirect_uri' => env('OAUTH_DISCORD_CALLBACK_URI'),
        ]);

        $userInfo = Http::withHeaders([
            'Authorization' => 'Bearer '.$response['access_token']
        ])->get('https://discord.com/api/users/@me');

        if(($user = User::where('email', $userInfo['email'])->first()) === null){
            $user = new User;
            $user->name = $userInfo['username'];
            $user->email = $userInfo['email'];
            $user->email_verified_at = now();
            $user->password = Hash::make(Str::random(100));
            $user->remember_token = Str::random(10);
            $user->save();
        }

        Auth::login($user);

        return redirect('/member');
    }
}
