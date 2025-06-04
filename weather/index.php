<?
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
    $APPLICATION->SetTitle("Title");
?>
<?$APPLICATION->IncludeComponent(
    "weather.forecast:weather.widget",
    "",
    [
    ],
    false
);?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>