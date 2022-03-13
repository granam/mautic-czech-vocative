<?php
namespace MauticPlugin\MauticVocativeBundle\EventListener;

use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailNameToVocativeSubscriber implements EventSubscriberInterface
{

    /**
     * @var NameToVocativeConverter
     */
    private $nameToVocativeConverter;

    public function __construct(NameToVocativeConverter $nameToVocativeConverter)
    {
        $this->nameToVocativeConverter = $nameToVocativeConverter;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EmailEvents::EMAIL_ON_SEND => ['onEmailGenerate', -999 /* lowest priority */],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', -999 /* lowest priority */],
        ];
    }

    /**
     * @param EmailSendEvent $event
     */
    public function onEmailGenerate(EmailSendEvent $event)
    {
        $content = $event->getSubject()
            . $event->getContent(true /* with tokens replaced (to get names) */)
            . $event->getPlainText();
        $tokenList = $this->nameToVocativeConverter->findAndReplace($content);
        if (\count($tokenList) > 0) {
            $event->addTokens($tokenList);
            unset($tokenList);
        }
    }

}