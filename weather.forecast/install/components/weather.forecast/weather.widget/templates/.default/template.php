<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

//если нужна текущая погода
$weather = $arResult["CURRENT_WEATHER"];


//если нужна погода на следующий день
//$weather = $arResult["TOMORROW_WEATHER"];


$temp = $weather["temp"] ?? "—";
$humidity = $weather["humidity"] ?? "—";
$pressureHpa = $weather["pressure"] ?? null;
$location = $weather["location"] ?? ($arParams["CITY"] ?? "Город");

if ($pressureHpa !== null) {
    $pressure = round($pressureHpa * 0.75006375541921);
} else {
    $pressure = "—";
}
?>

<h3>Прогноз погоды в <?= htmlspecialcharsbx($location) ?> </h3>

<?php if (!empty($weather['icon'])): ?>
    <p><img src="<?= htmlspecialcharsbx($weather['icon']) ?>" alt="Иконка погоды"></p>
<?php endif; ?>
<p>🌡 Температура: <?= htmlspecialcharsbx($temp) ?> <?= ($arParams["UNITS"] === "imperial" ? "°F" : "°C") ?></p>
<p>💧 Влажность: <?= htmlspecialcharsbx($humidity) ?>%</p>
<p>🌀 Давление: <?= htmlspecialcharsbx($pressure) ?> мм рт. ст.</p>
<?php if (!empty($weather['raw']['current']['condition']['text'])): ?>
    <p>🌤 Условия: <?= htmlspecialcharsbx($weather['raw']['current']['condition']['text']) ?></p>
<?php endif; ?>
