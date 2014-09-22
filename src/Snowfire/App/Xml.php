<?php namespace Snowfire\App;

class Xml
{
	public static function create($id, $name, $acceptUrl, $uninstallUrl, $tabUrl, $actions = [])
	{
		$xml = new \SimpleXMLElement('<application></application>');
		
		// Application name
		$app = $xml->addChild('app');		
		$app->addChild('id', $id);
		$app->addChild('name', $name);
		
		// Add urls
		$xml->addChild('acceptUrl', $acceptUrl);
		$xml->addChild('uninstallUrl', $uninstallUrl);
		
		// Integration points
		$integration = $xml->addChild('integration');
		$point = $integration->addChild('point');
		$point->addAttribute('type', 'MODULE_TAB');
		$point->addChild('url', $tabUrl);
		
		if (count($actions) > 0) {
			
			$actionsRow = $xml->addChild('actions');
			
			foreach ($actions as $key => $value) {
				$actionRow = $actionsRow->addChild('action');
				$actionRow->addChild('name', $key);
				$actionRow->addChild('url', $value);
			}
			
		}
		
		return $xml->asXML();
	}
	
	/**
	 * Create response
	 *
	 * @param boolean $success
	 * @return XML
	 */
	public function response($success)
	{
		$success = $success ? 'true' : 'false';
		
		$xml = new \SimpleXMLElement('<response></response>');
		$xml->addChild('success', $success);
		
		return $xml->asXML();
	}
}