<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $city = $request->input('city');
        $apiKey = env('OPENWEATHER_API_KEY'); //API key

        $client = new Client();
        $url = "http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric"; //metric values

        try {
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody(), true);

            $weatherData = [ //gets required data
                'city' => $data['name'],
                'temperature' => $data['main']['temp'],
                'weather_condition' => $data['weather'][0]['description'],
                'wind_speed' => $data['wind']['speed'],
                'wind_direction' => $data['wind']['deg'],
                'atm_pressure' => $data['main']['pressure'],
                'humidity' => $data['main']['humidity']
                //the probability of rain is available for paid api keys only
            ];

            //finding direction, since owm api provides degrees
            if ($data['wind']['deg'] > 337.5) $weatherData['wind_direction'] = 'Northerly';
            elseif ($data['wind']['deg']>337.5) $weatherData['wind_direction'] =  'Northerly';
            elseif ($data['wind']['deg']>292.5) $weatherData['wind_direction'] =  'North Westerly';
            elseif ($data['wind']['deg']>247.5) $weatherData['wind_direction'] =  'Westerly';
            elseif ($data['wind']['deg']>202.5) $weatherData['wind_direction'] =  'South Westerly';
            elseif ($data['wind']['deg']>157.5) $weatherData['wind_direction'] =  'Southerly';
            elseif ($data['wind']['deg']>122.5) $weatherData['wind_direction'] =  'South Easterly';
            elseif ($data['wind']['deg']>67.5) $weatherData['wind_direction'] =  'Easterly';
            elseif ($data['wind']['deg']>22.5) $weatherData['wind_direction'] =  'North Easterly';
            else $weatherData['wind_direction'] =  'Northerly';

            return response()->json($weatherData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'City not found or invalid request.'], 404);
        }
    }
}
