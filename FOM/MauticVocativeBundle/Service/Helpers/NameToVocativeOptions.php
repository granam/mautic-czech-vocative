<?php
namespace MauticPlugin\MauticVocativeBundle\Service\Helpers;

class NameToVocativeOptions
{
    /**
     * @var string
     */
    private $maleNameReplacement;
    /**
     * @var string
     */
    private $femaleNameReplacement;

    public static function createFromString($stringOptions)
    {
        $options = [];
        $stringOptions = trim($stringOptions);
        if ($stringOptions !== '') {
            $values = explode(',', $stringOptions);
            if (isset($values[0])) {
                $options['maleNameReplacement'] = trim($values[0]);
            }
            if (isset($values[1])) {
                $options['femaleNameReplacement'] = trim($values[1]);
            }
        }

        return new static($options);
    }

    public function __construct(array $values)
    {
        foreach ($values as $name => $value) {
            if (!property_exists($this, $name)) {
                throw new Exceptions\UnknownOption('Got unknown option of name ' . var_export($name, true));
            }
            $this->$name = $value;
        }
    }

    /**
     * @return bool
     */
    public function hasMaleNameReplacement()
    {
        return isset($this->maleNameReplacement);
    }

    /**
     * @return string
     */
    public function getMaleNameReplacement()
    {
        return $this->maleNameReplacement;
    }

    /**
     * @return bool
     */
    public function hasFemaleNameReplacement()
    {
        return isset($this->femaleNameReplacement);
    }

    /**
     * @return mixed
     */
    public function getFemaleNameReplacement()
    {
        return $this->femaleNameReplacement;
    }

}
