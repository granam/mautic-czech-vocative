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
     * Searching for [name|vocative] (enclosed by native or encoded square brackets,
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
        if (preg_match_all(
                '~(?<toReplace>(?:\[|%5B)(?<prefixToKeep>\[*)\s*(?<toVocative>[^\[\]]+[^\s])\s*(?<suffixToKeep>\]*)\|vocative(?:\]|%5D))~u',
                $value,
                $matches
            ) > 0
        ) {
            foreach ($matches['toReplace'] as $index => $toReplace) {
                $toVocative = $matches['toVocative'][$index];
                $prefixToKeep = $matches['prefixToKeep'][$index];
                $suffixToKeep = $matches['suffixToKeep'][$index];
                $value = str_replace($toReplace, $prefixToKeep . $this->toVocative($toVocative) . $suffixToKeep, $value);
            }
        }

        return $value;
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
