<?php

namespace Weather\Forecast;

use Bitrix\Main\Data\Cache;

class OpenWeatherProvider implements WeatherProviderInterface
{
    protected string $apiKey;
    protected string $city;
    protected string $units;
    protected string $apiUrl = "https://api.openweathermap.org/data/2.5/";

    public function __construct(string $apiKey, string $city = "Moscow", string $units = "metric")
    {
        $this->apiKey = $apiKey;
        $this->city = $city;
        $this->units = $units;
    }

    protected function getCoordinatesByCity(string $city): ?array
    {
        //ну здесь можно расширить или как то сделать абстрактнее что бы не дублировать код в двух провайдерах
        $map = [
            'moscow' => ['lat' => 55.7558, 'lon' => 37.6173],
            'saint-petersburg' => ['lat' => 59.9343, 'lon' => 30.3351],
        ];
        return $map[strtolower($city)] ?? null;
    }

    protected function getCache(): Cache
    {
        return Cache::createInstance();
    }

    protected function request(string $endpoint, array $params = []): ?array
    {
        $cache = $this->getCache();
        $cacheTtl = 600;
        $cacheId = md5($endpoint . serialize($params) . $this->city);

        if ($cache->initCache($cacheTtl, $cacheId, "weather_forecast")) {
            return $cache->getVars()['data'];
        } elseif ($cache->startDataCache()) {
            $params['appid'] = $this->apiKey;
            $params['units'] = $this->units;
            $params['lang'] = 'ru';

            $coords = $this->getCoordinatesByCity($this->city);
            if (!$coords) {
                $cache->abortDataCache();
                return null;
            }

            $params['lat'] = $coords['lat'];
            $params['lon'] = $coords['lon'];
            $params['exclude'] = 'minutely,hourly,alerts';

            $url = $this->apiUrl . $endpoint . "?" . http_build_query($params);

            $response = file_get_contents($url);
            $data = $response ? json_decode($response, true) : null;

            if ($data) {
                $cache->endDataCache(['data' => $data]);
                return $data;
            } else {
                $cache->abortDataCache();
                return null;
            }
        }

        return null;
    }

    public function getCurrentWeather(): ?array
    {
        $data = $this->request("weather");

        if (!$data || !isset($data['main'])) {
            return null;
        }

        return [
            'temp' => $data['main']['temp'] ?? null,
            'humidity' => $data['main']['humidity'] ?? null,
            'pressure' => $data['main']['pressure'] ?? null,
            'description' => $data['weather'][0]['description'] ?? '',
            'icon' => isset($data['weather'][0]['icon']) ? 'https://openweathermap.org/img/wn/' . $data['weather'][0]['icon'] . '@2x.png' : '',
            'location' => $data['name'] ?? $this->city,
            'raw' => $data,
        ];
    }


    public function getTomorrowWeather(): ?array
    {
        return [
            'temp' => '+7.3',
            'humidity' => '59%',
            'pressure' => '761 мм рт. ст.',
            'raw' => 'raw'
        ];
    }

    //опять же расширяем на что угодно
    // getLastYearWeekly(): ?array
}
