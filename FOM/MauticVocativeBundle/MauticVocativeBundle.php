<?php
namespace MauticPlugin\MauticVocativeBundle;

use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class MauticVocativeBundle extends PluginBundleBase
{
    const CZECH_NAME_CLASS_SOURCE_FILE = __DIR__ . '/vendor/granam/czech-vocative/Granam/CzechVocative/CzechName.php';

    public function boot()
    {
        require_once self::CZECH_NAME_CLASS_SOURCE_FILE;
    }

    /**
     * To provide dependent library without dependency on Composer.
     * For usage @see scripts section in composer.json
     * @throws \LogicException
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public static function includeLibraryToBundle()
    {
        $fs = new Filesystem();
        $from = __DIR__ . '/../../vendor/granam/czech-vocative/Granam/CzechVocative/';
        $to = __DIR__ . '/vendor/granam/czech-vocative/Granam/CzechVocative/';
        $fs->remove($to);
        $fs->mirror($from, $to);
        $czechName = \file_get_contents(self::CZECH_NAME_CLASS_SOURCE_FILE);
        if (empty($czechName)) {
            throw new IOException('Unable to read from ' . self::CZECH_NAME_CLASS_SOURCE_FILE);
        }
        $withLocalNamespace = \preg_replace(
            '~namespace\s+[^;]+~',
            'namespace MauticPlugin\MauticVocativeBundle\CzechVocative',
            $czechName
        );
        if ($withLocalNamespace === null || $czechName === $withLocalNamespace) {
            \preg_match('~\s*namespace\s+(?<namespace>[^;]+)~', $czechName, $matches);
            throw new \LogicException(
                'Unable to replace namespace from CzechVocative to MauticPlugin\MauticVocativeBundle\CzechVocative'
                . ', original namespace is ' . (!empty($matches['namespace']) ? $matches['namespace'] : 'unknown')
            );
        }
        if (!\file_put_contents(self::CZECH_NAME_CLASS_SOURCE_FILE, $withLocalNamespace)) {
            throw new IOException('Unable to write into ' . self::CZECH_NAME_CLASS_SOURCE_FILE);
        }
    }
}
