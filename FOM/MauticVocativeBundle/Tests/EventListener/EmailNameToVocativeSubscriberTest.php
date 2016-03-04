<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\EventListener;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\LeadBundle\EventListener\EmailSubscriber;
use MauticPlugin\MauticVocativeBundle\EventListener\EmailNameToVocativeSubscriber;
use MauticPlugin\MauticVocativeBundle\Tests\FOMTestWithMockery;

class EmailNameToVocativeSubscriberTest extends FOMTestWithMockery
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
        $leadEmailEventPriorities = $this->getLeadEmailEventsPriorities();
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
    private function getLeadEmailEventsPriorities()
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
    public function I_got_names_converted_in_email()
    {
        $mauticFactory = $this->createMauticFactory($toVocalize = 'foo [bar|vocative] baz', $vocalized = 'qux');
        $subscriber = new EmailNameToVocativeSubscriber($mauticFactory);
        $emailSendEvent = $this->createEmailSentEvent($toVocalize, $vocalized);
        $subscriber->onEmailGenerate($emailSendEvent);
    }

    /**
     * @param string $toVocalize
     * @param string $vocalized
     * @return EmailSendEvent|\Mockery\MockInterface $emailSendEvent
     */
    private function createEmailSentEvent($toVocalize, $vocalized)
    {
        $emailSendEvent = $this->mockery('\Mautic\EmailBundle\Event\EmailSendEvent');
        $emailSendEvent->shouldReceive('getContent')
            ->with(true)// with tokens replaced
            ->once()
            ->andReturn($toVocalize);
        $emailSendEvent->shouldReceive('setContent')
            ->once()
            ->with($vocalized);

        return $emailSendEvent;
    }

    /**
     * @param string $toVocative
     * @param string $inVocative
     * @return \Mockery\MockInterface|MauticFactory
     */
    private function createMauticFactory($toVocative, $inVocative)
    {
        $mauticFactory = $this->mockery('\Mautic\CoreBundle\Factory\MauticFactory');
        $mauticFactory->shouldReceive('getTemplating');
        $mauticFactory->shouldReceive('getRequest');
        $mauticFactory->shouldReceive('getSecurity');
        $mauticFactory->shouldReceive('getSerializer');
        $mauticFactory->shouldReceive('getSystemParameters');
        $mauticFactory->shouldReceive('getDispatcher');
        $mauticFactory->shouldReceive('getTranslator');
        $mauticFactory->shouldReceive('getKernel')
            ->andReturn($kernel = $this->mockery('\stdClass'));
        $kernel->shouldReceive('getContainer')
            ->andReturn($container = $this->mockery('\stdClass'));
        $container->shouldReceive('get')
            ->with('plugin.vocative.name_converter')
            ->andReturn($nameConverter = $this->mockery('\MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter'));
        $nameConverter->shouldReceive('findAndReplace')
            ->with($toVocative)
            ->andReturn($inVocative);

        return $mauticFactory;
    }
}
