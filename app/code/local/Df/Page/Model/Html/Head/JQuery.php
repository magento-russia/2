<?php
class Df_Page_Model_Html_Head_JQuery extends Df_Core_Model {
	/**
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	public function process($format, array &$staticItems) {
		return $this->getProcessor() ? $this->getProcessor()->process($format, $staticItems) : '';
	}

	/** @return Df_Page_Model_Html_Head_JQuery_Abstract|null */
	private function getProcessor() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed|null $result */
			$result = null;
			$this->{__METHOD__} = rm_n_set(
				$this->needLoadFromLocal()
				? Df_Page_Model_Html_Head_JQuery_Local::s()
				: (
					$this->needLoadFromGoogle()
					? Df_Page_Model_Html_Head_JQuery_Google::s()
					: null
				)
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Core_Model_Settings_Jquery */
	private function getSettings() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_is_admin()
				? df_cfg()->admin()->jquery()
				: df_cfg()->tweaks()->jquery()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needLoad() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->needLoadFromLocal() || $this->needLoadFromGoogle();
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needLoadFromGoogle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					Df_Admin_Model_Config_Source_JqueryLoadMode::VALUE__LOAD_FROM_GOOGLE
				===
					$this->getSettings()->getLoadMode()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needLoadFromLocal() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					Df_Admin_Model_Config_Source_JqueryLoadMode::VALUE__LOAD_FROM_LOCAL
				===
					$this->getSettings()->getLoadMode()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Page_Model_Html_Head_JQuery */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}