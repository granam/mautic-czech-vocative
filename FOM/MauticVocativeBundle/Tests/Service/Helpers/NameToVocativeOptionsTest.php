<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Service\Helpers;

use MauticPlugin\MauticVocativeBundle\Service\Helpers\NameToVocativeOptions;

class NameToVocativeOptionsTest extends \PHPUnit_Framework_TestCase
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
                        $this->assertInstanceOf(NameToVocativeOptions::class, $options);
                        $this->assertSame($maleAlias !== '', $options->hasMaleAlias());
                        $this->assertSame($maleAlias ?: null, $options->getMaleAlias());
                        $this->assertSame($femaleAlias !== '', $options->hasFemaleAlias());
                        $this->assertSame($femaleAlias ?: null, $options->getFemaleAlias());
                    }
                }
            }
        }
    }
}
