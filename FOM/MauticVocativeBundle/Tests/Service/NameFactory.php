<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Service;

use CzechVocative\CzechName;

class NameFactory
{
    /**
     * @return CzechName
     */
    public static function createCzechName()
    {
        return new CzechName();
    }
}
