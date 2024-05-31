<?php

namespace Binaryk\LaravelRestify\Traits;

use Binaryk\LaravelRestify\Cache\Cacheable;
use Binaryk\LaravelRestify\Observers\FlushRestifyPolicyCacheObserver;
use Binaryk\LaravelRestify\Restify;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

trait InvalidatePolicyCacheable
{
    public static function bootedPolicyCacheable(): void
    {
        $model = static::newModel();

        if (! static::isCacheImplemented($model)) {
            return;
        }

        $shouldFlush = app(Restify::repositoryForTable($model->getTable()))::$flushRestifyCache;

        if (isset($shouldFlush) && $shouldFlush) {
            static::newModel()::observe(
                static::getFlushPolicyCacheObserver()
            );
        }
    }

    protected static function getFlushPolicyCacheObserver(): string
    {
        return FlushRestifyPolicyCacheObserver::class;
    }

    public static function isCacheImplemented(Model $model): bool
    {
        $policy = Gate::getPolicyFor($model);

        return method_exists($policy, 'cache') && $policy instanceof Cacheable;
    }
}
