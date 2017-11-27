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

    protected function mockery($className): MockInterface
    {
        return \Mockery::mock($className);
    }
}