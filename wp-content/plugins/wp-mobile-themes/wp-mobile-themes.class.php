<?php

class WPMobileThemes {

	private static $mobileTheme;

	function WPMobileThemes($mobileTheme) {
		if($this->isMobile()) {
			$this->mobileTheme = $mobileTheme;
			add_filter('stylesheet', array(&$this, 'getMobileStylesheet'));
			add_filter('template', array(&$this, 'getMobileTemplate'));
		}
	}
	
	public function isMobile() {
		// get agent
		$container = $_SERVER['HTTP_USER_AGENT'];

		// get mobile agents and excluded agents
		$mobileUserAgents = $this->getMobileUserAgents();
		$excludedUserAgents = $this->getExcludedUserAgents();

		// if it's excluded agent, return false
		foreach($excludedUserAgents as $agent) {
			if(preg_match("#$agent#i", $container)) {	
				return false;
			}
		}

		// if it's mobile agent, return true
		foreach($mobileUserAgents as $agent) {
			if(preg_match("#$agent#i", $container )) {
				return true;
			}
		}
	}

	public function getExcludedUserAgents() {
		$userAgents = array(
			'SCH-I800',
			'Xoom'	
		);
		
		return $userAgents;
	}

	public function getMobileUserAgents() {
		$userAgents = array(
			"iPhone",  				// Apple iPhone
			"iPod", 				// Apple iPod touch
			"incognito", 			// Other iPhone browser
			"webmate", 				// Other iPhone browser
			"Android", 			 	// 1.5+ Android
			"dream",				// Pre 1.5 Android
			"CUPCAKE", 			 	// 1.5+ Android
			"blackberry9500",	 	// Storm
			"blackberry9530",	 	// Storm
			"blackberry9520",	 	// Storm v2
			"blackberry9550",	 	// Storm v2
			"blackberry 9800",		// Torch
			"webOS",				// Palm Pre Experimental
			"s8000", 				// Samsung Dolphin browser
			"bada",				 	// Samsung Dolphin browser
			"Googlebot-Mobile"		// the Google mobile crawler
		);
		
		return $userAgents;
	}

	public function getMobileTemplate() {
		$theme = $this->mobileTheme;

		if (empty($theme)) {
			return $template;
		}

		$theme = get_theme($theme);
		
		if (empty($theme)) {
			return $template;
		}

		// Don't let people peek at unpublished themes.
		if (isset($theme['Status']) && $theme['Status'] != 'publish')
			return $template;		

		return $theme['Template'];
	}

	public function getMobileStylesheet() {
		$theme = $this->mobileTheme;

		if (empty($theme)) {
			return $stylesheet;
		}

		$theme = get_theme($theme);

		// Don't let people peek at unpublished themes.
		if (isset($theme['Status']) && $theme['Status'] != 'publish')
			return $template;		
		
		if (empty($theme)) {
			return $stylesheet;
		}

		return $theme['Stylesheet'];
	}
}

?>