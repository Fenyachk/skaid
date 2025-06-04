<?php

use Bitrix\Main\ModuleManager;

class weather_forecast extends CModule
{
    public $MODULE_ID = "weather.forecast";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME = "Weather Forecast";
    public $MODULE_DESCRIPTION = "Модуль для получения прогноза погоды";

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }

    public function InstallDB()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            ModuleManager::registerModule($this->MODULE_ID);
        }
        return true;
    }

    public function UnInstallDB()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            ModuleManager::unRegisterModule($this->MODULE_ID);
        }
        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION;
        if ($this->InstallDB()) {
            CopyDirFiles(
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/weather.forecast/install/components/weather.forecast",
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/weather.forecast",
                true,
                true
            );
        } else {
            $APPLICATION->ThrowException("Ошибка установки модуля");
            return false;
        }
        return true;
    }

    public function DoUninstall()
    {
        global $APPLICATION;
        if ($this->UnInstallDB()) {
            DeleteDirFilesEx("/bitrix/components/weather.forecast");
        } else {
            $APPLICATION->ThrowException("Ошибка удаления модуля");
            return false;
        }
        return true;
    }
}
