<?php
namespace MauticPlugin\MauticVocativeBundle\Tests\EventListener;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\LeadBundle\EventListener\EmailSubscriber;
use MauticPlugin\MauticVocativeBundle\EventListener\EmailNameToVocativeSubscriber;
use MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter;
use MauticPlugin\MauticVocativeBundle\Tests\FOMTestWithMockery;

class EmailNameToVocativeSubscriberTest extends FOMTestWithMockery
{

    /**
     * @test
     */
    public function Conversion_reacts_both_on_send_and_view_of_email()
    {
        self::assertEquals(
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
            self::assertTrue(\is_array($reaction));
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
        $watchedEvents = \array_filter(
            $subscribedEvents,
            function ($value) use ($subscribedEvents, $lookedForEvents) {
                $eventName = \array_search($value, $subscribedEvents, true);

                return \in_array($eventName, $lookedForEvents, true);
            }
        );
        $priorities = [];
        foreach ($watchedEvents as $eventName => $reaction) {
            $priority = $this->filterPriorityValue($reaction);
            $priorities[$eventName] = $priority;
        }
        self::assertCount(\count($lookedForEvents), $priorities);

        return $priorities;
    }

    private function filterPriorityValue(array $reaction)
    {
        $wrappedPriority = \array_filter($reaction, function ($value) {
            return \is_numeric($value);
        });
        self::assertTrue(\is_array($wrappedPriority));

        return current($wrappedPriority);
    }

    /**
     * @test
     */
    public function I_got_names_converted_in_email()
    {
        $mauticFactory = $this->createMauticFactory($toVocalize = 'foo [bar|vocative] baz', ['bar' => 'qux']);
        $subscriber = new EmailNameToVocativeSubscriber($mauticFactory);
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
     * @param array|string[] $previousToReplacedTokens
     * @return \Mockery\MockInterface|MauticFactory
     */
    private function createMauticFactory(string $toVocative, array $previousToReplacedTokens): MauticFactory
    {
        $mauticFactory = $this->mockery(MauticFactory::class);
        $mauticFactory->shouldReceive('getTemplating');
        $mauticFactory->shouldReceive('getRequest');
        $mauticFactory->shouldReceive('getSecurity');
        $mauticFactory->shouldReceive('getSerializer');
        $mauticFactory->shouldReceive('getSystemParameters');
        $mauticFactory->shouldReceive('getDispatcher');
        $mauticFactory->shouldReceive('getTranslator');
        $mauticFactory->shouldReceive('getKernel')
            ->andReturn($kernel = $this->mockery(\stdClass::class));
        $kernel->shouldReceive('getContainer')
            ->andReturn($container = $this->mockery(\stdClass::class));
        $container->shouldReceive('get')
            ->zeroOrMoreTimes()
            ->with('plugin.vocative.name_converter')
            ->andReturn($nameConverter = $this->mockery(NameToVocativeConverter::class));
        $nameConverter->shouldReceive('findAndReplace')
            ->zeroOrMoreTimes()
            ->with($toVocative)
            ->andReturn($previousToReplacedTokens);

        return $mauticFactory;
    }
}
