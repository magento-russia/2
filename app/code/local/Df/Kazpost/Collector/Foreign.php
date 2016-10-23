<?php
class Df_Kazpost_Collector_Foreign extends Df_Shipping_Collector_Child {
	/**
	 * @override
	 * @see Df_Shipping_Collector::_collect()
	 * @used-by Df_Shipping_Collector_Child::s_collect()
	 * @return void
	 */
	protected function _collect() {
		/** @var int|null $zone */
		$zone = dfa(Df_Kazpost_Zones::$a, $this->countryDestUc());
		if (!$zone) {
			$this->errorInvalidCountryDest();
		}
		/** @var float $weight */
		$weight = $this->weightKg();
		/** @var int[] $rates */
		foreach (self::$_rates as $maxWeight => $currentRates) {
			/** @var int $maxWeight */
			/** @var int[] $currentRates */
			if ($weight < $maxWeight) {
				$rates = $currentRates;
				break;
			}
		}
		if (!isset($rates)) {
			/** @var int[] $last */
			$last = self::$_rates[10];
			/** @var int $diff */
			$diff = (int)floor($weight - 10);
			/** @var int $count */
			$count = count($last);
			for ($i = 0; $i < $count; $i++) {
				$rates[]= $last[$i] + $diff * self::$_ratesMore[$i];
			}
		}
		$this->addRate($rates[$zone - 1], 'ground', 'наземным транспортом');
		$this->addRate($rates[5 + $zone - 1], 'air', 'воздушным транспортом');
	}

	/**
	 * @override
	 * @see Df_Shipping_Collector::feeFixed()
	 * @used-by Df_Shipping_Collector::addRate()
	 * «Прием/доставка посылок на дом, в офис за 1 ед. отправления (при наличии возможности): 600»
	 * «Экспедиционный сбор независимо от  веса и объявленной ценности: 400»
	 * «Простое уведомление международных почтовых отправлений: 310»
	 * http://www.kazpost.kz/uploads/content/files/СТАНДАРТ%20Тарифы%20по%20почтовым%20услугам.docx
	 * @return int|float
	 */
	protected function feeFixed() {return 600 + 400 + 310;}

	/** @var array(int => int[]) */
	private static $_rates = array(
		3 => array(3400, 4100, 5500, 7600, 12200, 5100, 6150, 8250, 11400, 18300)
		,4 => array(3900, 4800, 6400, 9400, 15800, 5850, 7200, 9600, 14100, 23700)
		,5 => array(4400, 5500, 7300, 11200, 19400, 6600, 8250, 10950, 16800, 29100)
		,6 => array(4900, 6200, 8200, 13000, 23000, 7350, 9300, 12300, 19500, 34500)
		,7 => array(5400, 6900, 9100, 14800, 26600, 8100, 10350, 13650, 22200, 39900)
		,8 => array(5900, 7600, 10000, 16600, 30200, 8850, 11400, 15000, 24900, 45300)
		,9 => array(6400, 8300, 10900, 18400, 33800, 9600, 12450, 16350, 27600, 50700)
		,10 => array(6900, 9000, 11800, 20200, 37400, 10350, 13500, 17700, 30300, 56100)
	);

	/** @var int[] */
	private static $_ratesMore = array(500, 700, 1000, 1800, 3600, 750, 1050, 1500, 2700, 5400);
}