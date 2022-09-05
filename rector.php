<?php

use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets
    $rectorConfig->sets([
        SetList::PHP_70,
        SetList::PHP_71,
        SetList::PHP_72,
        SetList::PHP_73,
    ]);
};
