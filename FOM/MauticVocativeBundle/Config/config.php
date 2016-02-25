<?php
return [
    'name' => 'Word to vocative',
    'description' => 'Modifier to convert a name to its vocative form, useful for email opening salutation.',
    'author' => 'Friends of Mautic',
    'version' => '1.0.0',

    'services' => [
        'events' => [
            'plugin.vocative.emailNameToVocative.subscriber' => [
                'class' => 'MauticPlugin\MauticVocativeBundle\EventListener\EmailNameToVocativeSubscriber'
            ]
        ],
        'other' => [
            'plugin.vocative.name_converter' => [
                'class' => 'MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter'
            ]
        ]
    ],
];
