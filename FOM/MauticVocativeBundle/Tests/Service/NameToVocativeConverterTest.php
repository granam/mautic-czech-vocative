<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Service;

use MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter;

class NameToVocativeConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function I_can_convert_name_to_vocative()
    {
        $service = new NameToVocativeConverter();
        $this->assertEquals(
            $service->convert('karel'),
            'Karle'
        );
    }
}
