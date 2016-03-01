<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Integration;

use Mautic\CoreBundle\Factory\MauticFactory;
use MauticPlugin\MauticVocativeBundle\Integration\FOMVocativeIntegration;
use MauticPlugin\MauticVocativeBundle\Tests\FOMTestWithMockery;

class FOMVocativeIntegrationTest extends FOMTestWithMockery
{

    /**
     * @test
     */
    public function I_can_integrate_it_by_proper_name()
    {
        $integration = new FOMVocativeIntegration($this->createFactory());
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
        $factory = $this->mockery(MauticFactory::class);

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
        $integration = new FOMVocativeIntegration($this->createFactory());
        $this->assertSame(
            'none',
            $integration->getAuthenticationType()
        );
    }
}
