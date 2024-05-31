<?php

namespace Binaryk\LaravelRestify\Observers;

use Binaryk\LaravelRestify\Cache\PolicyCache;
use Binaryk\LaravelRestify\Restify;
use Binaryk\LaravelRestify\Traits\InvalidatePolicyCacheable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FlushRestifyPolicyCacheObserver
{
    public function created(Model $model): void
    {
        $this->invalidateCache($model, 'created');
    }

    public function updated(Model $model): void
    {
        $this->invalidateCache($model, 'updated');
    }

    public function deleted(Model $model): void
    {
        $this->invalidateCache($model, 'deleted');
    }

    public function forceDeleted(Model $model): void
    {
        $this->invalidateCache($model, 'deleted');
    }

    public function restored(Model $model): void
    {
        $this->invalidateCache($model, 'restored');
    }

    protected function invalidateCache(Model $model, string $eventType): void
    {
        if (! in_array(InvalidatePolicyCacheable::class, class_uses($model), true)) {
            return;
        }

        $repositoryKey = app(Restify::repositoryForTable($model->getTable()))::uriKey();

        if (! $repositoryKey) {
            return;
        }

        $key = PolicyCache::keyForPolicyMethods($repositoryKey, $eventType, $model->getKey());

        if (! Cache::has($key)) {
            return;
        }

        Cache::forget($key);
    }
}
