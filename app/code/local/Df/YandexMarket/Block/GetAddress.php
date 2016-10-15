<?php
class Df_YandexMarket_Block_GetAddress extends Df_Core_Block_Template {
	/** @return string */
	public function getRedirectUrl() {
		return df_cc(
			'http://market.yandex.ru/addresses.xml?callback='
			,rawurlencode(Mage::getUrl('df-yandex-market/address/'))
		);
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/yandex_market/getAddress.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		return
			df_cfg()->checkout()->other()->canGetAddressFromYandexMarket()
			&& !df_customer_logged_in()
		;
	}
}