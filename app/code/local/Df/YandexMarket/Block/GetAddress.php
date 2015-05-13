<?php
class Df_YandexMarket_Block_GetAddress extends Df_Core_Block_Template {
	/** @return string */
	public function getRedirectUrl() {
		return
			rm_sprintf(
				'http://market.yandex.ru/addresses.xml?callback=%s'
				,rawurlencode(Mage::getUrl('df-yandex-market/address/'))
			)
		;
	}
	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/yandex_market/getAddress.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		return
				df_enabled(Df_Core_Feature::YANDEX_MARKET)
			&&
				df_cfg()->checkout()->other()->canGetAddressFromYandexMarket()
			&&
				!df_mage()->customer()->isLoggedIn()
		;
	}
	const _CLASS = __CLASS__;
}