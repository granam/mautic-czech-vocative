<?php
namespace MauticPlugin\MauticVocativeBundle\Service\Helpers;

class NameToVocativeOptions
{
    /**
     * @var string
     */
    private $maleNameAlias;
    /**
     * @var string
     */
    private $femaleNameAlias;

    public static function createFromString($stringOptions)
    {
        $options = [];
        $stringOptions = trim($stringOptions);
        if ($stringOptions !== '') {
            $values = explode(',', $stringOptions);
            if (isset($values[0])) {
                $options['maleNameAlias'] = trim($values[0]);
            }
            if (isset($values[1])) {
                $options['femaleNameAlias'] = trim($values[1]);
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
    public function hasMaleNameAlias()
    {
        return isset($this->maleNameAlias);
    }

    /**
     * @return string
     */
    public function getMaleNameAlias()
    {
        return $this->maleNameAlias;
    }

    /**
     * @return bool
     */
    public function hasFemaleNameAlias()
    {
        return isset($this->femaleNameAlias);
    }

    /**
     * @return mixed
     */
    public function getFemaleNameAlias()
    {
        return $this->femaleNameAlias;
    }

}
