<?php
namespace MauticPlugin\MauticVocativeBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter;

class EmailNameToVocativeSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_SEND => ['onEmailGenerate', -999 /* lowest priority */],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', -999 /* lowest priority */],
        ];
    }

    public function onEmailGenerate(EmailSendEvent $event)
    {
        // to array and implode solves "sometimes string, sometimes array" return value
        $toVocalize = implode((array)$event->getContent(true /* with tokens replaced (to get names) */));
        $vocalized = $this->getConverter()->findAndReplace($toVocalize);
        $event->setContent($vocalized);
    }

    /**
     * @return NameToVocativeConverter
     */
    private function getConverter()
    {
        return $this->factory->getKernel()->getContainer()->get('plugin.vocative.name_converter');
    }

}
