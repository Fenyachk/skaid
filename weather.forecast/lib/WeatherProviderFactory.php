<?php

namespace Weather\Forecast;

class WeatherProviderFactory
{
    public static function create(string $apiProvider, string $apiKey, string $city = 'Moscow', string $units = 'metric'): WeatherProviderInterface
    {

        switch (strtolower($apiProvider)) {
            case 'openweathermap':
                return new OpenWeatherProvider($apiKey, $city, $units);

            case 'weatherapi':
                return new WeatherApiProvider($apiKey, $city);

            default:
                throw new \InvalidArgumentException("Unknown weather API provider: $apiProvider");
        }
    }
}
