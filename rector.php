<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector;
use Rector\EarlyReturn\Rector\If_\ChangeIfElseValueAssignToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeNestedIfsToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\EarlyReturn\Rector\Return_\PreparedValueToEarlyReturnRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;

return RectorConfig::configure()->withCache(__DIR__ . '/.cache/rector', FileCacheStorage::class)->withPaths(
    [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]
)->withPhpSets(
    php83: true
)->withPreparedSets(
    deadCode: true,
    codeQuality: true,
    codingStyle: true,
    typeDeclarations: true,
)->withRules(
    [
        RemoveAlwaysElseRector::class,
        ChangeNestedIfsToEarlyReturnRector::class,
        ChangeIfElseValueAssignToEarlyReturnRector::class,
        ChangeNestedForeachIfsToEarlyContinueRector::class,
        PreparedValueToEarlyReturnRector::class,
    ]
)->withSkip(
    [
        AddOverrideAttributeToOverriddenMethodsRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        StringClassNameToClassConstantRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
    ]
);
