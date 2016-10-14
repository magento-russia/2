<?php
abstract class Df_Vk_Model_Settings_Widget extends Df_Core_Model_Settings {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getWidgetType();

	/** @return string|null */
	public function getCode() {
		/** @var string|null $result */
		$result = Mage::getStoreConfig($this->getConfigKey('code'));
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return boolean */
	public function getEnabled() {
		return $this->getYesNo($this->getConfigKey('enabled'));
	}

	/**
	 * @param string $configKeyShort
	 * @return string
	 */
	private function getConfigKey($configKeyShort) {
		return df_concat_xpath('df_vk', $this->getWidgetType(), $configKeyShort);
	}
}