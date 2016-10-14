<?php
abstract class Df_Core_Model_Geo_Locator_Real extends Df_Core_Model_Geo_Locator {
	/** @return string */
	abstract protected function getConverter();

	/** @return string */
	abstract protected function getUrlTemplate();

	/**
	 * @param string $text
	 * @return array(string => mixed)
	 */
	protected function convertJson($text) {return df_nta(json_decode($text, $assoc = true));}

	/**
	 * @param string $text
	 * @return array(string => mixed)
	 */
	protected function convertXml($text) {
		/** @var Df_Core_Sxe|null $xml */
		$xml = rm_xml($text, $throw = false);
		return !$xml ? array() : $xml->asCanonicalArray();
	}

	/** @return string */
	protected function getCacheKey() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode('::', array(get_class($this) , $this->getIpAddress()));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getPathBase() {return '';}

	/**
	 * @param string $path
	 * @return string|null
	 */
	protected function getProperty($path) {
		if (!isset($this->{__METHOD__}[$path])) {
			/** @var string|null $result */
			$result = $this->loadFromCache($path);
			if (!$result) {
				$result = $this->getPropertyInternal($path);
				// Обратите внимание, что в долгосрочный кэш попадает только успешный результат.
				// Кэшировать нули не только нет смысла, но и опасно:
				// ведь тогда у нас не будет стимула для нового обращения к API сервера.
				if ($result) {
					$this->saveToCache($path, $result);
				}
			}
			$this->{__METHOD__}[$path] = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__}[$path]);
	}

	/**
	 * @param string $path
	 * @return string|null
	 */
	protected function getPropertyInternal($path) {return $this->queryArray($path);}

	/** @return array(string => mixed) */
	protected function getResponseAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => mixed) $result */
			$result = array();
			if ($this->getResponse()) {
				try {
					$result = call_user_func(array($this, $this->getConverter()), $this->getResponse());
				}
				catch (Exception $e) {}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	protected function getUrlParamsAdditional() {return array();}

	/**
	 * @param string $propertyName
	 * @return string|null
	 */
	protected function loadFromCache($propertyName) {
		return df_a_deep(
			Df_Core_Model_Geo_Cache::s()->cache, df_concat_xpath($this->getCacheKey(), $propertyName)
		);
	}

	/**
	 * @param string $pathRelative
	 * @return string|null
	 */
	protected function queryArray($pathRelative) {
		return df_a_deep($this->getResponseAsArray(), $this->getPathFull($pathRelative));
	}

	/**
	 * @param string $propertyName
	 * @param string $value
	 * @return void
	 */
	protected function saveToCache($propertyName, $value) {
		Df_Core_Model_Geo_Cache::s()->cache[$this->getCacheKey()][$propertyName] = $value;
	}

	/**
	 * @param string $pathRelative
	 * @return string
	 */
	private function getPathFull($pathRelative) {
		return df_concat_xpath(df_clean(array($this->getPathBase(), $pathRelative)));
	}

	/** @return string|null */
	private function getResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(df_ftn(@file_get_contents($this->getUrl())));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string */
	private function getUrl() {return strtr($this->getUrlTemplate(), $this->getUrlParams());}

	/** @return array(string => string) */
	private function getUrlParams() {
		return $this->getUrlParamsAdditional() + array('{ip}' => $this->getIpAddress());
	}
}


