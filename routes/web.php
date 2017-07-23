    <?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth','AuthController@provider');
Route::get('/callback/twitter','AuthController@callback');
Route::get('home/{id}', 'AuthController@timeline')->name('home');
// Route::post('sendPDF', 'SendTweets@send');
Route::post('sendPDF',function(){
    return $tweets= Twitter::getUserTimeline(['screen_name' => Auth::user()->id, 'count' => 10, 'format' => 'array']);
});

Route::POST('searchFollowers',function(){
    if(Request::ajax()){
        $followerName=request('search');
        $followerResult=App\Follower::where('name','like',"%$followerName%")
        ->where('user_id',Auth::user()->id)
        ->get();

        return view('followerlist',compact(['followerResult']));

    }
});
