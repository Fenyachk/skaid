<?php
\Bitrix\Main\Loader::registerAutoLoadClasses(
    "weather.forecast",
    [
        "WeatherForecast\\WeatherApi" => "lib/WeatherApi.php",
    ]
);
?>
