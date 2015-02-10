<?php namespace Snowfire\App;

use Exception;
use Input;
use Response;
use Route;
use Session;
use Request;
use Redirect;
use URL;

class SnowfireApp
{

	private $parameters;

	public function __construct(array $parameters)
	{
		$this->parameters = $parameters;
	}

	public function parameter($key)
	{
		return $this->parameters[$key];
	}

	public function loggedIn()
	{
		$session = Session::get('snowfire_app', []);

		if ($session)
		{
			$app = \Snowfire\App\Storage::
			whereAppKey($session['app_key'])->
			first();

			$session['app'] = $app;
		}

		return $session;
	}

	public function login($appKey, $userKey)
	{
		Session::put('snowfire_app',
			[
				'app_key' => $appKey,
				'user_key' => $userKey,
			]);
	}

	public function getByKey($appKey)
	{
		$storage = \Snowfire\App\Storage::
		whereAppKey($appKey)->
		first();

		return $storage;
	}

	public function currentApp()
	{
		$session = $this->loggedIn();

		if ($session)
		{
			return $session['app'];
		}
		else
		{
			throw new Exception('Not logged in');
		}
	}

	public function authorized($id)
	{
		$session = $this->loggedIn();
		return $session && $session['app']->id == $id;
	}

	public function xml()
	{
		$xml = new \SimpleXMLElement('<application></application>');

		// Application name
		$app = $xml->addChild('app');
		$app->addChild('id', $this->parameters['id']);
		$app->addChild('name', $this->parameters['name']);

		// Add urls
		$xml->addChild('acceptUrl', $this->parameters['acceptUrl']);
		$xml->addChild('uninstallUrl', $this->parameters['uninstallUrl']);

		// Integration points
		if (isset($this->parameters['tabUrl'])) {
			$integration = $xml->addChild('integration');
			$point = $integration->addChild('point');
			$point->addAttribute('type', 'MODULE_TAB');
			$point->addChild('url', $this->parameters['tabUrl']);
		}

		if (count($this->parameters['actions']) > 0) {

			$actionsRow = $xml->addChild('actions');

			foreach ($this->parameters['actions'] as $key => $value) {
				$actionRow = $actionsRow->addChild('action');
				$actionRow->addChild('name', $key);
				$actionRow->addChild('url', route($value));
			}

		}

		$dom = dom_import_simplexml($xml)->ownerDocument;
		$dom->formatOutput = true;
		return $dom->saveXML();
	}

	public function response($success)
	{
		$success = $success ? 'true' : 'false';

		$xml = new \SimpleXMLElement('<response></response>');
		$xml->addChild('success', $success);

		$dom = dom_import_simplexml($xml)->ownerDocument;
		$dom->formatOutput = true;
		return $dom->saveXML();
	}

	public function route($route, $parameters = [])
	{
		if ( ! \Input::has('actionName'))
		{
			// This is not a request from Snowfire, treat it as a normal route
			return URL::route($route, $parameters);
		}

		$actions = $this->parameter('actions');	// From app config
		$currentActionName = Input::get('actionName');
		$currentActionRoute = $actions[$currentActionName];	// "front"
		$toPath = URL::route($route, $parameters, false);	// "/front/auth/login"
		$currentActionPath = URL::route($currentActionRoute, [], false);	// "/front"

		if (preg_match('~^' . $currentActionPath . '(.*)~', $toPath, $matches))
		{
			return Input::get('siteUrl') .
			Input::get('urlPath') .
			$matches[1];
		}

		throw new Exception("Could not match route [{$route}] to current Snowfire action [{$currentActionName}]");
	}

	public static function returnUrl($subPath = null)
	{
		if ($subPath == null)
		{
			$subPath = Input::get('urlSubPath');
		}
		else
		{
			$subPath = '/' . $subPath;
		}


		return Input::get('siteUrl') . Input::get('urlPath') . $subPath;
	}

	public function redirectSnowfireRequest()
	{
		if (Request::header('User-Agent') == 'Snowfire') {
			return Redirect::route('snowfireApp.install');
		}
	}

	public function isRequestFromSnowfire()
	{
		return \Request::header('User-Agent') == 'Snowfire';
	}
}