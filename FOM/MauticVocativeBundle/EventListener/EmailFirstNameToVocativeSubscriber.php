<?php
namespace MauticPlugin\MauticVocativeBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;

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
                if (preg_match_all('~(?<toReplace>\[(?<toVocative>[^\[\]]+)\|foo\])~u', $value, $matches) > 0) {
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
        $id = $this->toNormalizedId($value);
        if (!$this->translator->hasId($id, 'vocatives', 'cs_CZ')) {
            return $value; // nothing to do
        }

        return $this->translator->trans($id, [] /* no parameters */, 'vocatives', 'cs_CZ');
    }

    private function toNormalizedId($value)
    {
        $trimmed = trim($value);
        $decoded = html_entity_decode($trimmed, ENT_HTML5, 'UTF-8');
        $originalLocale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'C.UTF-8');
        $withoutDiacritics = iconv('UTF-8', 'ASCII//TRANSLIT', $decoded);
        setlocale(LC_CTYPE, $originalLocale);
        $underscored = preg_replace('~[^a-zA-Z0-9]+~', '_', $withoutDiacritics);
        $lowercased = strtolower($underscored);

        return 'plugin.vocative.' . $lowercased;
    }
}
