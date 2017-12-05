<?php
namespace MauticPlugin\MauticVocativeBundle\Tests;

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class FOMTestWithMockery extends TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @param string $className
     * @return MockInterface
     */
    protected function mockery(string $className): MockInterface
    {
        return \Mockery::mock($className);
    }
}