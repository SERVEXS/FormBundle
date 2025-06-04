<?php

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
    $rectorConfig->sets([
        \Rector\PHPUnit\Set\PHPUnitLevelSetList::UP_TO_PHPUNIT_100
//        \Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_82,
//      \Rector\Symfony\Set\SymfonyLevelSetList::UP_TO_SYMFONY_63,
    ]);
};
