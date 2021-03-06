<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use Psy\Exception\RuntimeException;
use Twitter;
use App\Follower;
use Illuminate\Support\Facades\Input;
class AuthController extends Controller
{
    /**
    *Redirect the user to the Twitter authentication page.
    *
    * @return Response to the callback url
    */
    public function provider()
    {

        $sign_in_twitter = true;
        $force_login = false;
        // reset config in case of login
        Twitter::reconfig(['token' => '', 'secret' => '']);
        $token = Twitter::getRequestToken(route('twitter.callback')); //get request tokens

        if (isset($token['oauth_token_secret'])) {
            $url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);
            //set oauth token and secret token in session
            session()->put('oauth_state', 'start');
            session()->put('oauth_request_token', $token['oauth_token']);
            session()->put('oauth_request_token_secret', $token['oauth_token_secret']);

            return redirect($url);//return to callback url with auth info
        }

        return redirect()->route('twitter.error');
    }

    /**
    * Fetch the user information from callback url and login the user.
    *
    * @return Response to home
    */
    public function callback()
    {
          //check for outh token
        if (session()->has('oauth_request_token')) {
            $request_token = [
                'token'  => session()->get('oauth_request_token'),
                'secret' => session()->get('oauth_request_token_secret'),
            ]; //get oauth token and secret token from session

            Twitter::reconfig($request_token);

            $oauth_verifier = false;

            if (Input::has('oauth_verifier')) {
                $oauth_verifier = Input::get('oauth_verifier');
                // getAccessToken() will reset the token for you
                $token = Twitter::getAccessToken($oauth_verifier);
            }
            // error if can't find aouth token secret
            if (!isset($token['oauth_token_secret'])) {
                return redirect()->route('twitter.error');
            }

            $credentials = Twitter::getCredentials(['format' => 'array']); //get credential info of user

            if (is_array($credentials) && !isset($credentials->error)) {
                // find or create new user
                $user= $this->findUser($credentials);
                // login user via Auth
                Auth::login($user);
                // save aacess token in session
                session()->put('access_token', $token);

                return redirect('/home');
            }
            return redirect()->route('twitter.error');
        }
    }


    /**
    * Return user if exists; create and return if doesn't
    *
    * @param $user contain twitter info user(name,handle,prifile photo,twitter id)
    * @return User
    */
    private function findUser($user)
    {
        //check user already exist
        $authUser = User::where('twitter_id', $user['id'])->first();
        if ($authUser) {
            return $authUser;
        }
        //if not then create a new
        $newUser= User::create([
            'name' => $user['name'],
            'handle' => $user['screen_name'],
            'twitter_id' => $user['id'],
            'avatar' => $user['profile_image_url_https'],
        ]);
        //create followers of new user
        $this->createFollowers($newUser);
        return $newUser;
    }

    /**
    * get followers of created user and insert into database
    * @param   $newUser information of created user
    * @return  void
    */
    private function createFollowers($newUser)
    {
        $cursor=-1;
        $count=0;
        while ($cursor !=0 && $count!=15) {
            $followers = Twitter::getFollowers(['screen_name' => $newUser->handle,
            'count' => 200, 'cursor' => $cursor, 'format' => 'array']);

            foreach ($followers['users'] as $follower) {
                Follower::create([
                    'user_id' => $newUser->id,
                    'name' => $follower['name'],
                    'screen_name' => $follower['screen_name'],
                ]);
            }
            $cursor=$followers['next_cursor'];
            $count++;
        }
        return;
    }

    /**
    * this function logout user from session
    * @return response to login page
    */
    public function logout()
    {
        session()->forget('access_token');
        Auth::logout();
        return redirect('/');
    }

}
