<?php
namespace Df\KazpostEms\Collector;
use Df\KazpostEms\Data\Domestic as D;
class Domestic extends Child {
	/**
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector\Child::s_collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkCityOrig();
		/** @var int|null $cityIdOrig */
		$cityIdOrig = dfa(D::$cities, $this->oCityUc());
		if (!$cityIdOrig) {
			$this->errorInvalidCityOrig();
		}
		$this->checkCityDest();
		/** @var int|null $cityIdDest */
		$cityIdDest = dfa(D::$cities, $this->dCityUc());
		if (!$cityIdDest) {
			$this->errorInvalidCityDest();
		}
		df_assert(isset(D::$zones[$cityIdOrig][$cityIdDest]));
		$this->rate($this->choose(D::$_rates, D::$_ratesMore, D::$zones[$cityIdOrig][$cityIdDest]));
	}

	/**
	 * @override
	 * @see \Df\Shipping\Collector::feeFixed()
	 * @used-by \Df\Shipping\Collector::rate()
	 * «Заказное уведомление EMS отправлений: 400»
	 * http://www.kazpost.kz/uploads/content/files/УСЛУГИ%20УСКОРЕННОЙ%20И%20КУРЬЕРСКОЙ%20ПОЧТЫ.docx
	 * @return int|float
	 */
	protected function feeFixed() {return 400;}
}