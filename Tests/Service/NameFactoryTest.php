<?php

declare(strict_types=1);

namespace MauticPlugin\GranamCzechVocativeBundle\Tests\Service;

use Granam\CzechVocative\CzechName;
use MauticPlugin\GranamCzechVocativeBundle\Service\NameFactory;
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
