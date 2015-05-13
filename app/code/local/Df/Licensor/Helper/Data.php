<?php
class Df_Licensor_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Licensor_Model_Context */
	public function getContext() {return Df_Licensor_Model_Context::s();}

	/** @return string */
	public function getDateFormat() {return self::DATE_FORMAT;}

	/**
	 * @param string $code
	 * @return Df_Licensor_Model_Feature
	 */
	public function getFeatureByCode($code) {
		df_param_string_not_empty($code, 0);
		/** @var Df_Licensor_Model_Feature $result */
		$result = $this->getFeatures()->getItemById($code);
		if (!$result) {
			$result = Df_Licensor_Model_Feature::i($code);
			$this->getFeatures()->addItem($result);
		}
		return $result;
	}

	/**
	 * @param Df_Licensor_Model_Feature $feature
	 * @return Df_Licensor_Model_Feature_Info
	 */
	public function getFeatureInfo(Df_Licensor_Model_Feature $feature) {
		$result = $this->getFeatureInfoCollection()->getItemById($feature->getId());
		/** @var Df_Licensor_Model_Feature_Info $result */
		if (!$result) {
			$result = Df_Licensor_Model_Feature_Info::i($feature);
			$this->getFeatureInfoCollection()->addItem($result);
		}
		return $result;
	}

	/** @return Df_Licensor_Model_Collection_License */
	public function getLicenses() {
		return Df_Licensor_Model_Collection_License::s();
	}

	/** @return Df_Licensor_Model_Collection_Store */
	public function getStores() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Licensor_Model_Collection_Store::i();
			$this->{__METHOD__}->loadAll();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Licensor_Model_Feature */
	public function getSuperFeature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getFeatureByCode(Df_Core_Feature::ALL);
			df_assert(true === $this->{__METHOD__}->isSuper());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $dateExpirationAsString
	 * @return Zend_Date
	 */
	public function parseDateExpiration($dateExpirationAsString) {
		/** @var Zend_Date $result */
		if (
				!$dateExpirationAsString
			||
				('2050-01-01' === $dateExpirationAsString)
		) {
			$result = df()->date()->getUnlimited();
		}
		else {
			/** @var string $timezone */
			$timezone = date_default_timezone_get();
			date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
			$result = new Zend_Date($dateExpirationAsString, $this->getDateFormat());
			/** @var Zend_Date $result */
			date_default_timezone_set($timezone);
		}
		return $result;
	}

	/** @return Df_Licensor_Model_Collection_Feature */
	private function getFeatures() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Licensor_Model_Collection_Feature::i();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Licensor_Model_Feature_Info_Collection */
	private function getFeatureInfoCollection() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Licensor_Model_Feature_Info_Collection::i();
		}
		return $this->{__METHOD__};
	}

	// Формат даты в лицензии
	const DATE_FORMAT = "yyyy-MM-dd";

	/** @return Df_Licensor_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}