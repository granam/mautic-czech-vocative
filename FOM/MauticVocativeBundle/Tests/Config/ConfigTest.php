<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Config;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @test
     */
    public function Classes_listed_in_config_exists()
    {
        self::assertFileExists($file = __DIR__ . '/../../Config/config.php');
        $config = include __DIR__ . '/../../Config/config.php';
        self::assertTrue(\is_array($config));
        $this->checkClassesExistence($config);
    }

    private function checkClassesExistence(array $config)
    {
        foreach ($config as $key => $value) {
            if ($key === 'class') {
                self::assertTrue(\class_exists($value), "Can not find class {$value}");
            } else if ($key === 'factory') {
                self::assertTrue(\class_exists($value[0], "Can not find class {$value[0]}"));
                self::assertTrue(\is_callable($value[0] . '::' . $value[1]), "Can not call {$value[0]} . '::' . {$value[1]}");
            } else if (\is_array($value)) {
                $this->checkClassesExistence($value);
            }
        }
    }
}
