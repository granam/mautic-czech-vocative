<?php
namespace MauticPlugin\GranamCzechVocativeBundle\Service;

use Granam\CzechVocative\CzechName;

class NameFactory
{
    public const CZECH_NAME_SERVICE_ID = 'plugin.vocative.czech_name';

    public static function createCzechName(): CzechName
    {
        return new CzechName();
    }
}
