<?php
class Df_Core_Model_Geo_Locator_Multi extends Df_Core_Model_Geo_Locator {
	/** @return string|null */
	public function getCity() {return $this->serverIpgeobaseRu()->getCity();}

	/** @return string|null */
	public function getCountryIso2() {return $this->serverIpgeobaseRu()->getCountryIso2();}

	/** @return string|null */
	public function getRegionName() {return $this->serverIpgeobaseRu()->getRegionName();}

	/** @return string|null */
	private function getLatude() {
		/** @var string|null */
		/** @var string|null */
		$result = $this->serverIpgeobaseRu()->getLatude();
		return $result ? $result : $this->serverFreegeoipNet()->getLatude();
	}

	/** @return string|null */
	private function getLongitude() {
		/** @var string|null */
		$result = $this->serverIpgeobaseRu()->getLongitude();
		return $result ? $result : $this->serverFreegeoipNet()->getLongitude();
	}

	/** @return Df_Core_Model_Geo_Locator_Real_FreegeoipNet */
	private function serverFreegeoipNet() {
		return Df_Core_Model_Geo_Locator_Real_FreegeoipNet::s($this->getIpAddress());
	}

	/** @return Df_Core_Model_Geo_Locator_Real_IpgeobaseRu */
	private function serverIpgeobaseRu() {
		return Df_Core_Model_Geo_Locator_Real_IpgeobaseRu::s($this->getIpAddress());
	}

	/**
	 * @param string $ipAddress
	 * @return Df_Core_Model_Geo_Locator_Multi
	 */
	public static function s($ipAddress) {return self::sc(__CLASS__, $ipAddress);}
}