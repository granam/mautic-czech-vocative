<?php
namespace MauticPlugin\MauticVocativeBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class FirstNameToVocativeIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'FirstNameToVocative';
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

}