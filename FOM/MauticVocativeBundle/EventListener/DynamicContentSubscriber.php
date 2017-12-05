<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticVocativeBundle\EventListener;

use Mautic\CoreBundle\Event as MauticEvents;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\DynamicContentBundle\DynamicContentEvents;
use MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter;

/**
 * Class DynamicContentSubscriber.
 */
class DynamicContentSubscriber extends CommonSubscriber
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
        if (\count($tokenList) > 0) {
            $content = \str_replace(\array_keys($tokenList), \array_values($tokenList), $content);
        }
        $event->setContent($content);
    }
}