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
        array_walk_recursive($config, function($value, $key) {
            if ($key === 'class') {
                $this->assertTrue(class_exists($value));
            }
        });
    }
}
