<?php

namespace App\Http\Controllers;

use App\Chat;
use App\ChatTweet;
use App\Arachive;
use App\DM;
use App\Setting;
use App\Library\TwitterBot;
use App\Schedule;
use App\Tweet;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
         $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        $settings = Setting::findOrNew(1);

        $twitter = new TwitterBot();

        $requestMethod = 'GET';
        $url = 'https://api.twitter.com/1.1/trends/place.json';
        $getfield = '?id=' . 1;

        $trends = json_decode($twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest());

        if (isset($trends->errors) && !empty($trends->errors)){
            $trends = null;
        }
        $chat_tweet = ChatTweet::get()->last();
        $archive_tweet = Tweet::orderBy('created_at','desc')->take(1)->first();

        $numbers = new \stdClass();
        $numbers->chat = Chat::count();
        $numbers->dm = DM::count();
        $numbers->schedules = Schedule::count();
        $numbers->archive = Arachive::count();
        $numbers->chat_tweets = ChatTweet::count();

        return view('home',compact('trends','chat_tweet','archive_tweet','top_chat_chart','numbers','settings'));
    }
}
