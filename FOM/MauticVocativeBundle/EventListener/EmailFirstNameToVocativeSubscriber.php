<?php
namespace MauticPlugin\MauticVocativeBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter;

class EmailFirstNameToVocativeSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_SEND => ['onEmailGenerate', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', 0],
        ];
    }

    public function onEmailGenerate(EmailSendEvent $event)
    {
        $event->setContent($this->findAndReplace((array)$event->getContent()));
    }

    private function findAndReplace(array $values)
    {
        $replaced = array_map(
            function ($value) {
                if (preg_match_all('~(?<toReplace>\[(?<toVocative>[^\[\]]+)\|vocative\])~u', $value, $matches) > 0) {
                    foreach ($matches['toReplace'] as $index => $toReplace) {
                        $toVocative = $matches['toVocative'][$index];
                        $value = str_replace($toReplace, $this->toVocative($toVocative), $value);
                    }

                    return $value;
                }

                return $value;
            },
            $values
        );

        return implode($replaced);
    }

    private function toVocative($value)
    {
        /** @var NameToVocativeConverter $converter */
        $converter = $this->factory->getKernel()->getContainer()->get('plugin.vocative.name_converter');

        return $converter->convert($value);
    }

}
