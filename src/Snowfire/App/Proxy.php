<?php namespace Snowfire\App;

use Carbon\Carbon;
use Exception;
use Session;

class Proxy {

	public static function getByHash($hash)
	{
		$proxy = \Snowfire\App\ProxyStorage::where('hash', $hash)->first();

		if ( ! $proxy)
		{
			throw new \Exception('Invalid proxy hash');
		}

		return $proxy;
	}

	public static function saveInSession($hash)
	{
		Session::set('snowfireProxyHash', $hash);
	}

	/**
	 * Create a temporary path to transfer a user to my core app domain (outside Snowfire) with the ability
	 * to return, keeping the auth
	 */
	public static function createHash($toUrl)
	{
		$returnUrl = \SnowfireApp::returnUrl();
		$proxy = ProxyStorage::generate(\Auth::user()->id, $toUrl, $returnUrl);

		return $proxy->hash;
	}

	/**
	 * This method allows us to return from our core domain (outside Snowfire) back to Snowfire
	 */
	public static function getReturnUrlFromSession()
	{
		if ( ! Session::has('snowfireProxyHash'))
		{
			throw new Exception('No snowfireProxyHash found in session. Impossible to return to Snowfire.');
		}

		$proxy = ProxyStorage::where('hash', Session::get('snowfireProxyHash'))->first();

		return $proxy->return_url;
	}

	/**
	 * Remove expired proxy hash links (1 day)
	 */
	public static function cleanup()
	{
		ProxyStorage::where('valid_until', '<', Carbon::now())->delete();
	}
}