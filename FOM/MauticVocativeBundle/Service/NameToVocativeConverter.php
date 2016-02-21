<?php
namespace MauticPlugin\MauticVocativeBundle\Service;

use MauticPlugin\MauticVocativeBundle\Vocative\Name;

class NameToVocativeConverter
{
    /**
     * @var Name
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
            $this->name = new Name();
        }

        return $this->name;
    }
}