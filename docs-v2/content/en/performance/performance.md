---
title: Performance
menuTitle: Performance
description: Performance
category: Advanced
position: 14
---

## Policy Caching

When loading a large number of models, Restify will check each policy method as `show` or `allowRestify` (including for all relations) before serializing them.

In order to improve performance, Restify caches the policies. You simply have to enable the caching by setting the `restify.cache.policies.enabled` property to `true` in the `restify.php` configuration file:

```php
'cache' => [
    'policies' => [
        'enabled' => true,
        'ttl' => 5 * 60, // seconds
    ],
],
```

Restify allows individual caching at the policy level with specific configurations. To enable this, the policy level must define the method `allowCaching`, which should return a `bool`.

Additionally, there are global configurations in `restify.php`:

```php
'cache' => [
    'policy' => [
        'allow_random_ttl' => true, //360 * random_int(1, 9);
        'default_ttl' =>  5 * 60,//default seconds
    ],
]
```
If the global configuration `restify.cache.policies.enabled` is set to `false`, individual policy caching will be disregarded. The primary point of control is the `allowCaching` method.

The caching is tight to the current authenticated user so if another user is logged in, the cache will be hydrated for the new user once again.

## Disable index meta

Index meta are policy information related to what actions are allowed on a resource for a specific user. However, if you don't need this information, you can disable the index meta by setting the `restify.repositories.serialize_index_meta` property to `false` in the `restify.php` configuration file:

```php
'repositories' => [
    'serialize_index_meta' => false,
    
    'serialize_show_meta' => true,
],
```

This will give your application a boost, especially when loading a large amount of resources or relations.
