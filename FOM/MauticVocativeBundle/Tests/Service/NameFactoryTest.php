<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Service;

use MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName;
use MauticPlugin\MauticVocativeBundle\Service\NameFactory;
use PHPUnit\Framework\TestCase;

class NameFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_create_name_by_factory()
    {
        $czechName = NameFactory::createCzechName();
        self::assertInstanceOf(CzechName::class, $czechName);
    }
}