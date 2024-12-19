<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/actions',
        __DIR__ . '/apps',
        __DIR__ . '/bins',
        __DIR__ . '/tools',
        __DIR__ . '/tpls',
    ])
    ->withoutParallel()
    // uncomment to reach your current PHP version
    ->withPhpSets(php70: true)
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0);
