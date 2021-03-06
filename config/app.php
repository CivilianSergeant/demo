<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'encryption_key' => '1234567891234567',

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'single'),

    'log_level' => env('APP_LOG_LEVEL', 'debug'),

    'product' => [
        'title'=> 'PLAAS OTT API',
        'version'=>'1.01'
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */


        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,

    ],


    /*
    |--------------------------------------------------------------------------
    | Username password
    |--------------------------------------------------------------------------
    | User name and password for login to api documentation
    |
    */

    'username'=>'admin',
    'password'=>'admiN321',


    /*
    |--------------------------------------------------------------------------
    | appId appSecurityCode confirmCodeTemplate
    |--------------------------------------------------------------------------
    | appId and appSecurityCode need to be used by remote application
    | to have access into API
    |
    */

    'appId'   => 'NexViewersentTV',
    'appSecurityCode' => 'eee80f834a6e15b47db06fb70e75bada',
    'confirmCodeTemplate' => 'VIEWERS TV PASSCODE IS: ',

    /*
    |--------------------------------------------------------------------------
    | bkashMerchantNumber
    |--------------------------------------------------------------------------
    | bkashMerchantNumber  is used for bkash transaction as merchant number
    | into the Get BKash Info API
    |
    */

    'bkashMerchantNumber' => '01711234567',



    /*
    |--------------------------------------------------------------------------
    | Menus
    |--------------------------------------------------------------------------
    | Menus and Routes for api documents
    |
    */

    'api_routes' => [
        'service-operators'         => '01. Get Service Operators',
        'service-operator-ids'      => '02. Get Service Operator IDs',
        'device-types'              => '03. Get Device Types',
        'system-settings'           => '04. Get System Settings',
        'registration-subscriber'   => '05. Registration Subscriber',
        're-registration'           => '06. Re-Registration',
        'confirm-code'              => '07. Confirm Code',
        'sign-in'                   => '08. Sign-In',
        'api-login'                 => '09. App Login',
        'subscriber-profile'        => '10. Get Subscriber Profile',
        'subscriber-profile-update' => '11. Set Subscriber Profile',
        'heart-beat'                => '12. Get Heart Beat',
        'contents'                  => '13. Get Contents',
        'viewing-content'           => '14. Get Viewing Content',
        'feature-contents'          => '15. Get Feature Contents',
        'history-contents'          => '16. Get History Contents',
        'relative-contents'         => '17. Get Relative Contents',
        'search-contents'           => '18. Get Search Contents',
        'popular-contents'          => '19. Get Popular Contents',
        'purchase-content-by-wallet'=> '20. Purchase Content By Wallet',
        'packages'                  => '21. Get Packages',
        'package-details'           => '22. Get Package Details',
        'purchase-package-by-wallet'=> '23. Purchase Package By Wallet',
        'subscribed-packages'       => '24. Get Subscribed Packages',
        'categories'                => '25. Get Categories',
        'epgs'                      => '26. Get EPG',
        'set-favorites'             => '27. Set Favorites',
        'favorite-contents'         => '28. Get Favorite Contents',
        'notification'              => '29. Get Notification',
        'bkash-info'                => '30. Get Bkash Info',
        'set-fcm-token'             => '31. Set FCM Token',
        'newly-uploaded-contents'   => '32. Get Newly Uploaded Contents',
        'add-amount-by-scratch-card'=> '33. Add Amount By Scratch Card',
        'about-us'                  => '34. Get About Us',
        'relative-contents-ext'     => '35. Get Relative Contents Ext',
        'forgot-password'           => '36. Forgot Password',
        'reset-password'            => '37. Reset Password',
        'change-password'           => '38. Change Password',
        'content-order-id'          => '39. Get Content Order ID',
        'new-epgs'              => '40. Get New EPG'
    ]

];
