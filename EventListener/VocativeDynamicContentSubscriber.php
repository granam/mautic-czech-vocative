<?php

declare(strict_types=1);

namespace MauticPlugin\GranamCzechVocativeBundle\EventListener;

use Mautic\CoreBundle\Event as MauticEvents;
use Mautic\DynamicContentBundle\DynamicContentEvents;
use MauticPlugin\GranamCzechVocativeBundle\Service\NameToVocativeConverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DynamicContentSubscriber.
 */
class VocativeDynamicContentSubscriber implements EventSubscriberInterface
{

    public const SERVICE_ID = 'plugin.vocative.dynamic.content.subscriber';

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
            DynamicContentEvents::TOKEN_REPLACEMENT => ['onTokenReplacement', -10],
        ];
    }

    /**
     * @param MauticEvents\TokenReplacementEvent $event
     */
    public function onTokenReplacement(MauticEvents\TokenReplacementEvent $event)
    {
        $content = $event->getContent();
        $tokenList = $this->nameToVocativeConverter->findAndReplace($content);
        if (count($tokenList) > 0) {
            $content = str_replace(array_keys($tokenList), array_values($tokenList), $content);
        }
        $event->setContent($content);
    }
}
