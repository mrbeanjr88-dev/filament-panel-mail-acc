<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Production Repository
    |--------------------------------------------------------------------------
    |
    | Enter the URL of your repository. This will be used to clone the repo
    | and keep it in sync with future pushes.
    |
    */

    'repository' => 'https://github.com/mrbeanjr88-dev/filament-panel-mail-acc.git',

    /*
    |--------------------------------------------------------------------------
    | Production Branch
    |--------------------------------------------------------------------------
    |
    | Enter the branch name that should be deployed. This will be used to
    | keep the deployment in sync with the branch on your repository.
    |
    */

    'branch' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Keep Releases
    |--------------------------------------------------------------------------
    |
    | Enter the number of releases you wish to keep on the server. When a
    | new release is cloned, the oldest release will be removed. In order
    | to trigger a rollback you may visit the "Releases" page on your
    | Forge dashboard.
    |
    */

    'keep_releases' => 5,

    /*
    |--------------------------------------------------------------------------
    | Shared Directories / Files
    |--------------------------------------------------------------------------
    |
    | Enter any directories or files that should be shared between releases.
    | These will be symlinked into the release directory from the shared
    | directory. This is useful for storage folders and .env files.
    |
    */

    'shared' => [
        'storage',
        '.env',
    ],

    /*
    |--------------------------------------------------------------------------
    | Build / Warm
    |--------------------------------------------------------------------------
    |
    | Enter any build / warm commands that should be run on deployment.
    | These commands will be run within the context of the new release
    | directory. Typically, you will want to run composer install, and
    | any other build commands such as npm run build.
    |
    */

    'build' => [
        'composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader',
        'php artisan config:cache',
        'php artisan event:cache',
        'php artisan route:cache',
        'php artisan view:cache',
        'php artisan filament:upgrade',
        'php artisan migrate --force',
        'npm ci --ignore-scripts',
        'npm run build',
    ],

    /*
    |--------------------------------------------------------------------------
    | Migrate
    |--------------------------------------------------------------------------
    |
    | Enter if your deployment should run database migrations each time it
    | is deployed. If this is set to "true", then the Migrate Artisan
    | command will be run. Otherwise, only the "build" commands will run.
    |
    */

    'migrate' => true,

    /*
    |--------------------------------------------------------------------------
    | Workers
    |--------------------------------------------------------------------------
    |
    | Enter any workers that your application depends on. These will be
    | restarted by the deployment script after the deployment is
    | finished. This ensures your workers are using the new code.
    |
    */

    'workers' => [
        [
            'connection' => 'redis',
            'queue' => ['default'],
            'tries' => 3,
            'timeout' => 120,
            'sleep' => 1,
            'max_time' => 3600,
            'backoff' => 5,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Restart Queue After Deployment
    |--------------------------------------------------------------------------
    |
    | Enter if your deployment should restart the queue worker after
    | the deployment is finished. If you have many queue workers,
    | you may want to set this to false.
    |
    */

    'restart_queue_after_deploy' => true,

];
