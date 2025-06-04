<?php
$module_id = 'weather.forecast';
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule($module_id);

if (!$USER->IsAdmin()) {
    return;
}

$showRightsTab = true;

$aTabs = [
    [
        'DIV' => 'edit1',
        'TAB' => GetMessage('WEATHER_FORECAST_TAB_SETTINGS', 'Настройки'),
        'TITLE' => GetMessage('WEATHER_FORECAST_TAB_SETTINGS', 'Настройки модуля'),
    ],
];

$tabControl = new CAdminTabControl("tabControl", $aTabs);

$provider = COption::GetOptionString($module_id, "provider", "openweather");
$apiKey = COption::GetOptionString($module_id, "api_key", "");
$defaultCity = COption::GetOptionString($module_id, "city", "moscow");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid()) {
    if (isset($_POST['api_key'])) {
        COption::SetOptionString($module_id, "api_key", $_POST["api_key"]);
    }
    if (isset($_POST['provider'])) {
        COption::SetOptionString($module_id, "provider", $_POST["provider"]);
    }
    if (isset($_POST['city'])) {
        COption::SetOptionString($module_id, "city", $_POST["city"]);
    }

    LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($module_id) . "&lang=" . LANGUAGE_ID);
}

$apiKey = COption::GetOptionString($module_id, "api_key", "");
$provider = COption::GetOptionString($module_id, "provider", "openweather");
$defaultCity = COption::GetOptionString($module_id, "city", "Moscow");

$tabControl->Begin();
?>

<form method="POST"
      action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($module_id) ?>&lang=<?= LANG ?>">
    <?= bitrix_sessid_post(); ?>
    <?php $tabControl->BeginNextTab(); ?>

    <tr>
        <td width="40%">Провайдер погоды:</td>
        <td width="60%">
            <select name="provider">
                <option value="openweathermap" <?= $provider === "openweathermap" ? "selected" : "" ?>>OpenWeathermap
                </option>
                <option value="weatherapi" <?= $provider === "weatherapi" ? "selected" : "" ?>>WeatherAPI</option>
            </select>
        </td>
    </tr>

    <tr>
        <td>API ключ:</td>
        <td><input type="text" name="api_key" value="<?= htmlspecialcharsbx($apiKey) ?>" size="50"></td>
    </tr>

    <tr>
        <td>Город по умолчанию:</td>
        <td>
            <select name="city">
                <option value="moscow" <?= ($defaultCity === "moscow") ? "selected" : "" ?>>Москва</option>
                <option value="saint-petersburg" <?= ($defaultCity === "saint-petersburg") ? "selected" : "" ?>>
                    Санкт-Петербург
                </option>
            </select>
        </td>
    </tr>


    <?php $tabControl->Buttons(); ?>
    <input type="submit" name="save" value="Сохранить" class="adm-btn-save">
    <input type="submit" name="apply" value="Применить">
    <input type="button" name="cancel" value="Отменить"
           onclick="window.location='<?= $APPLICATION->GetCurPage() ?>?lang=<?= LANG ?>'">
</form>

<?php
$tabControl->End();
?>
