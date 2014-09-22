<?php

Route::filter('snowfireAppAuth', function($route)
{
	$id = $route->getParameter('snowfireAppId');

	if (!SnowfireApp::authorized($id))
	{
		$app = \Snowfire\App\Storage::find($id);

		if ($app)
		{
			return Redirect::to($app->site_url . 'a;applications/application/moduleTab/' . SnowfireApp::parameter('id'));
		}
		else
		{
			return Response::make('Not authorized, please login again through Snowfire', 403);
		}
	}
});

Route::get('snowfire/install', ['as' => 'snowfireApp.install', function()
{
	return Response::make(SnowfireApp::xml(), 200, ['content-type' => 'text/xml']);
}]);

Route::post('snowfire/accept', ['as' => 'snowfireApp.accept', function()
{
	$storage = \Snowfire\App\Storage::where('site_url', '=', Request::get('domain'))->first();

	// Create new app, or activate a previously uninstalled app from the same domain
	if ($storage == null)
	{
		$storage = new \Snowfire\App\Storage;
		$storage->site_url = Request::get('domain');
	}

	$storage->app_key = Request::get('appKey');
	$storage->state = 'INSTALLED';
	$storage->save();

	//Log::info('sfapp/accept', [$_GET, $_POST]);
	return Response::make(SnowfireApp::response(true), 200, ['content-type' => 'text/xml']);
}]);

Route::post('snowfire/uninstall', ['as' => 'snowfireApp.uninstall', function()
{
	//Log::info('sfapp/uninstall', [$_GET, $_POST]);
	$account = \Snowfire\App\Storage::whereAppKey(Request::get('appKey'))->first();
	$account->state = 'UNINSTALLED';
	$account->save();

	return Response::make(SnowfireApp::response(true), 200, ['content-type' => 'text/xml']);
}]);

// Admin tab
Route::get('snowfire/tab-proxy', ['as' => 'snowfireApp.tab', function()
{
	$appKey = Request::get('snowfireAppKey');
	$app = SnowfireApp::getByKey($appKey);

	if ( ! $app)
	{
		return Response::make('Invalid Snowfire app key', 403);
	}


	SnowfireApp::login(
		Request::get('snowfireAppKey'),
		Request::get('snowfireUserKey')
	);

	return Redirect::route(SnowfireApp::parameter('tabRedirectRoute'), [$app->id]);

}]);

// Proxy an URL to send a user between core domain and snowfire loaded app
Route::get('snowfire/proxy/{hash}', ['as' => 'snowfireApp.proxy', function($hash)
{
	$proxy = \Snowfire\App\Proxy::getByHash($hash);

	Auth::loginUsingId($proxy->user_id);

	// Save to session, so we know how to redirect back to the app via Snowfire when we're done
	\Snowfire\App\Proxy::saveInSession($hash);

	// Cleanup expired hashes
	\Snowfire\App\Proxy::cleanup();

	return Redirect::to($proxy->to_url);
}]);