<?php
namespace MauticPlugin\MauticVocativeBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class VocativeIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'Vocative';
    }

    public function getAuthenticationType()
    {
        return 'oauth2';
    }

}