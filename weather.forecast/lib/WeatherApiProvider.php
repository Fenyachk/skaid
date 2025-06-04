<?php

namespace Weather\Forecast;

use Bitrix\Main\Data\Cache;

class WeatherApiProvider implements WeatherProviderInterface
{
    protected string $apiKey;
    protected string $city;
    protected string $apiUrl = "http://api.weatherapi.com/v1/";

    public function __construct(string $apiKey, string $city = "Moscow")
    {
        $this->apiKey = $apiKey;
        $this->city = $city;
    }

    protected function getCoordinatesByCity(string $city): ?array
    {
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
            $params['key'] = $this->apiKey;

            $coords = $this->getCoordinatesByCity($this->city);
            if (!$coords) {
                $cache->abortDataCache();
                return null;
            }

            $params['q'] = $coords['lat'] . ',' . $coords['lon'];
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
        $data = $this->request("current.json");

        if (!$data || !isset($data['current'])) {
            return null;
        }

        return [
            'temp' => $data['current']['temp_c'] ?? null,
            'humidity' => $data['current']['humidity'] ?? null,
            'pressure' => $data['current']['pressure_mb'] ?? null,
            'description' => $data['current']['condition']['text'] ?? '',
            'icon' => !empty($data['current']['condition']['icon'])
                ? 'https:' . $data['current']['condition']['icon']
                : '',
            'location' => $data['location']['name'] ?? $this->city,
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
