<?php
// 2016-10-25
namespace Df\Ems;
use Df_Directory_Model_Country as Country;
class Collector extends \Df\Shipping\Collector\Ru {
	/**
	 * 2016-10-25
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkWeightIsLE(31.5);
		$this->addRate(
			$this->cond()->getRate()
			, null
			, null
			, $this->cond()->getDeliveryTimeMin()
			, $this->cond()->getDeliveryTimeMax()
		);
	}

	/** @return Conditions */
	private function cond() {return dfc($this, function() {return
		Conditions::i2($this->orig(), $this->dest(), $this->rr()->getWeightInKg(), 'att')
	;});}

	/**
	 * 2016-10-25
	 * @return string
	 * @throws \Exception
	 */
	private function dest() {return dfc($this, function() {
		/** @var Country|null $country */
		$country = $this->rr()->getDestinationCountry();
		/** @var int|string|null $regionId */
		$regionId = $this->rr()->getDestinationRegionId();
		/** @var string|null $result */
		$result = \Df\Ems\Locator::find($country, $regionId, $this->rr()->getDestinationCity());
		if (!$result) {
			$this->rr()->throwException(
				$country->isRussia() && !$regionId
				? 'Укажите область.'
				: 'К сожалению, мы не можем определить указанное Вами место доставки.'
				."<br/>Может быть, Вы неправильно указали город, область или страну?"
			);
		}
		return $result;
	});}

	/**
	 * 2016-10-25
	 * @return string
	 * @throws \Exception
	 */
	private function orig() {return dfc($this, function() {
		/** @var string|null $result */
		$result = \Df\Ems\Locator::find(
			$this->rr()->getOriginCountry()
			, $this->rr()->getOriginRegionId()
			, $this->rr()->getOriginCity()
		);
		if (!$result) {
			$this->rr()->throwException(
				'Не получается найти адрес магазина в справочнике EMS Почты России.'
				."\nАдминистратору магазина надо либо изменить соответствующие значения"
				. ' в разделе «Система» → «Настройки» → «Продажи» → «Доставка:'
				. ' общие настройки» → «Расположение магазина»,'
				. ', либо обратиться в службу технической поддержки Российской сборки Magento.'
			);
		}
		return $result;
	});}
}