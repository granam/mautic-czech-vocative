<?php
namespace MauticPlugin\MauticVocativeBundle\Service;

use MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName;

class NameToVocativeConverter
{
    /**
     * @var CzechName
     */
    private $name;

    /**
     * @param string $name
     * @return string
     */
    public function convert($name)
    {
        return $this->getName()->vocative($name);
    }

    private function getName()
    {
        if (!isset($this->name)) {
            $this->name = new CzechName();
        }

        return $this->name;
    }
}