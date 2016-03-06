<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function Classes_listed_in_config_exists()
    {
        $this->assertFileExists($file = __DIR__ . '/../../Config/config.php');
        $config = include __DIR__ . '/../../Config/config.php';
        $this->assertTrue(is_array($config));
        $this->checkClassesExistence($config);
    }

    private function checkClassesExistence(array $config)
    {
        foreach ($config as $key => $value) {
            if ($key === 'class') {
                $this->assertTrue(class_exists($value));
            } else if ($key === 'factory') {
                $this->assertTrue(class_exists($value[0]));
                $this->assertTrue(is_callable($value[0] . '::' . $value[1]));
            } else if (is_array($value)) {
                $this->checkClassesExistence($value);
            }
        }
    }
}
