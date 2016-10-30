<?php
namespace Df\Kazpost\Collector;
class Domestic extends Child {
	/**
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector\Child::s_collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkWeightIsLE(14.5);
		/** @var float $weight */
		$weight = $this->weightKg();
		if ($weight < 3) {
			$this->rates(500, 1080, 1380);
		}
		else if ($weight < 6) {
			$this->rates(600, 1380, 1880);
		}
		else if ($weight < 9) {
			$this->rates(700, 1680, 2380);
		}
		else if ($weight < 12) {
			$this->rates(800, 1980, 2880);
		}
		else if ($weight < 14.5) {
			$this->rates(900, 2280, 3380);
		}
	}

	/**
	 * @override
	 * @see \Df\Shipping\Collector::feeFixed()
	 * @used-by \Df\Shipping\Collector::rate()
	 * «Прием/доставка посылок на дом, в офис за 1 ед. отправления (при наличии возможности): 600»
	 * http://www.kazpost.kz/uploads/content/files/СТАНДАРТ%20Тарифы%20по%20почтовым%20услугам.docx
	 * @return int|float
	 */
	protected function feeFixed() {return 600;}

	/**
	 * @used-by _collect()
	 * @param float $inCity
	 * @param float $ground
	 * @param float $air
	 */
	private function rates($inCity, $ground, $air) {
		if ($this->isInCity()) {
			$this->rate($inCity, null, null, 'in_city');
		}
		else {
			$this->rate($ground, null, null, 'ground', 'наземным транспортом');
			$this->rate($air, null, null, 'air', 'воздушным транспортом');
		}
	}
}