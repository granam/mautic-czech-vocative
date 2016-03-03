<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Service;

use MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName;
use MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter;

class NameToVocativeConverterTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @test
     */
    public function I_can_convert_name_to_vocative()
    {
        $service = new NameToVocativeConverter($this->createCzechName($name = 'karel', $inVocative = 'foo'));
        $this->assertEquals($service->toVocative($name), $inVocative);
    }

    /**
     * @param $expectedName
     * @param $inVocative
     * @return CzechName
     */
    private function createCzechName($expectedName, $inVocative)
    {
        $czechName = $this->mockery(CzechName::class);
        if ($inVocative !== false) {
            $czechName->shouldReceive('vocative')
                ->with($expectedName)
                ->andReturn($inVocative);
        } else {
            $czechName->shouldReceive('vocative')
                ->never();
        }

        return $czechName;
    }

    private function mockery($className)
    {
        return \Mockery::mock($className);
    }

    /**
     * @test
     */
    public function I_can_vocalize_value()
    {
        $this->checkEmailContentConversion('baz', true /* conversion should be called */, 'baz');
    }

    /**
     * @param string $toConvert
     * @param bool $conversionShouldHappen
     * @param string $toVocative
     */
    private function checkEmailContentConversion($toConvert, $conversionShouldHappen, $toVocative = '')
    {
        $wrappedByShortCode = "foo [$toConvert|vocative] bar";
        $nameConverter = new NameToVocativeConverter(
            $this->createCzechName($toVocative, ($conversionShouldHappen ? 'qux' : false))
        );
        $this->assertSame(
            'foo ' . ($conversionShouldHappen ? 'qux' : '') . ' bar',
            $nameConverter->findAndReplace($wrappedByShortCode)
        );
    }

    /**
     * @test
     */
    public function I_got_names_converted_even_if_wrapped_by_white_space()
    {
        $withWhiteSpaces = "\t\n baz  \t\n ";
        $this->checkEmailContentConversion($withWhiteSpaces, true /* conversion should be called */, trim($withWhiteSpaces));
    }

    /**
     * @test
     */
    public function I_do_not_trigger_conversion_by_empty_value()
    {
        $this->checkEmailContentConversion('', false /* conversion should not be called */);
    }

    /**
     * @test
     */
    public function I_got_removed_white_spaces_only_without_conversion_trigger()
    {
        $this->checkEmailContentConversion("\n\t\t    \n\t  ", false /* conversion should not be called */);
    }

    /**
     * @test
     */
    public function I_got_untouched_names_with_trailing_non_letters()
    {
        $withTrailingNonLetters = 'What ? !';
        $this->assertNotRegExp('~[[:alpha:]]$~u', $withTrailingNonLetters);
        $this->checkEmailContentConversion($withTrailingNonLetters, true /* conversion should be called */, $withTrailingNonLetters);
    }

    /**
     * @test
     */
    public function I_got_redundant_square_brackets_removed_as_well()
    {
        $this->checkEmailContentConversion('[doktor]', true /* conversion should be called */, 'doktor');
        $this->checkEmailContentConversion('doktor]', true /* conversion should be called */, 'doktor');
        $this->checkEmailContentConversion('[doktor', true /* conversion should be called */, 'doktor');
    }

    /**
     * @test
     */
    public function I_can_vocalize_even_complex_name()
    {
        $this->checkEmailContentConversion("\n\t\t Maria \n Gloria \t Galia Valia ", true /* conversion should be called */, "Maria \n Gloria \t Galia Valia");
    }
}
