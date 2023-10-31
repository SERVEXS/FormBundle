<?php

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/DataTransformer',
        __DIR__ . '/DependencyInjection',
        __DIR__ . '/Resources',
        __DIR__ . '/Type',
        __DIR__ . '/tests',
    ]);
    $rectorConfig->sets([
        \Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_74,
        //        \Rector\Symfony\Set\SymfonyLevelSetList::UP_TO_SYMFONY_50,
    ]);
};
