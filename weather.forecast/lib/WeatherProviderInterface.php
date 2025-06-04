<?php

namespace Weather\Forecast;

interface WeatherProviderInterface
{
    public function getCurrentWeather(): ?array;

    public function getTomorrowWeather(): ?array;

    //здесь можно заманифестить в принципе что угодно
    //getLastYearWeekly()
}
