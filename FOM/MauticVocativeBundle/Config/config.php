<?php
return [
    'name' => 'Word to vocative',
    'description' => 'Modifier to convert a name or given gender-dependent alias to its vocative form, useful for email opening salutation.',
    'author' => 'Friends of Mautic',
    'version' => '2.0.2',

    'services' => [
        'events' => [
            'plugin.vocative.emailNameToVocative.subscriber' => [
                'class' => \MauticPlugin\MauticVocativeBundle\EventListener\EmailNameToVocativeSubscriber::class,
                'arguments' => ['plugin.vocative.name_converter']
            ],
            'plugin.vocative.dynamic.content.subscriber' => [
                'class' => \MauticPlugin\MauticVocativeBundle\EventListener\DynamicContentSubscriber::class,
            ],
        ],
        'other' => [
            'plugin.vocative.name_converter' => [
                'class' => \MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter::class,
                'arguments' => ['plugin.vocative.czech_name']
            ],
            'plugin.vocative.czech_name' => [
                'class' => \Granam\CzechVocative\CzechName::class,
                'factory' => [\MauticPlugin\MauticVocativeBundle\Service\NameFactory::class, 'createCzechName']
            ]
        ]
    ],
];
