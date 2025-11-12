<?php 

return [
    'is_service_cache' => env('LARAVEL_SUPPORT_IS_SERVICE_CACHE', false),
    'service_cache'  => \Projects\WellmedBackbone\Supports\ServiceCache::class,
];