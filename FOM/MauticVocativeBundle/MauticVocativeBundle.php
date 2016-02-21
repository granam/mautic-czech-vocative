<?php
namespace MauticPlugin\MauticVocativeBundle;

use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Symfony\Component\Filesystem\Filesystem;

class MauticVocativeBundle extends PluginBundleBase
{
    public static function includeLibraryToBundle()
    {
        $fs = new Filesystem();
        $fs->mirror(
            __DIR__ . '/../../vendor/granam/czech-vocative/src',
            __DIR__ . '/vendor/granam/czech-vocative/src'
        );
        file_put_contents(
            __DIR__ . '/vendor/granam/czech-vocative/src/CzechName.php',
            preg_replace(
                '~namespace\s+CzechVocative~',
                'namespace MauticPlugin\MauticVocativeBundle\CzechVocative',
                file_get_contents(__DIR__ . '/vendor/granam/czech-vocative/src/CzechName.php')
            )
        );
    }

    public function boot()
    {
        require_once __DIR__ . '/vendor/granam/czech-vocative/src/CzechName.php';
    }
}