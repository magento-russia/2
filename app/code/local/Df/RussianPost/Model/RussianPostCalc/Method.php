<?php
abstract class Df_RussianPost_Model_RussianPostCalc_Method extends Df_Shipping_Model_Method {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTitleBase();

	/**
	 * @override
	 * @return float
	 */
	public function getCost() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->convertFromRoublesToBase($this->getCostInRoubles());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_sprintf(
					'%s: %d %s,'
					,$this->getTitleBase()
					,$this->getTimeOfDelivery()
					,$this->getTimeOfDeliveryNounForm($this->getTimeOfDelivery())
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = parent::isApplicable();
		if ($result) {
			try {
				$this
					->checkCountryDestinationIsRussia()
					->checkCountryOriginIsRussia()
					->checkWeightIsLE(31.5)
				;
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/** @return float */
	private function getCostInRoubles() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_float(rm_preg_match('#([\d\.\,]+) руб#u', $this->getRateAsText()))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getTimeOfDelivery() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_preg_match_int('#(\d+)\* дн#u', $this->getRateAsText());
		}
		return $this->{__METHOD__};
	}

	/**
	 * строка вида:
	 * Доставка Почтой России: 347.6 руб. Контрольный срок: 14* дн.
	 * или:
	 * Доставка Почтой России 1 класс: 382.44 руб. Контрольный срок: 4* дн
	 * @return string
	 */
	public function getRateAsText() {
		/** @var string $result */
		$result = $this->_getData(self::P__RATE_AS_TEXT);
		df_result_string($result);
		return $result;
	}

	/**
	 *
	 * строка вида:
	 * Доставка Почтой России: 347.6 руб. Контрольный срок: 14* дн.
	 *
	 * или:
	 * Доставка Почтой России 1 класс: 382.44 руб. Контрольный срок: 4* дн
	 *
	 *
	 * @param string $value
	 * @return Df_RussianPost_Model_RussianPostCalc_Method
	 */
	public function setRateAsText($value) {
		df_param_string($value, 0);
		$this->setData(self::P__RATE_AS_TEXT, $value);
		return $this;
	}

	const _CLASS = __CLASS__;
	const P__RATE_AS_TEXT = 'rate_as_text';
}