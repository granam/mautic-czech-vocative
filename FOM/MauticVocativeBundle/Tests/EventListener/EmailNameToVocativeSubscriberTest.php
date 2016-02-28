<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\EventListener;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\LeadBundle\EventListener\EmailSubscriber;
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
    public function Conversion_has_lower_priority_than_lead_events()
    {
        $leadEmailEventPriorities = $this->getLeadEmailEventPriorities();
        foreach (EmailNameToVocativeSubscriber::getSubscribedEvents() as $eventName => $reaction) {
            $this->assertTrue(is_array($reaction));
            $priority = $this->filterPriorityValue($reaction);
            $this->assertArrayHasKey($eventName, $leadEmailEventPriorities);
            $this->assertLessThan($leadEmailEventPriorities[$eventName], $priority);
        }
    }

    /**
     * By event name indexed priorities
     * @return array|int[]
     */
    private function getLeadEmailEventPriorities()
    {
        $subscribedEvents = EmailSubscriber::getSubscribedEvents();
        $lookedForEvents = [EmailEvents::EMAIL_ON_SEND, EmailEvents::EMAIL_ON_DISPLAY];
        $this->assertNotEmpty($lookedForEvents);
        $watchedEvents = array_filter(
            $subscribedEvents,
            function ($value) use ($subscribedEvents, $lookedForEvents) {
                $eventName = array_search($value, $subscribedEvents);

                return in_array($eventName, $lookedForEvents);
            }
        );
        $priorities = [];
        foreach ($watchedEvents as $eventName => $reaction) {
            $priority = $this->filterPriorityValue($reaction);
            $priorities[$eventName] = $priority;
        }
        $this->assertCount(count($lookedForEvents), $priorities);

        return $priorities;
    }

    private function filterPriorityValue(array $reaction)
    {
        $wrappedPriority = array_filter($reaction, function ($value) {
            return is_numeric($value);
        });
        $this->assertTrue(is_array($wrappedPriority));

        return current($wrappedPriority);
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
