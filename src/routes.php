<?php

Route::group(['prefix' => 'snowfire'], function() {

    Route::get('/install', ['as' => 'snowfire.install', function() {
        return Response::make(Snowfire::xml(), 200, ['content-type' => 'text/xml']);
    }]);

    Route::post('/accept', ['as' => 'snowfire.accept', function()
    {
        $storage = \Snowfire\App\Storage\AccountStorage::where('site_url', '=', Request::get('domain'))->first();

        // Create new app, or activate a previously uninstalled app from the same domain
        if ($storage == null)
        {
            $storage = new \Snowfire\App\Storage\AccountStorage;
            $storage->site_url = Request::get('domain');
        }

        $storage->app_key = Request::get('appKey');
        $storage->state = 'INSTALLED';
        $storage->save();

        //Log::info('sfapp/accept', [$_GET, $_POST]);
        return Response::make(Snowfire::response(true), 200, ['content-type' => 'text/xml']);
    }]);

    Route::post('/uninstall', ['as' => 'snowfire.uninstall', function()
    {
        //Log::info('sfapp/uninstall', [$_GET, $_POST]);
        $account = \Snowfire\App\Storage\AccountStorage::whereAppKey(Request::get('appKey'))->first();
        $account->state = 'UNINSTALLED';
        $account->save();

        return Response::make(Snowfire::response(true), 200, ['content-type' => 'text/xml']);
    }]);

    // Admin tab
    Route::get('/tab-proxy', ['as' => 'snowfire.tab', function()
    {
        $accountsRepository = app('Snowfire\App\Repositories\AccountsRepository');
        $appKey = Request::get('snowfireAppKey');
        $app = $accountsRepository->getByKey($appKey);

        if ( ! $app)
        {
            return Response::make('Invalid Snowfire app key', 403);
        }


        Snowfire::login(
            Request::get('snowfireAppKey'),
            Request::get('snowfireUserKey')
        );

        return Redirect::route(Snowfire::parameter('tabRedirectRoute'), [$app->id]);

    }]);

    // Proxy an URL to send a user between core domain and snowfire loaded app
    Route::get('snowfire/proxy/{hash}', ['as' => 'snowfire.proxy', function($hash)
    {
        $proxy = \Snowfire\App\Proxy::getByHash($hash);

        Auth::loginUsingId($proxy->user_id);

        // Save to session, so we know how to redirect back to the app via Snowfire when we're done
        \Snowfire\App\Proxy::saveInSession($hash);

        // Cleanup expired hashes
        \Snowfire\App\Proxy::cleanup();

        return Redirect::to($proxy->to_url);
    }]);

});