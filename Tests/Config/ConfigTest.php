<?php

declare(strict_types=1);

namespace MauticPlugin\GranamCzechVocativeBundle\Tests\Config;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @test
     */
    public function Classes_listed_in_config_exists()
    {
        $configFile = __DIR__ . '/../../Config/config.php';
        self::assertFileExists($configFile);
        $config = include $configFile;
        self::assertTrue(is_array($config));
        $this->checkClassesExistence($config);
    }

    private function checkClassesExistence(array $config)
    {
        foreach ($config as $key => $value) {
            if ($key === 'class') {
                self::assertTrue(class_exists($value), "Can not find class {$value}");
            } elseif ($key === 'factory') {
                self::assertTrue(class_exists($value[0]), "Can not find class {$value[0]}");
                self::assertTrue(is_callable($value[0] . '::' . $value[1]), "Can not call {$value[0]} . '::' . {$value[1]}");
            } elseif (is_array($value)) {
                $this->checkClassesExistence($value);
            }
        }
    }
}
