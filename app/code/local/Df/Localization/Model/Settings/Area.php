<?php
class Df_Localization_Model_Settings_Area extends Df_Core_Model_Settings_Group {
	/** @return string */
	public function allowInterference() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!self::f()
				? Df_Admin_Model_Config_Source_YesNoDev::VALUE__YES
				: $this->getValue('allow_interference');
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isEnabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::f() && $this->getYesNo('enabled', 'rm_translation');
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function needHideDecimals() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::f() && $this->getYesNo('hide_decimals');
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function needSetAsPrimary() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->isEnabled() && $this->getYesNo('set_as_primary', array('rm_translation'))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->addData(array(self::P__SECTION => 'df_localization'));
	}
	/**
	 * @static
	 * @param string $group
	 * @return Df_Localization_Model_Settings_Area
	 */
	public static function i($group) {return new self(array(self::P__GROUP => $group));}

	/** @return bool */
	private static function f() {
		/** @var bool */
		static $result;
		if (!isset($result)) {
			$result = df_enabled(Df_Core_Feature::LOCALIZATION);
		}
		return $result;
	}
}