<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\Integration;

use MauticPlugin\MauticVocativeBundle\Integration\FOMVocativeIntegration;
use MauticPlugin\MauticVocativeBundle\Tests\FOMTestWithMockery;

class FOMVocativeIntegrationTest extends FOMTestWithMockery
{

    /**
     * @test
     */
    public function I_can_integrate_it_by_proper_name()
    {
        $integration = new FOMVocativeIntegration();
        self::assertSame(
            $this->parseExpectedName(\get_called_class()),
            $integration->getName()
        );
    }

    private function parseExpectedName($testClassName)
    {
        self::assertEquals(1, preg_match('~(?<name>\w+)IntegrationTest$~', $testClassName, $matches));

        return $matches['name'];
    }

    /**
     * @test
     */
    public function I_do_not_need_to_authenticate_to_use_it()
    {
        $integration = new FOMVocativeIntegration();
        self::assertSame(
            'none',
            $integration->getAuthenticationType()
        );
    }
}