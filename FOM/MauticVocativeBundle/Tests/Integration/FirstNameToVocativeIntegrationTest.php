<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Integration;

use Mautic\CoreBundle\Factory\MauticFactory;
use MauticPlugin\MauticVocativeBundle\Integration\FirstNameToVocativeIntegration;

class FirstNameToVocativeIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function I_can_integrate_it_by_proper_name()
    {
        $integration = new FirstNameToVocativeIntegration($this->createFactory());
        $this->assertSame(
            $this->parseExpectedName(static::class),
            $integration->getName()
        );
    }

    /**
     * @return \Mockery\MockInterface|MauticFactory
     */
    private function createFactory()
    {
        $factory = \Mockery::mock(MauticFactory::class);

        return $factory;
    }

    private function parseExpectedName($testClassName)
    {
        $this->assertEquals(1, preg_match('~(?<name>\w+)IntegrationTest$~', $testClassName, $matches));

        return $matches['name'];
    }

    /**
     * @test
     */
    public function I_do_not_need_to_authenticate_to_use_it()
    {
        $integration = new FirstNameToVocativeIntegration($this->createFactory());
        $this->assertSame(
            'none',
            $integration->getAuthenticationType()
        );
    }
}
