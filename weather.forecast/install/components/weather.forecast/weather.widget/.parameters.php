<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
    "PARAMETERS" => [
        "UNITS" => [
            "PARENT" => "BASE",
            "NAME" => "Единицы измерения",
            "TYPE" => "LIST",
            "VALUES" => [
                "metric" => "Цельсий",
                "imperial" => "Фаренгейт"
            ],
            "DEFAULT" => "metric",
        ],
        "CACHE_TIME" => [
            "DEFAULT" => 600,
        ],
    ],
];
