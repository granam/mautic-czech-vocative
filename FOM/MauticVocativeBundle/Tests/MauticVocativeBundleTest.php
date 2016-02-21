<?php
namespace MauticPlugin\MauticVocativeBundle\Tests;

use MauticPlugin\MauticVocativeBundle\MauticVocativeBundle;

class MauticVocativeBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function I_can_boot_bundle()
    {
        $bundle = new MauticVocativeBundle();
        $this->assertFalse(class_exists('\MauticPlugin\MauticVocativeBundle\Vocative\Name'));
        $bundle->boot();
        $this->assertTrue(class_exists('\MauticPlugin\MauticVocativeBundle\Vocative\Name'));
    }
}