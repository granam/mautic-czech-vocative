<?php

declare(strict_types=1);

namespace MauticPlugin\GranamCzechVocativeBundle\Tests\Service\Helpers;

use MauticPlugin\GranamCzechVocativeBundle\Service\Helpers\NameToVocativeOptions;
use PHPUnit\Framework\TestCase;

class NameToVocativeOptionsTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_create_options_from_string()
    {
        foreach (['', 'foo'] as $maleAlias) {
            foreach (['', 'bar'] as $femaleAlias) {
                foreach (['', " \n\t ", "\n\n\n"] as $space1) {
                    foreach (['', " \n\t ", "\n\n\n"] as $space2) {
                        $string = "{$space1}{$maleAlias}{$space2},{$space1}{$femaleAlias}{$space2}";
                        $options = NameToVocativeOptions::createFromString($string);
                        self::assertInstanceOf(NameToVocativeOptions::class, $options);
                        self::assertSame($maleAlias !== '', $options->hasMaleAlias());
                        self::assertSame($maleAlias ?: null, $options->getMaleAlias());
                        self::assertSame($femaleAlias !== '', $options->hasFemaleAlias());
                        self::assertSame($femaleAlias ?: null, $options->getFemaleAlias());
                    }
                }
            }
        }
    }
}
