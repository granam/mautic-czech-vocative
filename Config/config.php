<?php

use MauticPlugin\GranamVocativeBundle\Service\NameToVocativeConverter;
use MauticPlugin\GranamVocativeBundle\Service\NameFactory;
use Granam\CzechVocative\CzechName;
use MauticPlugin\GranamVocativeBundle\EventListener\EmailNameToVocativeSubscriber;
use MauticPlugin\GranamVocativeBundle\EventListener\VocativeDynamicContentSubscriber;

return [
    'name' => 'Vocative',
    'description' => 'Modifier to convert a name or given gender-dependent alias to its vocative form, useful for email opening salutation.',
    'author' => 'Friends of Mautic',
    'version' => '2.1.0',

    'services' => [
        'events' => [
            EmailNameToVocativeSubscriber::SERVICE_ID => [
                'class' => EmailNameToVocativeSubscriber::class,
                'arguments' => [
                    NameToVocativeConverter::SERVICE_ID,
                ],
            ],
            VocativeDynamicContentSubscriber::SERVICE_ID => [
                'class' => VocativeDynamicContentSubscriber::class,
            ],
        ],
        'other' => [
            NameToVocativeConverter::SERVICE_ID => [
                'class' => NameToVocativeConverter::class,
                'arguments' => [
                    NameFactory::CZECH_NAME_SERVICE_ID,
                ],
            ],
            NameFactory::CZECH_NAME_SERVICE_ID => [
                'class' => CzechName::class,
                'factory' => [NameFactory::class, 'createCzechName'],
            ],
        ],
    ],
];
