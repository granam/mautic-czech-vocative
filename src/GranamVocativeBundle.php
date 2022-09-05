<?php

declare(strict_types=1);

namespace MauticPlugin\GranamVocativeBundle;

use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class GranamVocativeBundle extends PluginBundleBase
{
    private const CZECH_NAME_CLASS_SOURCE_FILE = __DIR__ . '/vendor/granam/czech-vocative/src/CzechName.php';

    public function boot()
    {
        require_once self::CZECH_NAME_CLASS_SOURCE_FILE;
    }

    /**
     * To provide dependent library without dependency on Composer.
     * For usage @see scripts -> pre-autoload-dump section in composer.json
     * @throws \LogicException
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @codeCoverageIgnore
     */
    public static function includeLibraryToBundle()
    {
        $fs = new Filesystem();
        $from = __DIR__ . '/../vendor/granam/czech-vocative/src/';
        $to = __DIR__ . '/vendor/granam/czech-vocative/src/';
        $fs->remove($to);
        $fs->mirror($from, $to);
        $czechName = file_get_contents(self::CZECH_NAME_CLASS_SOURCE_FILE);
        if (empty($czechName)) {
            throw new IOException('Unable to read from ' . self::CZECH_NAME_CLASS_SOURCE_FILE);
        }
        $withLocalNamespace = \preg_replace(
            '~namespace\s+[^;]+~',
            'namespace MauticPlugin\GranamVocativeBundle\CzechVocative',
            $czechName
        );
        if ($withLocalNamespace === null || $czechName === $withLocalNamespace) {
            preg_match('~\s*namespace\s+(?<namespace>[^;]+)~', $czechName, $matches);
            throw new \LogicException(
                'Unable to replace namespace from CzechVocative to MauticPlugin\GranamVocativeBundle\CzechVocative'
                . ', original namespace is ' . (!empty($matches['namespace']) ? $matches['namespace'] : 'unknown')
            );
        }
        if (!file_put_contents(self::CZECH_NAME_CLASS_SOURCE_FILE, $withLocalNamespace)) {
            throw new IOException('Unable to write into ' . self::CZECH_NAME_CLASS_SOURCE_FILE);
        }
    }
}
