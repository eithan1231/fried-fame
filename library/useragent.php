<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\useragent.php
//
// ======================================


/**
* Based off of
* https://github.com/donatj/PhpUserAgent/blob/master/Source/UserAgentParser.php
*/
class useragent
{
  private $m_platform = 'default';
  private $m_browser = 'default';
  private $m_version = 'default';

  function __construct($u_agent = null) {
    if(is_null($u_agent)) {
      throw new Exception('Missing user agent');
  	}

  	if(preg_match('/\((.*?)\)/im', $u_agent, $parent_matches)) {
      preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|(Open|Net|Free)BSD|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS|Switch)|Xbox(\ One)?)
  				(?:\ [^;]*)?
  				(?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);
  		$priority = array( 'Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'FreeBSD', 'NetBSD', 'OpenBSD', 'CrOS', 'X11' );

      if(is_array($result['platform'])) {
        $result['platform'] = array_unique($result['platform']);
    		if(count($result['platform']) > 1) {
    			if($keys = array_intersect($priority, $result['platform'])) {
    				$this->m_platform = reset($keys);
    			} else {
    				$this->m_platform = $result['platform'][0];
    			}
    		} else if(isset($result['platform'][0])) {
    			$this->m_platform = $result['platform'][0];
    		}
      }
  	}

  	if($this->m_platform == 'linux-gnu' || $this->m_platform == 'X11') {
  		$this->m_platform = 'Linux';
  	} else if($this->m_platform == 'CrOS') {
  		$this->m_platform = 'Chrome OS';
  	}

  	preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
  				TizenBrowser|Chrome|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|CriOS|UCBrowser|Puffin|SamsungBrowser|
  				Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
  				Valve\ Steam\ Tenfoot|
  				NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
  				(?:\)?;?)
  				(?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix',
  		$u_agent, $result, PREG_PATTERN_ORDER);

  	// If nothing matched, return null (to avoid undefined index errors)
  	if(!isset($result['browser'][0]) || !isset($result['version'][0])) {
  		if(preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result)) {
  			return;
  		}

  		return;
  	}

  	if(preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $u_agent, $rv_result)) {
  		$rv_result = $rv_result['version'];
  	}

  	$this->m_browser = $result['browser'][0];
  	$this->m_version = $result['version'][0];

  	$lowerBrowser = array_map('strtolower', $result['browser']);

  	$find = function ($search, &$key, &$value = null) use ($lowerBrowser) {
  		$search = (array)$search;

  		foreach($search as $val) {
  			$xkey = array_search(strtolower($val), $lowerBrowser);
  			if($xkey !== false) {
  				$value = $val;
  				$key = $xkey;

  				return;
  			}
  		}

  		return;
  	};

  	$key = 0;
  	$val = '';
  	if($this->m_browser == 'Iceweasel' || strtolower($this->m_browser) == 'icecat') {
  		$this->m_browser = 'Firefox';
  	} else if($find('Playstation Vita', $key)) {
  		$this->m_platform = 'PlayStation Vita';
  		$this->m_browser  = 'Browser';
  	} else if($find(array('Kindle Fire', 'Silk'), $key, $val)) {
  		$this->m_browser  = $val == 'Silk' ? 'Silk' : 'Kindle';
  		$this->m_platform = 'Kindle Fire';
  		if(!($this->m_version = $result['version'][$key]) || !is_numeric($this->m_version[0])) {
  			$this->m_version = $result['version'][array_search('Version', $result['browser'])];
  		}
  	} else if($find('NintendoBrowser', $key) || $this->m_platform == 'Nintendo 3DS') {
  		$this->m_browser = 'NintendoBrowser';
  		$this->m_version = $result['version'][$key];
  	} else if($find('Kindle', $key, $this->m_platform)) {
  		$this->m_browser = $result['browser'][$key];
  		$this->m_version = $result['version'][$key];
  	} else if($find('OPR', $key)) {
  		$this->m_browser = 'Opera Next';
  		$this->m_version = $result['version'][$key];
  	} else if($find('Opera', $key, $this->m_browser)) {
  		$find('Version', $key);
  		$this->m_version = $result['version'][$key];
  	} else if($find('Puffin', $key, $this->m_browser)) {
  		$this->m_version = $result['version'][$key];
  		if(strlen($this->m_version) > 3) {
  			$part = substr($this->m_version, -2);
  			if(ctype_upper($part)) {
  				$this->m_version = substr($this->m_version, 0, -2);

  				$flags = array('IP' => 'iPhone', 'IT' => 'iPad', 'AP' => 'Android', 'AT' => 'Android', 'WP' => 'Windows Phone', 'WT' => 'Windows');
  				if(isset($flags[$part])) {
  					$this->m_platform = $flags[$part];
  				}
  			}
  		}
  	} else if($find(array('IEMobile', 'Edge', 'Midori', 'Vivaldi', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome'), $key, $this->m_browser)) {
  		$this->m_version = $result['version'][$key];
  	} else if($rv_result && $find('Trident', $key)) {
  		$this->m_browser = 'MSIE';
  		$this->m_version = $rv_result;
  	} else if($find('UCBrowser', $key)) {
  		$this->m_browser = 'UC Browser';
  		$this->m_version = $result['version'][$key];
  	} else if($find('CriOS', $key)) {
  		$this->m_browser = 'Chrome';
  		$this->m_version = $result['version'][$key];
  	} else if($this->m_browser == 'AppleWebKit') {
  		if($this->m_platform == 'Android' && !($key = 0)) {
  			$this->m_browser = 'Android Browser';
  		} else if(strpos($this->m_platform, 'BB') === 0) {
  			$this->m_browser  = 'BlackBerry Browser';
  			$this->m_platform = 'BlackBerry';
  		} else if($this->m_platform == 'BlackBerry' || $this->m_platform == 'PlayBook') {
  			$this->m_browser = 'BlackBerry Browser';
  		} else {
  			$find('Safari', $key, $this->m_browser) || $find('TizenBrowser', $key, $this->m_browser);
  		}

  		$find('Version', $key);
  		$this->m_version = $result['version'][$key];
  	} else if($pKey = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser']))) {
  		$pKey = reset($pKey);

  		$this->m_platform = 'PlayStation '. preg_replace('/[^\d]/i', '', $pKey);
  		$this->m_browser  = 'NetFront';
  	}
  }

  /**
  * Gets the platform of the useragent
  */
  public function getPlatform()
  {
    return $this->m_platform;
  }

  /**
  * Gets the browser of the useragent
  */
  public function getBrowser()
  {
    return $this->m_browser;
  }

  /**
  * Gets the version of the browser (?)
  */
  public function getVersion()
  {
    return $this->m_version;
  }
}
