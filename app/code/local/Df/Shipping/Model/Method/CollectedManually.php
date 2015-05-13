<?php
abstract class Df_Shipping_Model_Method_CollectedManually extends Df_Shipping_Model_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getTitleBase()
				? 'default'
				: df_output()->formatUrlKeyPreservingCyrillic($this->getTitleBase())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		return
			!$this->getRequest()
			? ''
			: (!$this->getTitleBase() ? '' : $this->getTitleBase() . ': ')
			. $this->formatTimeOfDelivery($this->getTimeOfDeliveryMin(), $this->getTimeOfDeliveryMax())
		;
	}

	/** @return string */
	protected function getTitleBase() {return $this->_getData(self::P__TITLE_BASE);}

	/**
	 * @param int $value
	 * @return Df_Spsr_Model_Method
	 */
	public function setTimeOfDelivery($value) {
		$this->setTimeOfDeliveryMax($value);
		$this->setTimeOfDeliveryMin($value);
		return $this;
	}

	/**
	 * @param int $value
	 * @return Df_Spsr_Model_Method
	 */
	public function setTimeOfDeliveryMax($value) {
		df_param_integer($value, 0);
		$this->setData(self::P__TIME_OF_DELIVERY__MAX, $value);
		return $this;
	}

	/**
	 * @param int $value
	 * @return Df_Spsr_Model_Method
	 */
	public function setTimeOfDeliveryMin($value) {
		df_param_integer($value, 0);
		$this->setData(self::P__TIME_OF_DELIVERY__MIN, $value);
		return $this;
	}

	/** @return int */
	protected function getTimeOfDeliveryMax() {
		return rm_nat0($this->_getData(self::P__TIME_OF_DELIVERY__MAX));
	}

	/** @return int */
	protected function getTimeOfDeliveryMin() {
		return rm_nat0($this->_getData(self::P__TIME_OF_DELIVERY__MIN));
	}
	const _CLASS = __CLASS__;
	const P__TIME_OF_DELIVERY__MAX = 'time_of_delivery__max';
	const P__TIME_OF_DELIVERY__MIN = 'time_of_delivery__min';
	const P__TITLE_BASE = 'title_base';
}