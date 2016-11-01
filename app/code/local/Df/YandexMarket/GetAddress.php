<?php
// 2016-11-01
namespace Df\YandexMarket;
use Df_Checkout_Model_Settings_Other as S;
class GetAddress {
	/**     
	 * 2016-11-01
	 * @return string
	 */
	public static function r() {return 
		!S::s()->canGetAddressFromYandexMarket() || df_customer_logged_in() ? '' : 
			df_tag('a', [
				'class' => 'df-yandex-market-address'
				,'href' =>
					'http://market.yandex.ru/addresses.xml?callback='
					. rawurlencode(\Mage::getUrl('df-yandex-market/address/'))
				
			], df_tag('img', [
				'border' => 0
				,'src' => \Mage::getDesign()->getSkinUrl('df/images/yandex-market/button-address.png')
			]))
	;}
}