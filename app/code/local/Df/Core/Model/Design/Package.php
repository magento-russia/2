<?php
class Df_Core_Model_Design_Package extends Df_Core_Model {
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

	/** @return array(string => mixed) */
	private function getPackageConfig() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !$this->isCustom() ? array() : rm_config_a(
				'rm/design/package', rm_design_package()->getPackageName()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $paramName
	 * @param mixed $defaultValue [optional]
	 * @return string
	 */
	private function getPackageConfigParam($paramName, $defaultValue = null) {
		return dfa($this->getPackageConfig(), $paramName, $defaultValue);
	}

	const _C = __CLASS__;
	const PACKAGE_PARAM__DEFAULT_ROUTE = 'default-route';
	const PACKAGE_PARAM__VERSION = 'version';

	/** @return Df_Core_Model_Design_Package */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}