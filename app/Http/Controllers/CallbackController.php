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
        $response = $this->discordRequest('https://discord.com/api/oauth2/token', [
            'client_id' => env('OAUTH_DISCORD_CLIENT_ID'),
            'client_secret' => env('OAUTH_DISCORD_CLIENT_SECRET'),
            "grant_type" => "authorization_code",
            'code' => request()->get('code'),
            'redirect_uri' => env('OAUTH_DISCORD_CALLBACK_URI'),
        ]);

        $userInfo = $this->discordRequest('https://discord.com/api/users/@me', [
            'token' => $response->access_token,
        ]);

        if(($user = User::where('email', $userInfo->email)->first()) === null){
            $user = new User;
            $user->name = $userInfo->username;
            $user->email = $userInfo->email;
            $user->email_verified_at = now();
            $user->password = Hash::make(Str::random(100));
            $user->remember_token = Str::random(10);
            $user->save();
        }

        Auth::login($user);

        return redirect('/member');
    }

    private function discordRequest($url, $post) {

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if(!isset($post['token'])){
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }else{
            $headers[] = 'Authorization: Bearer '.$post['token'];
        }

        $headers[] = 'Accept: application/json';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        return json_decode($response);
    }
}
