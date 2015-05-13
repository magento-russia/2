<?php
class Df_Core_Model_Design_Package extends Df_Core_Model_Abstract {
	/** @return string|null */
	public function getDefaultRoute() {
		return $this->getPackageConfigParam(self::PACKAGE_PARAM__DEFAULT_ROUTE);
	}

	/** @return string */
	public function getName() {return rm_design_package()->getPackageName();}

	/** @return bool */
	public function hasConfiguration() {return !!$this->getPackageConfig();}

	/** @return bool */
	public function isCustom() {return 'default' !== $this->getName();}

	/** @return string|null */
	public function getVersion() {return $this->getPackageConfigParam(self::PACKAGE_PARAM__VERSION);}

	/** @return mixed[] */
	private function getPackageConfig() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $result  */
			$result = array();
			if ($this->isCustom()) {
				/** @var Mage_Core_Model_Config_Element|bool $configNode */
				$configNode =
					Mage::getConfig()->getNode(
						rm_config_key('rm/design/package', rm_design_package()->getPackageName())
					)
				;
				if ($configNode) {
					$result = $configNode->asCanonicalArray();
				}
			}
			/**
			 * Varien_Simplexml_Element::asCanonicalArray может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $paramName
	 * @param mixed $defaultValue[optional]
	 * @return string
	 */
	private function getPackageConfigParam($paramName, $defaultValue = null) {
		return df_a($this->getPackageConfig(), $paramName, $defaultValue);
	}

	const _CLASS = __CLASS__;
	const PACKAGE_PARAM__DEFAULT_ROUTE = 'default-route';
	const PACKAGE_PARAM__VERSION = 'version';

	/** @return Df_Core_Model_Design_Package */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}