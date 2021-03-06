<?php
class Df_Core_Helper_Version extends Mage_Core_Helper_Abstract {
	/** @return bool */
	public function isItAdmin() {return $this->isLicensorGeneratorExist();}

	/** @return bool */
	public function isItFree() {return !$this->isLicensorExist();}

	/** @return bool */
	public function isLicensorEncoded() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result = false;
			if ($this->isLicensorExist()) {
				/** @var string $pathToLicensorFile */
				$pathToLicensorFile = BP . DS . 'app/code/local/Df/Licensor/Model/License.php';
				df_assert(file_exists($pathToLicensorFile));
				/** @var bool $result */
				$result =
						false
					!==
						strpos(file_get_contents($pathToLicensorFile), base64_decode('SFIrYw=='))
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isCommunityEdition() {
		return !$this->isEnterpriseEdition() && !$this->isProfessionalEdition();
	}

	/** @return bool */
	private function isEnterpriseEdition() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')
				&&
					Mage::getConfig()->getModuleConfig('Enterprise_AdminGws')
				&&
					Mage::getConfig()->getModuleConfig('Enterprise_Checkout')
				&&
					Mage::getConfig()->getModuleConfig('Enterprise_Customer')
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isProfessionalEdition() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')
				&&
					!Mage::getConfig()->getModuleConfig('Enterprise_AdminGws')
				&&
					!Mage::getConfig()->getModuleConfig('Enterprise_Checkout')
				&&
					!Mage::getConfig()->getModuleConfig('Enterprise_Customer')
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string|null $param1[optional]
	 * @param string|null $param2[optional]
	 * @return string|boolean
	 */
	public function magentoVersion($param1 = null, $param2 = null) {
		if (!isset($this->{__METHOD__}[$param1][$param2])) {
			$this->{__METHOD__}[$param1][$param2] =
				is_null($param1)
				? Mage::getVersion()
				: (
					is_null($param2)
					? version_compare($this->getVersionTranslated(), $param1, '==')
					: (
						!$this->hasDigits($param2)
						?
							// $param2 is operator, not version
							version_compare($this->getVersionTranslated(), $param1, $param2)
						:
								version_compare(
									$this->getVersionTranslated()
									,$param1
									,">="
								)
							&&
								version_compare(
									$this->getVersionTranslated()
									,$param2
									,"<="
								)
					)
				)
			;
		}
		return $this->{__METHOD__}[$param1][$param2];
	}

	/**
	 * @param string $version
	 * @return string
	 */
	private function convertVersionToCommunityEdition($version) {
		/** @var string $result */
		$result =
			$this->isCommunityEdition()
			? $version
			: $this->translate(
				$version
				,$this->isEnterpriseEdition()
				? array(
					'1.13.0' => '1.8.0.0'
					,'1.12.0' => '1.7.0.0'
					,'1.11.0' => '1.6.0'
					,'1.9.1' => '1.5.0'
					,'1.9.0' => '1.4.2'
					,'1.8.0' => '1.3.1'
				)
				: array(
					'1.8.0' => '1.4.1'
					,'1.7.0' => '1.3.1'
				)
			)
		;
		return $result;
	}
	
	/** @return string */
	private function getVersionTranslated() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->convertVersionToCommunityEdition(Mage::getVersion());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $string
	 * @return int
	 */
	private function hasDigits($string) {return 1 === preg_match('#\d#', $string);}

	/** @return bool */
	private function isLicensorExist() {return df_module_enabled(Df_Core_Module::LICENSOR);}

	/** @return bool */
	private function isLicensorGeneratorExist() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					@class_exists('Dfa_LicensorGenerator_Model_License_Generator')
				&&
					df_module_enabled(Df_Core_Module::LICENSOR_GENERATOR)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $originalVersion
	 * @param array(string => string) $map
	 * @return string
	 */
	private function translate($originalVersion, array $map) {
		/** @var string $result */
		$result = rm_last($map);
		foreach ($map as $currentOriginalVersion => $currentCommunityVersion) {
			/** @var string $currentOriginalVersion */
			/** @var string $currentCommunityVersion */
			if (version_compare($originalVersion, $currentOriginalVersion, '>=')) {
				$result = $currentCommunityVersion;
				break;
			}
		}
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return Df_Core_Helper_Version */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}