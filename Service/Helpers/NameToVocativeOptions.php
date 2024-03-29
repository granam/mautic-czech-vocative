<?php
namespace MauticPlugin\GranamCzechVocativeBundle\Service\Helpers;

class NameToVocativeOptions
{
    /**
     * @var string|null
     */
    private $maleAlias = null;
    /**
     * @var string|null
     */
    private $femaleAlias = null;
    /**
     * @var string|null
     */
    private $emptyNameAlias = null;

    public static function createFromString(string $stringOptions): NameToVocativeOptions
    {
        $options = [];
        $stringOptions = trim($stringOptions);
        if ($stringOptions !== '') {
            $values = explode(',', $stringOptions);
            if (array_key_exists(0, $values)) {
                $firstOption = trim($values[0]);
                if ($firstOption !== '') {
                    $options['maleAlias'] = $firstOption;
                }
            }
            if (array_key_exists(1, $values)) {
                $secondOption = trim($values[1]);
                if ($secondOption !== '') {
                    $options['femaleAlias'] = $secondOption;
                }
            }
            if (array_key_exists(2, $values)) {
                $thirdOption = trim($values[2]);
                if ($thirdOption !== '') {
                    $options['emptyNameAlias'] = $thirdOption;
                }
            }
        }

        return new static($options);
    }

    /**
     * @param array $values
     * @throws \MauticPlugin\GranamCzechVocativeBundle\Service\Helpers\Exceptions\UnknownOption
     */
    public function __construct(array $values)
    {
        foreach ($values as $name => $value) {
            switch ($name) {
                case 'maleAlias' :
                    $this->maleAlias = $value;
                    break;
                case 'femaleAlias' :
                    $this->femaleAlias = $value;
                    break;
                case 'emptyNameAlias' :
                    $this->emptyNameAlias = $value;
                    break;
                default :
                    throw new Exceptions\UnknownOption('Got unknown option of name ' . var_export($name, true));
            }
        }
    }

    /**
     * @return bool
     */
    public function hasMaleAlias(): bool
    {
        return $this->maleAlias !== null;
    }

    /**
     * @return string|null
     */
    public function getMaleAlias(): ?string
    {
        return $this->maleAlias;
    }

    /**
     * @return bool
     */
    public function hasFemaleAlias(): bool
    {
        return $this->femaleAlias !== null;
    }

    /**
     * @return string|null
     */
    public function getFemaleAlias(): ?string
    {
        return $this->femaleAlias;
    }

    /**
     * @return bool
     */
    public function hasEmptyNameAlias(): bool
    {
        return $this->emptyNameAlias !== null;
    }

    /**
     * @return null|string
     */
    public function getEmptyNameAlias(): ?string
    {
        return $this->emptyNameAlias;
    }

}
