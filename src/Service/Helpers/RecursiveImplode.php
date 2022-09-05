<?php
namespace MauticPlugin\GranamVocativeBundle\Service\Helpers;

class RecursiveImplode
{
    public static function implode(array $values): string
    {
        return implode(
            array_map(
                function ($value) {
                    if (is_array($value)) {
                        return self::implode($value);
                    }
                    return $value;
                },
                $values
            )
        );
    }
}
