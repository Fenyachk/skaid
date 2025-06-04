<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Weather\Forecast\WeatherProviderFactory;

class WeatherForecastComponent extends CBitrixComponent
{
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
        if (!Loader::includeModule('weather.forecast')) {
            ShowError('Модуль weather.forecast не подключен');
            return;
        }

        if (empty($this->arParams['API_KEY'])) {
            ShowError('Не указан API ключ');
            return;
        }

        $cacheTime = $this->arParams['CACHE_TIME'];
        $cacheId = md5(serialize([
            $this->arParams['API_KEY'],
            $this->arParams['CITY'],
            $this->arParams['UNITS'],
            $this->arParams['API_PROVIDER'],
        ]));

        if ($this->startResultCache($cacheTime, $cacheId)) {
            try {
                $provider = WeatherProviderFactory::create(
                    $this->arParams['API_PROVIDER'],
                    $this->arParams['API_KEY'],
                    $this->arParams['CITY'],
                    $this->arParams['UNITS']
                );
                $this->arResult['CURRENT_WEATHER'] = $provider->getCurrentWeather();

                //а здесь можно будет что угодно из манифеста вызвать соотв.
                //$this->arResult['NEXT_YEAR'] = $provider->getLastYearWeekly();


                //на стадии реализации
                //$this->arResult['TOMORROW_WEATHER'] = $provider->getTomorrowWeather();

                if (!$this->arResult['CURRENT_WEATHER']) {
                    throw new \Exception('Ошибка получения текущих данных погоды');
                }

                $this->includeComponentTemplate();
            } catch (\Throwable $e) {
                $this->AbortResultCache();
                ShowError($e->getMessage());
            }
        }
    }
}
