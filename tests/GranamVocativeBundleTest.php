<?php

declare(strict_types=1);

namespace MauticPlugin\GranamVocativeBundle\Tests;

use MauticPlugin\GranamVocativeBundle\CzechVocative\CzechName;
use MauticPlugin\GranamVocativeBundle\GranamVocativeBundle;
use PHPUnit\Framework\TestCase;

class GranamVocativeBundleTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_boot_bundle()
    {
        $bundle = new GranamVocativeBundle();
        self::assertFalse(
            class_exists(CzechName::class),
            sprintf("Class '%s' is not expected to be already loaded", CzechName::class)
        );
        $bundle->boot();
        self::assertTrue(
            class_exists(CzechName::class),
            sprintf("Class '%s' should be loaded", CzechName::class)
        );
    }
}
