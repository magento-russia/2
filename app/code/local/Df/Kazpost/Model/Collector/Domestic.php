<?php
class Df_Kazpost_Model_Collector_Domestic extends Df_Shipping_Model_Collector_Child {
	/**
	 * @override
	 * @see Df_Shipping_Model_Collector_Simple::_collect()
	 * @used-by Df_Shipping_Model_Collector_Child::s_collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkWeightIsLE(14.5);
		/** @var float $weight */
		$weight = $this->weightKg();
		if (3 > $weight) {
			$this->rates(500, 1080, 1380);
		}
		else if (6 > $weight) {
			$this->rates(600, 1380, 1880);
		}
		else if (9 > $weight) {
			$this->rates(700, 1680, 2380);
		}
		else if (12 > $weight) {
			$this->rates(800, 1980, 2880);
		}
		else if (14.5 > $weight) {
			$this->rates(900, 2280, 3380);
		}
	}

	/**
	 * @override
	 * @see Df_Shipping_Model_Collector_Simple::feeFixed()
	 * @used-by Df_Shipping_Model_Collector_Simple::addRate()
	 * «Прием/доставка посылок на дом, в офис за 1 ед. отправления (при наличии возможности): 600»
	 * @link http://www.kazpost.kz/uploads/content/files/СТАНДАРТ%20Тарифы%20по%20почтовым%20услугам.docx
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
			$this->addRate($inCity, 'in_city');
		}
		else {
			$this->addRate($ground, 'ground', 'наземным транспортом');
			$this->addRate($air, 'air', 'воздушным транспортом');
		}
	}
}