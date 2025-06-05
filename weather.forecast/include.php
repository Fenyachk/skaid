<?php

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(
    "weather.forecast",
    [
        "Weather\\Forecast\\OpenWeatherProvider"     => "lib/OpenWeatherProvider.php",
        "Weather\\Forecast\\WeatherApiProvider"      => "lib/WeatherApiProvider.php",
        "Weather\\Forecast\\WeatherProviderFactory"  => "lib/WeatherProviderFactory.php",
        "Weather\\Forecast\\WeatherProviderInterface"=> "lib/WeatherProviderInterface.php",
    ]
);
