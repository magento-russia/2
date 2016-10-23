<?php
class Df_KazpostEms_Collector_Foreign extends Df_KazpostEms_Collector_Child {
	/**
	 * @override
	 * @see Df_Shipping_Collector::_collect()
	 * @used-by Df_Shipping_Collector_Child::s_collect()
	 * @return void
	 */
	protected function _collect() {
		/** @var int|null $zone */
		$zone = dfa(Df_KazpostEms_Data_Foreign::$countries, $this->countryDestUc());
		if (is_null($zone)) {
			$this->errorInvalidCountryDest();
		}
		$this->addRate($this->choose(
			Df_KazpostEms_Data_Foreign::$_rates, Df_KazpostEms_Data_Foreign::$_ratesMore, $zone
		));
	}

	/**
	 * @override
	 * @see Df_Shipping_Collector::feeFixed()
	 * @used-by Df_Shipping_Collector::addRate()
	 * «Заказное уведомление EMS отправлений: 400»
	 * http://www.kazpost.kz/uploads/content/files/УСЛУГИ%20УСКОРЕННОЙ%20И%20КУРЬЕРСКОЙ%20ПОЧТЫ.docx
	 * @return int|float
	 */
	protected function feeFixed() {return 400;}
}