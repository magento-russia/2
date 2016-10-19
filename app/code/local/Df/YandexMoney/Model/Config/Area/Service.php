<?php
/**
 * Не перекрываем метод @see Df_Payment_Config_Area_Service::getShopId()
 * ради дополнительной валидации,
 * потому что цифр в номере счёта Яндекс.Денег может быть не только 14:
 * http://magento-forum.ru/topic/4315/
 */
class Df_YandexMoney_Model_Config_Area_Service extends Df_Payment_Config_Area_Service {
	/** @return string */
	public function getAppId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_decrypt($this->getVar('app_id'));
			df_assert_eq(64, strlen($this->{__METHOD__}));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getAppPassword() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_decrypt($this->getVar('app_password'));
			df_assert_eq(128, strlen($this->{__METHOD__}));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function descriptionForShop() {return
		$this->getVar('transaction_description_for_shop', '')
	;}

	/** @return string */
	public function getTransactionTag() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_substr($this->getVar('transaction_tag', ''), 0, 64);
		}
		return $this->{__METHOD__};
	}
}