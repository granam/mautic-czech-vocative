<?php
namespace MauticPlugin\MauticVocativeBundle\Tests;

use MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName;
use MauticPlugin\MauticVocativeBundle\MauticVocativeBundle;
use PHPUnit\Framework\TestCase;

class MauticVocativeBundleTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_boot_bundle()
    {
        $bundle = new MauticVocativeBundle();
        self::assertFalse(class_exists(CzechName::class));
        $bundle->boot();
        self::assertTrue(class_exists(CzechName::class));
    }
}
