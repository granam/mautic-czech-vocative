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
     * @param bool $isMale
     * @return CzechName
     */
    private function createCzechName($expectedName, $inVocative, $isMale = true)
    {
        $czechName = $this->mockery('\MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName');
        if ($inVocative !== false) {
            $czechName->shouldReceive('vocative')
                ->with(html_entity_decode($expectedName))
                ->andReturn($inVocative);
            $czechName->shouldReceive('isMale')
                ->andReturn($isMale);
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
     * @param string $nameOrMaleAlias
     * @param string $femaleAlias
     * @param string $gender
     */
    private function checkEmailContentConversion(
        $toConvert,
        $conversionShouldHappen,
        $nameOrMaleAlias = '',
        $femaleAlias = '',
        $gender = ''
    )
    {
        $spaces = ['', "\n\t ", " \t\n\t "]; // to test white space combinations
        foreach ($spaces as $space1) {
            foreach ($spaces as $space2) {
                if (in_array($gender, ['male', 'female'])) {
                    $optionsCombination = ["{$space1}({$space2}{$nameOrMaleAlias}{$space1},{$space2}{$femaleAlias}{$space1}){$space2}",];
                    if ($gender === 'male') {
                        $optionsCombination[] = "{$space1}({$space2}{$nameOrMaleAlias}{$space1}){$space2}"; // without female alias at all
                    }
                } else {
                    $optionsCombination = ["({$space1}{$space2})", ''];// with and without parenthesis
                }
                foreach ($optionsCombination as $options) {
                    foreach (range(1, 3) as $openingBracketCount) {
                        foreach (range(1, 3) as $closingBracketCount) {
                            $wrappedByShortCode = "foo{$space1}"; // leading junk
                            $wrappedByShortCode .= str_repeat('[', $openingBracketCount); // 1+ opening bracket
                            $wrappedByShortCode .= "{$space2}{$toConvert}{$space1}"; // name to vocative itself
                            $wrappedByShortCode .= "|vocative{$space2}{$options}{$space1}"; // modifier with optional options
                            $wrappedByShortCode .= str_repeat(']', $closingBracketCount); // 1+ closing bracket
                            $wrappedByShortCode .= "{$space2}bar"; // trailing junk
                            $nameConverter = new NameToVocativeConverter(
                                $this->createCzechName(
                                    $gender !== 'female' ? $nameOrMaleAlias : $femaleAlias,
                                    ($conversionShouldHappen
                                        ? 'Příliš žluťkoučký kůň úpěl ďábelské ódy'
                                        : false
                                    ),
                                    $gender !== 'female'
                                )
                            );
                            $vocalizedString = '';
                            if ($conversionShouldHappen) {
                                $vocalizedString = 'Příliš žluťkoučký kůň úpěl ďábelské ódy';
                                if ($toConvert !== $nameOrMaleAlias && html_entity_decode($toConvert) === $nameOrMaleAlias) {
                                    $vocalizedString = htmlentities($vocalizedString);
                                }
                            }
                            $this->assertSame(
                                'foo' . $space1 . str_repeat('[', $openingBracketCount - 1)
                                . $vocalizedString
                                . str_repeat(']', $closingBracketCount - 1) . $space2 . 'bar',
                                $nameConverter->findAndReplace($wrappedByShortCode)
                            );
                        }
                    }
                }
            }
        }
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
    public function I_can_vocalize_even_complex_name()
    {
        $this->checkEmailContentConversion("\n\t\t Maria \n Gloria \t Galia Valia ", true /* conversion should be called */, "Maria \n Gloria \t Galia Valia");
    }

    /**
     * @test
     */
    public function I_can_replace_name_by_vocalized_gender_dependent_alias()
    {
        $this->checkEmailContentConversion(
            'Roman',
            true /* conversion should be called */,
            'Romulus', // male alias
            'She-wolf', // female alias
            'male'
        );
        $this->checkEmailContentConversion(
            'Roman',
            true /* conversion should be called */,
            'Romulus', // male alias
            'She-wolf', // female alias
            'female'
        );
    }

    /**
     * @test
     */
    public function I_can_vocalize_html_encoded_name()
    {
        $this->checkEmailContentConversion('androiď&aacute;k', true /* conversion should be called */, 'androiďák');
    }

}
