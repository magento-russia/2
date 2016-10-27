<?php
namespace Df\KazpostEms\Collector;
use Df\KazpostEms\Data\Foreign as D;
class Foreign extends Child {
	/**
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector\Child::s_collect()
	 * @return void
	 */
	protected function _collect() {
		/** @var int|null $zone */
		$zone = dfa(D::$countries, $this->countryDestUc());
		if (is_null($zone)) {
			$this->errorInvalidCountryDest();
		}
		$this->addRate($this->choose(D::$_rates, D::$_ratesMore, $zone));
	}

	/**
	 * @override
	 * @see \Df\Shipping\Collector::feeFixed()
	 * @used-by \Df\Shipping\Collector::addRate()
	 * «Заказное уведомление EMS отправлений: 400»
	 * http://www.kazpost.kz/uploads/content/files/УСЛУГИ%20УСКОРЕННОЙ%20И%20КУРЬЕРСКОЙ%20ПОЧТЫ.docx
	 * @return int|float
	 */
	protected function feeFixed() {return 400;}
}