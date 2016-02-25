<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\EventListener;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\MauticVocativeBundle\EventListener\EmailNameToVocativeSubscriber;

class EmailNameToVocativeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function Conversion_reacts_both_on_send_and_view_of_email()
    {
        $this->assertEquals(
            array_keys(EmailNameToVocativeSubscriber::getSubscribedEvents()),
            [EmailEvents::EMAIL_ON_SEND, EmailEvents::EMAIL_ON_DISPLAY]
        );
    }

    /**
     * @test
     */
    public function I_got_names_in_vocative_on_email_send()
    {
        $mauticFactory = \Mockery::mock(MauticFactory::class);
        $mauticFactory->shouldReceive('getTemplating');
        $mauticFactory->shouldReceive('getRequest');
        $mauticFactory->shouldReceive('getSecurity');
        $mauticFactory->shouldReceive('getSerializer');
        $mauticFactory->shouldReceive('getSystemParameters');
        $mauticFactory->shouldReceive('getDispatcher');
        $mauticFactory->shouldReceive('getTranslator');
        /** @var MauticFactory|\Mockery\MockInterface $mauticFactory */
        $subscriber = new EmailNameToVocativeSubscriber($mauticFactory);
        $emailSendEvent = \Mockery::mock(EmailSendEvent::class);
        $emailSendEvent->shouldReceive('getContent')
            ->atLeast()->once()
            ->andReturn('foo [' . ($toVocative = 'baz') . '|vocative] bar');
        $emailSendEvent->shouldReceive('setContent')
            ->atLeast()->once()
            ->with('foo ' . ($inVocative = 'BAZ') . ' bar');
        $mauticFactory->shouldReceive('getKernel')
            ->andReturn($kernel = \Mockery::mock(\stdClass::class));
        $kernel->shouldReceive('getContainer')
            ->andReturn($container = \Mockery::mock(\stdClass::class));
        $container->shouldReceive('get')
            ->with('plugin.vocative.name_converter')
            ->andReturn($nameConverter = \Mockery::mock(\stdClass::class));

        $nameConverter->shouldReceive('convert')
            ->with($toVocative)
            ->andReturn($inVocative);
            /** @var EmailSendEvent $emailSendEvent */
            $subscriber->onEmailGenerate($emailSendEvent);
    }
}
