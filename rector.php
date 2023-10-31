<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/DataTransformer',
        __DIR__ . '/DependencyInjection',
        __DIR__ . '/Resources',
        __DIR__ . '/Type',
        __DIR__ . '/Tests',
    ]);
    $rectorConfig->sets([
        \Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_74,
//        \Rector\Symfony\Set\SymfonyLevelSetList::UP_TO_SYMFONY_50,
    ]);
};
