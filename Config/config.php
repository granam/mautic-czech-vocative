<?php

use MauticPlugin\GranamCzechVocativeBundle\Service\NameToVocativeConverter;
use MauticPlugin\GranamCzechVocativeBundle\Service\NameFactory;
use Granam\CzechVocative\CzechName;
use MauticPlugin\GranamCzechVocativeBundle\EventListener\EmailNameToVocativeSubscriber;
use MauticPlugin\GranamCzechVocativeBundle\EventListener\VocativeDynamicContentSubscriber;

return [
    'name' => 'Czech vocative',
    'description' => 'Modifier to convert a name or given gender-dependent alias to its vocative form, useful for email opening salutation.',
    'author' => 'Jaroslav Týc',
    'version' => '4.0.0',

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
