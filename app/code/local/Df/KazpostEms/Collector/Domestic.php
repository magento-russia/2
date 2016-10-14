<?php
class Df_KazpostEms_Collector_Domestic extends Df_KazpostEms_Collector_Child {
	/**
	 * @override
	 * @see Df_Shipping_Collector::_collect()
	 * @used-by Df_Shipping_Collector_Child::s_collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkCityOrig();
		/** @var int|null $cityIdOrig */
		$cityIdOrig = df_a(Df_KazpostEms_Data_Domestic::$cities, $this->cityOrigUc());
		if (!$cityIdOrig) {
			$this->errorInvalidCityOrig();
		}
		$this->checkCityDest();
		/** @var int|null $cityIdDest */
		$cityIdDest = df_a(Df_KazpostEms_Data_Domestic::$cities, $this->cityDestUc());
		if (!$cityIdDest) {
			$this->errorInvalidCityDest();
		}
		df_assert(isset(Df_KazpostEms_Data_Domestic::$zones[$cityIdOrig][$cityIdDest]));
		$this->addRate($this->choose(
			Df_KazpostEms_Data_Domestic::$_rates
			, Df_KazpostEms_Data_Domestic::$_ratesMore
			, Df_KazpostEms_Data_Domestic::$zones[$cityIdOrig][$cityIdDest]
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