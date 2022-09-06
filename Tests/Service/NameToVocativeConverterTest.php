<?php

declare(strict_types=1);

namespace MauticPlugin\GranamCzechVocativeBundle\Tests\Service;

use Granam\CzechVocative\CzechName;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\GranamCzechVocativeBundle\Service\NameToVocativeConverter;
use MauticPlugin\GranamCzechVocativeBundle\Tests\GranamTestWithMockery;
use Mockery\MockInterface;

class NameToVocativeConverterTest extends GranamTestWithMockery
{
    /**
     * @test
     */
    public function I_can_convert_name_to_vocative()
    {
        $service = new NameToVocativeConverter($this->createCzechName($name = 'karel', $inVocative = 'foo'));
        self::assertEquals($service->toVocative($name), $inVocative);
    }

    /**
     * @param string $expectedName
     * @param string $vocative
     * @param bool $isMale
     * @return CzechName|MockInterface
     */
    private function createCzechName(string $expectedName, string $vocative, bool $isMale = true): CzechName
    {
        $czechName = $this->mockery(CzechName::class);
        if ($vocative !== '') {
            $czechName->shouldReceive('vocative')
                ->atLeast()->once()
                ->with(html_entity_decode($expectedName))
                ->andReturn($vocative);
            $czechName->shouldReceive('isMale')
                ->andReturn($isMale);
        } else {
            $czechName->shouldReceive('vocative')
                ->never();
        }

        return $czechName;
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
     * @param string $genderOrEmpty
     * @param string $emptyNameAlias
     */
    private function checkEmailContentConversion(
        $toConvert,
        $conversionShouldHappen,
        $nameOrMaleAlias = '',
        $femaleAlias = '',
        $genderOrEmpty = '',
        $emptyNameAlias = ''
    )
    {
        $expectedName = $nameOrMaleAlias;
        if ($genderOrEmpty === 'female') {
            $expectedName = $femaleAlias;
        } elseif ($genderOrEmpty === 'empty') {
            $expectedName = $emptyNameAlias;
        }
        $nameConverter = new NameToVocativeConverter(
            $this->createCzechName(
                $expectedName,
                $conversionShouldHappen
                    ? 'Příliš žluťkoučký kůň úpěl ďábelské ódy'
                    : ''
                ,
                \in_array($genderOrEmpty, ['', 'male'], true)
            )
        );

        $spaces = ['', "\n\t ", " \t\n\t "]; // to test white space combinations
        foreach ($spaces as $space1) {
            foreach ($spaces as $space2) {
                $optionsCombination = [];
                if (\in_array($genderOrEmpty, ['male', 'female', 'empty'], true)) {
                    $optionsCombination[] = "{$space1}({$space2}{$nameOrMaleAlias}{$space1},{$space2}{$femaleAlias}{$space1},{$space2}{$emptyNameAlias}{$space1}){$space2}";
                    if (\in_array($genderOrEmpty, ['male', 'female'], true)) {
                        $optionsCombination[] = "{$space1}({$space2}{$nameOrMaleAlias}{$space1},{$space2}{$femaleAlias}{$space1}){$space2}"; // without empty name alias at all
                    }
                    if ($genderOrEmpty === 'male') {
                        $optionsCombination[] = "{$space1}({$space2}{$nameOrMaleAlias}{$space1}){$space2}"; // without female alias at all
                    }
                } else {
                    $optionsCombination[] = "({$space1}{$space2})";
                    $optionsCombination[] = '';
                }
                foreach ($optionsCombination as $options) {
                    foreach (\range(1, 3) as $openingBracketCount) {
                        foreach (\range(1, 3) as $closingBracketCount) {
                            $wrappedByShortCode = "foo{$space1}"; // leading junk
                            $wrappedByShortCode .= \str_repeat('[', $openingBracketCount); // 1+ opening bracket
                            $wrappedByShortCode .= "{$space2}{$toConvert}{$space1}"; // name to vocative itself
                            $wrappedByShortCode .= "|vocative{$space2}{$options}{$space1}"; // modifier with optional options
                            $wrappedByShortCode .= \str_repeat(']', $closingBracketCount); // 1+ closing bracket
                            $wrappedByShortCode .= "{$space2}bar"; // trailing junk
                            $vocalizedString = '';
                            if ($conversionShouldHappen) {
                                $vocalizedString = 'Příliš žluťkoučký kůň úpěl ďábelské ódy';
                                if ($toConvert !== $nameOrMaleAlias && \html_entity_decode($toConvert) === $nameOrMaleAlias) {
                                    $vocalizedString = \htmlentities($vocalizedString);
                                }
                            }
                            $toReplace = "[{$space2}$toConvert{$space1}|vocative{$space2}{$options}{$space1}]";
                            self::assertSame(
                                [$toReplace => $vocalizedString],
                                $nameConverter->findAndReplace($wrappedByShortCode),
                                "Expected\n'{$toReplace}' => '{$vocalizedString}'\nparsed from\n'{$wrappedByShortCode}'"
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
    public function I_got_names_converted_even_it_is_single_character()
    {
        $this->checkEmailContentConversion('a', true /* conversion should be called */, 'a');
    }

    /**
     * @test
     */
    public function I_do_not_trigger_conversion_by_empty_value_without_empty_string_alias()
    {
        $this->checkEmailContentConversion('', false /* conversion should not be called */);
    }

    /**
     * @test
     */
    public function I_get_vocalized_alias_for_empty_value_if_given()
    {
        $this->checkEmailContentConversion(
            "\r\n \t \n",
            true /* conversion should be called */,
            'I am boy',
            'I am girl',
            'empty',
            'I am Ms. No one'
        );
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
        self::assertDoesNotMatchRegularExpression('~[[:alpha:]]$~u', $withTrailingNonLetters);
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

    /**
     * @test
     */
    public function I_can_vocalize_name_enclosed_by_brackets()
    {
        $this->checkEmailContentConversion('[Venceslav]', true /* conversion should be called */, 'Venceslav');
    }

    /**
     * @test
     */
    public function I_got_vocalized_content_in_complex_string()
    {
        $rawContent = <<<HTML
<html lang="en">
<head>
	<title></title>
</head>
<body>
<p><a href="http://example.com/?%5B[[Alois]]|vocative%5D">XSS for free!&nbsp;</a></p><!-- wrong -->
<div><a href="http://example.com/?%5BKarel|vocative%5D">Click on me&nbsp;</a></div>
<p>[First Name karel]</p> <!-- missing shortcode -->
<span>[[First Name]|vocative]</span><!-- correct -->
<p>[ [ First Name karel ] | vocative ]</p>
<p>[fitnesačka|vocative(androiďačka)]</p>
<p>[|vocative]</p>
<div>[    | vocative ( Alone in the dark, Alice ) ]</div>
</body>
</html>
HTML;
        $nameConverter = new NameToVocativeConverter($this->createSimpleCzechName($replacement = 'foo'));
        $event = new EmailSendEvent();
        $event->setContent($rawContent);
        $tokens = $nameConverter->findAndReplace($rawContent);
        $event->addTokens($tokens);
        self::assertSame(<<<HTML
<html lang="en">
<head>
	<title></title>
</head>
<body>
<p><a href="http://example.com/?%5B[[Alois]]|vocative%5D">XSS for free!&nbsp;</a></p><!-- wrong -->
<div><a href="http://example.com/?{$replacement}">Click on me&nbsp;</a></div>
<p>[First Name karel]</p> <!-- missing shortcode -->
<span>{$replacement}</span><!-- correct -->
<p>{$replacement}</p>
<p>{$replacement}</p>
<p></p>
<div></div>
</body>
</html>
HTML
            ,
            $event->getContent(true /* with tokens replaced */)
        );
    }

    /**
     * @param string $toVocative
     * @return CzechName|\Mockery\MockInterface
     */
    private function createSimpleCzechName(string $toVocative = 'foo')
    {
        /** @var CzechName|\Mockery\MockInterface $czechName */
        $czechName = $this->mockery(CzechName::class);
        $czechName->shouldReceive('isMale')
            ->andReturn(true);
        $czechName->shouldReceive('vocative')
            ->andReturn($toVocative);

        return $czechName;
    }

}
