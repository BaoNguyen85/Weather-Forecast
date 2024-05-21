<?php

namespace App\Http\Controllers;

use App\Mail\WeatherUpdate;
use App\Models\History;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class WeatherController extends Controller
{
    public function getWeather(Request $request){
        $data = $request->all();
        
        $city = $request->input('city', null);
    
        $currentResponse = Http::get('https://api.weatherapi.com/v1/current.json', [
            'key' => '43d0bf2cf0ab452e9ba180827241704',
            'q' => $city,
            'aqi' => 'no'
        ]);
    
        $forecastResponse = Http::get('https://api.weatherapi.com/v1/forecast.json', [
            'key' => '43d0bf2cf0ab452e9ba180827241704',
            'q' => $city,
            'days' => 14
        ]);
    
        $currentWeather = $currentResponse->json();
        $forecastWeather = $forecastResponse->json();
        if(isset($currentWeather['location']['name'])){
            $history = new History();
            $history->name = $currentWeather['location']['name'];
            $history->location_localtime = $currentWeather['location']['localtime'];
            $history->temp = $currentWeather['current']['temp_c'];
            $history->wind = $currentWeather['current']['wind_mph'];
            $history->humidity = $currentWeather['current']['humidity'];
            $history->time_now = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
            $history->save();

            $oneDayAgo = Carbon::now()->subDay();
            History::where('time_now', '<', $oneDayAgo)->delete();
        }

        $all_history = History::orderby('id', 'DESC')->get();

        return view('home', compact('currentWeather', 'forecastWeather','all_history'));
    }
    public function subscribe(Request $request){
        $data = $request->all();
        $email = $data['email'];
        $location = $data['location'];

        $existingUser = UserModel::where('email', $email)->first();

        if ($existingUser) {
            $existingLocation = UserModel::where('email', $email)->where('location', $location)->first();
            if ($existingLocation) {
                Session::put('message','This location is already subscribed.');
                return Redirect::to('/');
            }
        }

        $user = new UserModel();
        $user->email = $data['email'];
        $user->location = $data['location'];
        $user->daily = $data['daily'];
        $user->save();

        $title_mail = "Dự báo thời tiết"; 
        $email = $request->input('email');
        $location = $request->input('location');

        $currentResponse = Http::get('https://api.weatherapi.com/v1/current.json', [
            'key' => '43d0bf2cf0ab452e9ba180827241704',
            'q' => $location,
            'aqi' => 'no'
        ]);

        $currentWeather = $currentResponse->json();

        if(isset($currentWeather['location']['name'])){

        $locationName = $currentWeather['location']['name'];
        $localTime = $currentWeather['location']['localtime'];
        $temperature = $currentWeather['current']['temp_c'];
        $windSpeed = $currentWeather['current']['wind_mph'];
        $humidity = $currentWeather['current']['humidity'];

        $emailContent = '
            <div style="background: #5372F0; display: flex; justify-content: between; align-items: center; color: white; border-radius: 5px">
                <div style="padding: 1vh">
                    <h2 style="padding: 1vh 0; font-weight: bold;">'.$locationName.' ('.$localTime.')</h2>
                    <p style="color: white">Temperature: '.$temperature.'°C</p>
                    <p style="color: white">Wind: '.$windSpeed.' M/S</p>
                    <p style="color: white">Humidity: '.$humidity.'%</p>
                </div>
            </div>
        ';

        }else{
            $emailContent = '
            <div style="background: #5372F0; display: flex; justify-content: between; align-items: center; color: white; border-radius: 5px">
                <div style="padding: 1vh">
                    <h2 style="padding: 1vh 0; font-weight: bold;">Location Not Found</h2>
                </div>
            </div>
        ';
        }

        $unsubscribeLink = route('unsubscribe', ['email' => $email, 'location' => $location]);
        $emailContent .= '<p><a href="' . $unsubscribeLink . '">Click here</a> to unsubscribe from daily weather updates.</p>';

        Mail::send([], [], function(Message $message) use ($title_mail, $email, $emailContent){
            $message->to($email)->subject($title_mail);
            $message->from('abuidang75@gmail.com', 'Weather Forecas'); // Thay thế bằng email và tên của bạn
            $message->html($emailContent);
        });

        return Redirect::to('/');
    }
    public function unsubscribe($email, $location){
        $user = UserModel::where('email', $email)->where('location', $location)->first();
        if ($user) {
            $user->delete();
        }
    }
}
