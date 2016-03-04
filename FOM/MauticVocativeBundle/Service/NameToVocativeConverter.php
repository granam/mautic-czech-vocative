<?php
namespace MauticPlugin\MauticVocativeBundle\Service;

use MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName;
use MauticPlugin\MauticVocativeBundle\Service\Helpers\NameToVocativeOptions;
use MauticPlugin\MauticVocativeBundle\Service\Helpers\RecursiveImplode;

class NameToVocativeConverter
{
    /**
     * @var CzechName
     */
    private $name;

    public function __construct(CzechName $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     * @param NameToVocativeOptions|null $options
     * @return string
     */
    public function toVocative($name, NameToVocativeOptions $options = null)
    {
        if ($options !== null) {
            if ($options->hasMaleNameReplacement() && $this->name->isMale($name)) {
                $name = $options->getMaleNameReplacement();
            } else if ($options->hasFemaleNameReplacement() && !$this->name->isMale($name)) {
                $name = $options->getFemaleNameReplacement();
            }
        }

        return $this->name->vocative($name);
    }

    /**
     * Searching for [name|vocative] (enclosed by native or URL encoded square brackets,
     * with name optionally enclosed by [square brackets] as well to match email preview
     * @param string $value
     * @return string
     */
    public function findAndReplace($value)
    {
        $value = $this->vocalizeByShortCodes($value);
        $value = $this->removeEmptyShortCodes($value);

        return $value;
    }

    private function vocalizeByShortCodes($value)
    {
        $regexpParts = [
            '(?<toReplace>',
            [
                '(?:\[|%5B)', // opening bracket, native or URL encoded
                [
                    '[\[\s]*', // redundant opening brackets (TODO ?) and white spaces are removed
                    '(?<toVocative>[^\[\]]+[^\s\[\]])', // without any bracket, ending by non-bracket and non-white-space
                    '[\]\s]*', // redundant closing brackets and white spaces are removed
                    '\|vocative\s*', // with trailing (relatively to name) pipe and keyword "vocative"
                    '(?:\(+', // options are enclosed in parenthesis
                    [
                        '(?<options>[^\)\]]+)?' // optional values of options with fail-save by excluded right bracket in case of missing closing parenthesis
                    ],
                    '\)*)?', // last parenthesis can be (but should not be) omitted, whole options are optional
                    '\s*', // trailing white spaces are thrown away
                ],
                '(?:\]|%5D)', // closing bracket, native or URL encoded
            ],
            ')'
        ];
        $regexp = '~' . RecursiveImplode::implode($regexpParts) . '~u'; // u = UTF-8
        if (preg_match_all(
                $regexp,
                $value,
                $matches
            ) > 0
        ) {
            foreach ($matches['toReplace'] as $index => $toReplace) {
                $toVocative = $matches['toVocative'][$index];
                $stringOptions = $matches['options'][$index];
                $value = str_replace(
                    $toReplace,
                    $this->toVocative($toVocative, NameToVocativeOptions::createFromString($stringOptions)),
                    $value
                );
            }
        }

        return $value;
    }

    private function removeEmptyShortCodes($value)
    {
        if (preg_match_all('~(?<toRemove>(?:\[|%5B)\s*(?<toKeep>.*[^\s]?)\s*\|vocative[^\]]*(?:\]|%5D))~u', $value, $matches) > 0) {
            foreach ($matches['toRemove'] as $index => $toReplace) {
                $toKeep = $matches['toKeep'][$index];
                $value = str_replace($toReplace, $toKeep, $value);
            }
        }

        return $value;
    }
}
