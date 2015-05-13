<?php
class Df_Core_Helper_Url extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $url
	 * @param string|null $version[optional]
	 * @return string
	 */
	public function addVersionStamp($url, $version = null) {
		return $url . '?v=' . ($version ? $version : rm_version());
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function decode($url) {return $this->processParts(self::RAW_URL_DECODE, $url);}

	/**
	 * @param string $url
	 * @return string
	 */
	public function encode($url) {return $this->processParts(self::RAW_URL_ENCODE, $url);}

	/**
	 * @param string $path
	 * @param bool $relative
	 * @return string
	 */
	public function fromPath($path, $relative = true) {
		return
			($relative ? '' : $this->getBase())
			. $this->encode(df_path()->adjustSlashes(df_path()->makeRelative($path)))
		;
	}

	/** @return string */
	public function getBase() {return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);}

	/**
	 * @param string $string
	 * @return bool
	 */
	public function is($string) {
		/**
		 * @link http://stackoverflow.com/a/15011528/254475
		 * @link http://www.php.net/manual/en/function.filter-var.php
		 * Обратите внимание, что
		 * filter_var('/C/A/CA559AWLE574_1.jpg', FILTER_VALIDATE_URL) вернёт false
		 */
		return false !== filter_var($string, FILTER_VALIDATE_URL);
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function toPath($url) {
		df_param_string($url, 0);
		if (!$this->isInternal($url)) {
			df_error(
				strtr(
					"Метод %method% требует, чтобы его параметр был внутренним для магазина адресом."
					."\nОднако программист передал в качестве параметра адрес «%url%»"
					.", который не является внутренним для магазина адресом"
					,array(
						'%method%' => __METHOD__
						,'%url%' => $url
					)
				)
			);
		}
		return BP . DS . df_path()->adjustSlashes(rawurldecode($this->makeRelative($url)));
	}

	/**
	 * @param string $function
	 * @param string $url
	 * @return string
	 */
	private function processParts($function, $url) {
		df_param_string($url, 0);
		return df_concat_url(array_map($function, explode('/', $url)));
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function makeRelative($url) {
		return
			(-1 >= stripos($url, $this->getBase()))
			? $url
			: df_text()->replaceCI($this->getBase(), '', $url)
		;
	}

	/**
	 * @param string $url
	 * @param string $domain
	 * @return bool
	 */
	public function isUrlBelongsToTheDomain($url, $domain) {
		// Надо запретить распространение лицензии домена на поддомены
		try {
			/** @var Zend_Uri_Http $zendUri */
			$zendUri = Zend_Uri::factory($url);
			$result =
				in_array(
					mb_strtoupper($zendUri->getHost())
					,array(
						mb_strtoupper($domain)
						,mb_strtoupper('www.' . $domain)
					)
				)
			;
		}
		catch(Exception $e) {
			$result = rm_contains($url, $domain, 0, Df_Core_Const::UTF_8);
		}
		return $result;
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	private function isInternal($url) {
		df_param_string($url, 0);
		/** @var Zend_Uri_Http $uri */
		$uri = Zend_Uri_Http::fromString($url);
		/** @var bool $result */
		$result = !$uri->getHost() || rm_contains($url, $this->getBase());
		return $result;
	}

	const _CLASS = __CLASS__;
	const RAW_URL_DECODE = 'rawurldecode';
	const RAW_URL_ENCODE = 'rawurlencode';
	/** @return Df_Core_Helper_Url */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}