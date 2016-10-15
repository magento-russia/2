<?php
class Df_YandexMarket_Model_Config_Backend_Api_ConfirmationCode
	extends Df_Admin_Config_Backend {
	/**
	 * @overide
	 * @return Df_YandexMarket_Model_Config_Backend_Api_ConfirmationCode
	 */
	protected function _beforeSave() {
		try {
			/** @var string $previousValue */
			$previousValue = df_cfg()->yandexMarket()->api()->getConfirmationCode();
			if ($this->getValue() && ($this->getValue() !== $previousValue)) {
				/** @var Mage_Core_Model_Config $config */
				$config = new Mage_Core_Model_Config();
				$config->saveConfig(
					$path = 'df_yandex_market/api/token'
					,$value = $this->requestToken()
					,$scope = 'default'
				);
			}
		}
		catch (Exception $e) {
			df_exception_to_session($e);
		}
		parent::_beforeSave();
		return $this;
	}

	/** @return string */
	private function requestToken() {
		return Df_YandexMarket_Model_OAuth::i(
			df_cfg()->yandexMarket()->api()->getApplicationId()
			, df_cfg()->yandexMarket()->api()->getApplicationPassword()
			, $this->getValue()
		)->getToken();
	}

	
}