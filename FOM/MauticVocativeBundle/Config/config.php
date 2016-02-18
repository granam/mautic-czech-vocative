<?php
return [
    'name' => 'First name to vocative',
    'description' => 'Modifier to convert a first name to its vocative form, useful for email opening salutation.',
    'author' => 'Friends of Mautic',
    'version' => '1.0.0',

    'services' => [
        'events' => [
            'plugin.vocative.vocativebundle.subscriber' => [
                'class' => 'MauticPlugin\MauticVocativeBundle\EventListener\VocativeSubscriber'
            ]
        ],
    ],
];