<?php
namespace MauticPlugin\MauticVocativeBundle\Service;

use MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName;

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
     * @return string
     */
    public function toVocative($name)
    {
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
            '~(?<toReplace>',
            [
                '(?:\[|%5B)', // opening square bracket, native or URL encoded
                [
                    '[\[\s]*', // redundant opening square brackets and white spaces are removed
                    '(?<toVocative>[^\[\]]+[^\s\[\]])', // without any square bracket, ending to non-square-bracket and non-white-space
                    '[\]\s]*', // redundant closing square brackets and white spaces are removed
                    '\|vocative' // with trailing pipe and keyword "vocative"
                ],
                '(?:\]|%5D)', // closing square bracket, native or URL encoded
            ],
            ')~u' // u = UTF-8
        ];
        if (preg_match_all(
                $this->implode($regexpParts),
                $value,
                $matches
            ) > 0
        ) {
            foreach ($matches['toReplace'] as $index => $toReplace) {
                $toVocative = $matches['toVocative'][$index];
                $value = str_replace($toReplace, $this->toVocative($toVocative), $value);
            }
        }

        return $value;
    }

    private function implode(array $values)
    {
        return implode(
            array_map(
                function ($value) {
                    if (is_array($value)) {
                        return $this->implode($value);
                    }

                    return $value;
                },
                $values
            )
        );
    }

    private function removeEmptyShortCodes($value)
    {
        if (preg_match_all('~(?<toRemove>(?:\[|%5B)\s*(?<toKeep>.*[^\s]?)\s*\|vocative(?:\]|%5D))~u', $value, $matches) > 0) {
            foreach ($matches['toRemove'] as $index => $toReplace) {
                $toKeep = $matches['toKeep'][$index];
                $value = str_replace($toReplace, $toKeep, $value);
            }
        }

        return $value;
    }
}
