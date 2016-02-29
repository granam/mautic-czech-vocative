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
        $event->setContent($this->findAndReplace((array)$event->getContent(true /* with tokens replaced -  to get names */)));
    }

    private function findAndReplace(array $values)
    {
        $replaced = array_map(
            function ($value) {
                /*
                 * searching for [name|vocative] (enclosed by native or encoded square brackets,
                 * with name optionally enclosed by [square brackets] as well to match email preview
                 */
                if (preg_match_all(
                        '~(?<toReplace>(?:\[|%5B)(?<prefixToKeep>\[*)(?<toVocative>[^\[\]]+)(?<suffixToKeep>\]*)\|vocative(?:\]|%5D))~u',
                        $value,
                        $matches
                    ) > 0
                ) {
                    foreach ($matches['toReplace'] as $index => $toReplace) {
                        $toVocative = $matches['toVocative'][$index];
                        $prefixToKeep = $matches['prefixToKeep'][$index];
                        $suffixToKeep = $matches['suffixToKeep'][$index];
                        $value = str_replace($toReplace, $prefixToKeep . $this->toVocative($toVocative) . $suffixToKeep, $value);
                    }
                }
                // lets remove unused '|vocative' modifiers, like empty ones
                if (preg_match_all('~(?<toRemove>(?:\[|%5B)(?<toKeep>.*)\|vocative(?:\]|%5D))~u', $value, $matches) > 0) {
                    foreach ($matches['toRemove'] as $index => $toReplace) {
                        $toKeep = $matches['toKeep'][$index];
                        $value = str_replace($toReplace, $toKeep, $value);
                    }
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
