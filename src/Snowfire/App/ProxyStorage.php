<?php namespace Snowfire\App;

use Carbon\Carbon;

class ProxyStorage extends \Eloquent
{
	protected $table = 'snowfire_proxy';
	protected $dates = ['valid_until'];

	public static function generate($userId, $toUrl, $returnUrl)
	{
		$hash = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));

		$proxy = new static;

		$proxy->user_id = $userId;
		$proxy->return_url = $returnUrl;
		$proxy->to_url = $toUrl;
		$proxy->hash = $hash;
		$proxy->valid_until = Carbon::now()->addHour();
		$proxy->save();

		return $proxy;
	}
}