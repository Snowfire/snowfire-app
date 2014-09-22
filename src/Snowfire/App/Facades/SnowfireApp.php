<?php namespace Snowfire\App\Facades;

use Illuminate\Support\Facades\Facade;

class SnowfireApp extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'snowfireApp';
	}

}