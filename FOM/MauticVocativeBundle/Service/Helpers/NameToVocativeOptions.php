<?php
namespace MauticPlugin\MauticVocativeBundle\Service\Helpers;

class NameToVocativeOptions
{
    /**
     * @var string|null
     */
    private $maleAlias;
    /**
     * @var string|null
     */
    private $femaleAlias;

    public static function createFromString($stringOptions)
    {
        $options = [];
        $stringOptions = trim($stringOptions);
        if ($stringOptions !== '') {
            $values = explode(',', $stringOptions);
            if (isset($values[0])) {
                $value = trim($values[0]);
                if ($value !== '') {
                    $options['maleAlias'] = $value;
                }
            }
            if (isset($values[1])) {
                $value = trim($values[1]);
                if ($value !== '') {
                    $options['femaleAlias'] = $value;
                }
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
    public function hasMaleAlias()
    {
        return isset($this->maleAlias);
    }

    /**
     * @return string|null
     */
    public function getMaleAlias()
    {
        return $this->maleAlias;
    }

    /**
     * @return bool
     */
    public function hasFemaleAlias()
    {
        return isset($this->femaleAlias);
    }

    /**
     * @return string|null
     */
    public function getFemaleAlias()
    {
        return $this->femaleAlias;
    }

}
