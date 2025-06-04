<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Weather\Forecast\WeatherProviderFactory;

class WeatherForecastComponent extends CBitrixComponent
{
    protected $provider;

    public function onPrepareComponentParams($arParams)
    {
        $moduleId = 'weather.forecast';

        $arParams["API_KEY"] = trim($arParams["API_KEY"] ?? Option::get($moduleId, 'api_key'));
        $arParams["CITY"] = trim($arParams["CITY"] ?? Option::get($moduleId, 'city', 'Moscow'));
        $arParams["UNITS"] = in_array($arParams["UNITS"], ['metric', 'imperial'])
            ? $arParams["UNITS"]
            : Option::get($moduleId, 'units', 'metric');
        $arParams["API_PROVIDER"] = $arParams["API_PROVIDER"] ?: Option::get($moduleId, 'provider', 'openweathermap');

        $arParams["CACHE_TIME"] = (int)$arParams["CACHE_TIME"];
        if ($arParams["CACHE_TIME"] <= 0) {
            $arParams["CACHE_TIME"] = 600;
        }
        return $arParams;
    }

    public function executeComponent()
    {
        if (!Loader::includeModule("weather.forecast")) {
            ShowError("Модуль weather.forecast не установлен");
            return;
        }

        if (empty($this->arParams["API_KEY"])) {
            ShowError("Не указан API ключ");
            return;
        }

        $cacheTime = $this->arParams["CACHE_TIME"];
        $cacheId = md5(serialize([
            $this->arParams["API_KEY"],
            $this->arParams["CITY"],
            $this->arParams["UNITS"],
            $this->arParams["API_PROVIDER"]
        ]));

        if ($this->startResultCache($cacheTime, $cacheId, 'weather_forecast')) {
            try {
                $this->provider = WeatherProviderFactory::create(
                    $this->arParams["API_PROVIDER"],
                    $this->arParams["API_KEY"],
                    $this->arParams["CITY"],
                    $this->arParams["UNITS"]
                );

                $currentWeather = $this->provider->getCurrentWeather();

                if (!$currentWeather) {
                    throw new \Exception("Ошибка получения данных погоды");
                }

                $this->arResult["CURRENT_WEATHER"] = $currentWeather;
                $this->includeComponentTemplate();

            } catch (\Throwable $e) {
                $this->AbortResultCache();
                ShowError($e->getMessage());
                return;
            }
        }
    }
}
