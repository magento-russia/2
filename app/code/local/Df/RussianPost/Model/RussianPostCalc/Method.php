<?php
abstract class Df_RussianPost_Model_RussianPostCalc_Method extends Df_Shipping_Model_Method_Russia {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTitleBase();

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			->checkCountryDestinationIsRussia()
			->checkCountryOriginIsRussia()
			->checkWeightIsLE(31.5)
			->checkPostalCodeDestinationIsRussian()
			->checkPostalCodeOriginIsRussian()
		;
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return float
	 */
	protected function getCost() {return rm_float(rm_preg_match('#([\d\.\,]+) руб#u', $this->_rateT));}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {return rm_preg_match_int('#(\d+)\* дн#u', $this->_rateT);}

	/**
	 * строка вида: «Доставка Почтой России: 347.6 руб. Контрольный срок: 14* дн.»
	 * или: «Доставка Почтой России 1 класс: 382.44 руб. Контрольный срок: 4* дн»
	 * @used-by Df_RussianPost_Model_Collector::createDomesticMethod()
	 * @param string $value
	 * @return void
	 */
	public function setRateT($value) {
		df_param_string_not_empty($value, 0);
		$this->_rateT = $value;
	}

	/** @var string */
	private $_rateT;
}