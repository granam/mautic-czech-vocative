<?php
namespace MauticPlugin\GranamVocativeBundle\Service;

use MauticPlugin\GranamVocativeBundle\CzechVocative\CzechName;

class NameFactory
{
    public const CZECH_NAME_SERVICE_ID = 'plugin.vocative.czech_name';

    /**
     * @return CzechName
     */
    public static function createCzechName()
    {
        return new CzechName();
    }
}
