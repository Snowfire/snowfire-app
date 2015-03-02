<?php namespace Snowfire\App\Facades;

use Illuminate\Support\Facades\Facade;

class Snowfire extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'snowfire';
	}

}