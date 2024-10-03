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
                'temperature' => $data['main']['temp'],
                'weather_condition' => $data['weather'][0]['description'],
                'city' => $data['name'],
                'country' => $data['sys']['country']
            ];

            return response()->json($weatherData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'City not found or invalid request.'], 404);
        }
    }
}
