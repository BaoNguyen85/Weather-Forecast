<?php

namespace App\Console\Commands;

use App\Models\UserModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SendDailyWeatherEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-weather-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily weather email to subscribers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = UserModel::all();
        foreach ($users as $user) {
            if($user->daily == 1){
                $this->sendWeatherEmail($user->email, $user->location);
            }
        }
    }
    private function sendWeatherEmail($email, $location)
    {
        $currentResponse = Http::get('https://api.weatherapi.com/v1/current.json', [
            'key' => 'YOUR_WEATHER_API_KEY',
            'q' => $location,
            'aqi' => 'no'
        ]);
    
        $currentWeather = $currentResponse->json();
        
        $locationName = $currentWeather['location']['name'];
        $localTime = $currentWeather['location']['localtime'];
        $temperature = $currentWeather['current']['temp_c'];
        $windSpeed = $currentWeather['current']['wind_mph'];
        $humidity = $currentWeather['current']['humidity'];
        $weatherIcon = $currentWeather['current']['condition']['icon'];
        $weatherText = $currentWeather['current']['condition']['text'];

        $emailContent = "
            <div class='text-white d-flex justify-content-between align-items-center rounded-1' style='background: #5372F0;'>
                <div class='px-4 py-3'>
                    <h5 class='py-2' style='font-weight: bold;'>$locationName ($localTime)</h5>
                    <p>Temperature: $temperature °C</p>
                    <p>Wind: $windSpeed M/S</p>
                    <p>Humidity: $humidity%</p>
                </div>
                <div class='d-flex flex-column justify-content-center align-items-center' style='padding-right: 10%'>
                    <img style='width: 8rem' src='$weatherIcon'>
                    <p>$weatherText</p>
                </div>
            </div>
        ";

        Mail::send([], [], function($message) use ($email, $emailContent) {
            $message->to($email)->subject("Daily Weather Forecast");
            $message->from('your@email.com', 'Your Name');
            $message->html($emailContent);
        });
    }
}
