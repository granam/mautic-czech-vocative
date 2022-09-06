<?php

declare(strict_types=1);

namespace MauticPlugin\GranamCzechVocativeBundle\Tests\EventListener;

use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\LeadBundle\EventListener\EmailSubscriber;
use MauticPlugin\GranamCzechVocativeBundle\EventListener\EmailNameToVocativeSubscriber;
use MauticPlugin\GranamCzechVocativeBundle\Service\NameToVocativeConverter;
use MauticPlugin\GranamCzechVocativeBundle\Tests\GranamTestWithMockery;
use Mockery\MockInterface;

class EmailNameToVocativeSubscriberTest extends GranamTestWithMockery
{

    /**
     * @test
     */
    public function Conversion_reacts_both_on_send_and_view_of_email()
    {
        self::assertEquals(
            [EmailEvents::EMAIL_ON_SEND, EmailEvents::EMAIL_ON_DISPLAY],
            array_keys(EmailNameToVocativeSubscriber::getSubscribedEvents())
        );
    }

    /**
     * @test
     */
    public function Conversion_has_lower_priority_than_lead_events()
    {
        $leadEmailEventPriorities = $this->getLeadEmailEventsPriorities();
        foreach (EmailNameToVocativeSubscriber::getSubscribedEvents() as $eventName => $reaction) {
            self::assertTrue(is_array($reaction));
            $priority = $this->filterPriorityValue($reaction);
            self::assertArrayHasKey($eventName, $leadEmailEventPriorities);
            self::assertLessThan($leadEmailEventPriorities[$eventName], $priority);
        }
    }

    /**
     * By event name indexed priorities
     * @return array|int[]
     */
    private function getLeadEmailEventsPriorities(): array
    {
        $subscribedEvents = EmailSubscriber::getSubscribedEvents();
        $lookedForEvents = [EmailEvents::EMAIL_ON_SEND, EmailEvents::EMAIL_ON_DISPLAY];
        self::assertNotEmpty($lookedForEvents);
        $watchedEvents = array_filter(
            $subscribedEvents,
            function ($value) use ($subscribedEvents, $lookedForEvents) {
                $eventName = array_search($value, $subscribedEvents, true);

                return in_array($eventName, $lookedForEvents, true);
            }
        );
        $priorities = [];
        foreach ($watchedEvents as $eventName => $reaction) {
            $priority = $this->filterPriorityValue($reaction);
            $priorities[$eventName] = $priority;
        }
        self::assertCount(count($lookedForEvents), $priorities);

        return $priorities;
    }

    private function filterPriorityValue(array $reaction)
    {
        $wrappedPriority = array_filter($reaction, function ($value) {
            return is_numeric($value);
        });
        self::assertIsArray($wrappedPriority);

        return current($wrappedPriority);
    }

    /**
     * @test
     */
    public function I_got_names_converted_in_email()
    {
        $subscriber = new EmailNameToVocativeSubscriber($this->createNameToVocativeConverter($toVocalize = 'foo [bar|vocative] baz', ['bar' => 'qux']));
        $emailSendEvent = $this->createEmailSentEvent($toVocalize, ['bar' => 'qux']);
        $subscriber->onEmailGenerate($emailSendEvent);
        self::assertTrue(true);
    }

    /**
     * @param string $toVocalize
     * @param array $tokensToReplace
     * @return EmailSendEvent|\Mockery\MockInterface $emailSendEvent
     */
    private function createEmailSentEvent(string $toVocalize, array $tokensToReplace): EmailSendEvent
    {
        $emailSendEvent = $this->mockery(EmailSendEvent::class);
        $emailSendEvent->shouldReceive('getContent')
            ->once()
            ->with(true)// with tokens replaced
            ->andReturn($toVocalize);
        $emailSendEvent->shouldReceive('getSubject')
            ->once()
            ->andReturn('');
        $emailSendEvent->shouldReceive('getPlainText')
            ->once()
            ->andReturn('');
        $emailSendEvent->shouldReceive('addTokens')
            ->once()
            ->with($tokensToReplace)
            ->andReturn('');

        return $emailSendEvent;
    }

    /**
     * @param string $toVocative
     * @param array $previousToReplacedTokens
     * @return NameToVocativeConverter|MockInterface
     */
    private function createNameToVocativeConverter(string $toVocative, array $previousToReplacedTokens): NameToVocativeConverter
    {
        $nameConverter = $this->mockery(NameToVocativeConverter::class);
        $nameConverter->shouldReceive('findAndReplace')
            ->zeroOrMoreTimes()
            ->with($toVocative)
            ->andReturn($previousToReplacedTokens);

        return $nameConverter;
    }
}
