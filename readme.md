## Snowfire App

This packages makes it possible to connect your Laravel app to Snowfire.

## Install the package

Add this to your composer.json

	"snowfire/snowfire-app": "1.*"

Add this to your service providers in `app.php`

    'Snowfire\App\AppServiceProvider'

Create the database table for snowfire installations

    $ php artisan migrate --package="snowfire/snowfire-app"

Publish the config file

    $ php artisan config:publish snowfire/snowfire-app

## Integration possibilities

There are two different ways to connect your app to Snowfire. As a link in the admin area and as a public action.

### Example

Lets say you have a list of events. A public action will be something like `http://your-app.com/events/all` which will render an HTML `<ul>` list. Then you will have an admin link from Snowfire to `http://your-app.com/admin` which will let users add/edit/remove events.

Start by adding your actions to `/app/config/packages/snowfire/snowfire-app/snowfire_app.php`

```php
return [
	'id' => 'demo_app',
	'name' => 'Demo app',
	'tab' => 'admin',
	'actions' => [
		'events' => 'action.events.index',
		'event' => 'action.events.show',
	]
];
```

This config adds the admin link as a named route called `snowfire.tab` and a route for all events. Both tab and actions are optional (but you need one of them, right?)

#### Your `routes.php`

```php

// Admin routes
Route::group(['prefix' => 'admin/{snowfireAppId}', 'before' => 'snowfireAppAuth', 'namespace' => 'Admin'], function()
{
	Route::get('/', ['as' => 'admin.index', function($appId)
    {
        return 'Admin for appId: ' . $appId;
    }]);

});

// Action routes
Route::group(['prefix' => 'action', 'namespace' => 'Front'], function()
{
	Route::get('events', ['uses' => 'EventsController@index', 'as' => 'action.events.index']);
	Route::get('events/{id}', ['uses' => 'EventsController@event', 'as' => 'action.events.show']);
});
```

This creates an admin route and the public action. The admin route is behind a snowfireAppAuth filter which makes sure the user is logged in and trusted.

### Your Snowfire snippet to the public event list

Login to Snowfire and install the app (System -> Apps)

	http://your-hosted-app.com/snowfire/install

Create a new snippet with this code:

```javascript
<?xml version="1.0" encoding="utf-8"?>
<snippet>
    <name>All events</name>
    <html><![CDATA[
		{ com_application (
			id: '{{ component_id_0 }}',
			app: 'demo_app',
			action: 'events',
			cache: '0',
			paramUrl: 'true',
			paramKey: 'true',
			paramUrlNoDomain: 'true'
		) }
	]]></html>
</snippet>
```

Now just add the snippet to a page and it will show you a list of events.