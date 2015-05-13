<?php
abstract class Df_Shipping_Model_Collector_Simple extends Df_Shipping_Model_Collector {
	/**
	 * @used-by collect()
	 * @see Df_Shipping_Model_Collector_Conditional::_collect()
	 * @return void
	 */
	abstract protected function _collect();

	/**
	 * @used-by fromBase()
	 * @used-by toBase()
	 * @return string
	 */
	abstract protected function currencyCode();

	/**
	 * @used-by collect()
	 * @return string
	 */
	abstract protected function domesticIso2();

	/**
	 * @override
	 * @see Df_Shipping_Model_Collector::getRateResult()
	 * @used-by Df_Shipping_Model_Carrier::collectRates()
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function getRateResult() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_model('shipping/rate_result');
			/** @uses collect() */
			$this->call('collect');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param float $cost
	 * @param string|null $code [optional]
	 * @param string|null $title [optional]
	 * @param Zend_Date|int|null $timeMin [optional]
	 * @param Zend_Date|int|null $timeMax [optional]
	 */
	protected function addRate($cost, $code = null, $title = null, $timeMin = null, $timeMax = null) {
		$cost = rm_float_positive($cost);
		if (!$code) {
			$code = $this->rateDefaultCode();
		}
		/** @var float $costBase */
		$costBase = $this->toBase($cost + $this->feeFixed()) + $this->feeDeclaredValueBase();
		/** @var float $price */
		$price = $this->roundPrice($costBase + $this->feeHandling($costBase));
		$title = $this->prepareMethodTitle($title, $timeMin, $timeMax);
		$this->getRateResult()->append(new Mage_Shipping_Model_Rate_Result_Method(array(
			'method' => $code, 'method_title' => $title, 'cost' => $costBase, 'price' => $price
		) + $this->resultCommon()));
	}

	/**
	 * @used-by collect()
	 * @return string|string[]
	 */
	protected function allowedOrigIso2Additional() {return array();}

	/**
	 * @used-by getRateResult()
	 * @used-by Df_Exline_Model_Collector::_collect()
	 * @return void
	 */
	protected function call() {
		try {
			/** @var mixed[] $args */
			$args = func_get_args();
			call_user_func_array(array($this, rm_first($args)), rm_tail($args));
		}
		catch (Df_Shipping_Exception $e) {
			$this->addError($e->getMessage() ? $e->getMessage() : $this->messageFailureGeneral());
		}
		catch (Exception $e) {
			df_notify_exception($e);
			$this->addError($this->messageFailureGeneral());
		}
	}

	/**
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	protected function checkCityDest() {
		if (!$this->cityDest()) {
			$this->error('Укажите город.');
		}
	}

	/**
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	protected function checkCityOrig() {
		if (!$this->cityOrig()) {
			$this->error('Администратор должен указать город склада интернет-магазина.');
		}
	}

	/**
	 * @used-by Df_NovaPoshta_Model_Collector::_collect()
	 * @param string|string[] $allowedIso2
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	protected function checkCountryDestIs($allowedIso2) {
		$allowedIso2 = rm_array($allowedIso2);
		if (!in_array($this->countryDestIso2(), $allowedIso2)) {
			$this->errorInvalidCountryDest();
		}
	}

	/**
	 * @param int|float $weightInKilogrammes
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	protected function checkWeightIsLE($weightInKilogrammes) {
		if ($this->rr()->getWeightInKilogrammes() > $weightInKilogrammes) {
			$this->errorInvalidWeight($weightInKilogrammes, 'не больше');
		}
	}

	/**
	 * @used-by cityDestUc()
	 * @return string|null
	 */
	protected function cityDest() {return $this->rr()->getDestinationCity();}

	/**
	 * @return string
	 */
	protected function cityDestUc() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_strtoupper($this->cityDest());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by cityOrigUc()
	 * @return string
	 */
	protected function cityOrig() {return $this->rr()->getOriginCity();}

	/**
	 * @used-by collect()
	 * @used-by Df_Exline_Model_Collector::locationOrigId()
	 * @return string|null
	 */
	protected function countryOrigIso2() {return $this->rr()->getOriginCountryId();}

	/**
	 * @return string
	 */
	protected function cityOrigUc() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_strtoupper($this->cityOrig());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by countryDestUc()
	 * @return string
	 */
	protected function countryDest() {return $this->rr()->getDestinationCountry()->getNameRussian();}

	/**
	 * @used-by Df_Shipping_Model_Collector_Conditional_WithForeign::childClass()
	 * @return string|null
	 */
	protected function countryDestIso2() {return $this->rr()->getDestinationCountryId();}

	/**
	 * @used-by Df_Kazpost_Model_Collector::collectForeign()
	 * @return string
	 */
	protected function countryDestUc() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_strtoupper($this->countryDest());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_NovaPoshta_Model_Collector::responseRate()
	 * @return float
	 */
	protected function declaredValue() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->fromBase($this->declaredValueBase());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by addRate()
	 * @return int|float
	 */
	protected function feeFixed() {return 0;}

	/**
	 * @used-by addRate()
	 * @return int|float
	 */
	protected function feePercentOfDeclaredValue() {return 0;}

	/**
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	protected function errorInvalidCityDest() {
		$this->error(
			'Доставка <b>%s</b> невозможна, либо название населённого пункта написано неверно.'
			, $this->rr()->вМесто()
		);
	}

	/**
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	protected function errorInvalidCityOrig() {
		$this->error(
			'Доставка <b>%s</b> невозможна, либо название населённого пункта написано неверно.'
			, $this->rr()->изМеста()
		);
	}

	/**
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	protected function errorInvalidCountryDest() {
		$this->error('Доставка <b>%s</b> невозможна.', $this->rr()->вСтрану());
	}

	/**
	 * @return bool
	 */
	protected function isInCity() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_strings_are_equal_ci(
				$this->rr()->getData('city'), $this->rr()->getDestCity()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by addRate()
	 * @return string
	 */
	protected function rateDefaultCode() {return 'standard';}

	/**
	 * @used-by Df_NovaPoshta_Model_Collector::responseRate()
	 * @return Df_Shipping_Model_Rate_Request
	 */
	protected function rr() {return $this->getRateRequest();}

	/**
	 * @return int
	 */
	protected function weightG() {return $this->rr()->getWeightInGrammes();}

	/**
	 * @used-by weightKgS()
	 * @return float
	 */
	protected function weightKg() {return $this->rr()->getWeightInKilogrammes();}

	/**
	 * @used-by Df_Exline_Model_Collector::_collect()
	 * @return string
	 */
	protected function weightKgS() {return rm_flts($this->weightKg());}

	/**
	 * @used-by Df_NovaPoshta_Model_Collector::responseRate()
	 * @return bool
	 */
	protected function приезжатьНаСкладМагазина() {
		return $this->configS()->needGetCargoFromTheShopStore();
	}

	/**
	 * 2015-03-22
	 * Добавляем не более одного диагностического сообщения для конкретного способа доставки.
	 * @used-by call()
	 * @param string $message
	 * @return void
	 */
	private function addError($message) {
		if (!$this->getRateResult()->getError()) {
			$this->getRateResult()->append(new Mage_Shipping_Model_Rate_Result_Error(array(
				'error' => true, 'error_message' => df_no_escape($message)
			) + $this->resultCommon()));
		}
	}

	/**
	 * @used-by collect()
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	private function checkCountryDest() {
		if (!$this->rr()->getDestinationCountry()) {
			$this->error('Укажите страну.');
		}
	}

	/**
	 * @used-by collect()
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	private function checkCountryOrig() {
		if (!$this->rr()->getOriginCountry()) {
			$this->error('Администратор должен указать страну склада интернет-магазина.');
		}
	}

	/**
	 * @used-by getRateResult()
	 * @return void
	 */
	private function collect() {
		$this->checkCountryOrig();
		if (in_array($this->countryOrigIso2(), array_merge(
			array($this->domesticIso2()), rm_array($this->allowedOrigIso2Additional())
		))) {
			$this->checkCountryDest();
			$this->_collect();
		}
	}

	/**
	 * @used-by feeFixed()
	 * @used-by feePercent()
	 * @used-by getHandlingFee()
	 * @return Df_Shipping_Model_Config_Area_Admin
	 */
	private function configA() {return $this->getRmConfig()->admin();}

	/**
	 * @used-by приезжатьНаСкладМагазина()
	 * @return Df_Shipping_Model_Config_Area_Service
	 */
	private function configS() {return $this->getRmConfig()->service();}

	/**
	 * @used-by prepareMethodTitle()
	 * @param Zend_Date|int|null $date
	 * @return int|null
	 */
	private function dateToDays($date) {
		/** @var int|null $result */
		if (is_null($date) || is_int($date)) {
			$result = $date;
		}
		else {
			df_assert($date instanceof Zend_Date);
			$result = df()->date()->getNumberOfDaysBetweenTwoDates($date, Zend_Date::now());
		}
		return $result;
	}

	/**
	 * @used-by prepareMethodTitle()
	 * @param int $value
	 * @return string
	 */
	private function dayNoun($value) {
		return df_text()->getNounForm($value, array('день', 'дня', 'дней'));
	}

	/**
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	private function error() {
		/** @var mixed $args */
		$args = func_get_args();
		throw new Df_Shipping_Exception(call_user_func_array('sprintf', $args));
	}

	/**
	 * @used-by checkWeightIsLE()
	 * @param int|float $maxWeightInKilogrammes
	 * @param string $conditionAsText
	 * @throws Df_Core_Exception_Client
	 */
	private function errorInvalidWeight($maxWeightInKilogrammes, $conditionAsText) {
		$this->error(strtr(
			'Вес посылки должен быть {не больше|меньше} {предел веса} кг,'
			. ' а вес Вашего заказа: {вес заказа} кг.'
			,array(
				'{не больше|меньше}' => $conditionAsText
				,'{предел веса}' => rm_flits($maxWeightInKilogrammes, 1)
				,'{вес заказа}' => $this->rr()->getWeightKgSD()
			)
		));
	}

	/**
	 * @used-by addRate()
	 * @return float
	 */
	private function feeDeclaredValueBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->declaredValueBase() * 0.01 * $this->feePercentOfDeclaredValue();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by addRate()
	 * @param float $costBase
	 * @return float
	 */
	private function feeHandling($costBase) {
		return $costBase * 0.01 * $this->configA()->feePercent() + $this->configA()->feeFixed();
	}

	/**
	 * @used-by addRate()
	 * @param float $amount
	 * @return float
	 */
	private function fromBase($amount) {
		return rm_currency()->convertFromBase($amount, $this->currencyCode(), $this->store());
	}

	/**
	 * @used-by call()
	 * @return string
	 */
	private function messageFailureGeneral() {
		return $this->getCarrier()->evaluateMessage(
			df_cfg()->shipping()->message()->getFailureGeneral($this->store())
		);
	}

	/**
	 * @used-by addRate()
	 * @param string|null $title [optional]
	 * @param Zend_Date|int|null $min [optional]
	 * @param Zend_Date|int|null $max [optional]
	 * @return string
	 */
	private function prepareMethodTitle($title = null, $min = null, $max = null) {
		/** @var int $handlingTime */
		$handlingTime = $this->configA()->getProcessingBeforeShippingDays();
		/** @var string $timeS */
		$min = $this->dateToDays($min);
		$max = $this->dateToDays($max);
		if (!$min && !$max) {
			$timeS = '';
		}
		else {
			if (!$max || $min === $max) {
				$min += $handlingTime;
				$timeS = "{$min} {$this->dayNoun($min)},";
			}
			else {
				$min += $handlingTime;
				$max += $handlingTime;
				$timeS = "{$min}-{$max} {$this->dayNoun($max)},";
			}
		}
		return implode(': ', array_filter(array($title, $timeS)));
	}

	/**
	 * @used-by addError()
	 * @used-by addRate()
	 * @return array(string => string)
	 */
	private function resultCommon() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'carrier' => $this->getCarrier()->getCarrierCode()
				/**
				 * При оформлении заказа Magento игнорирует данное значение
				 * и берёт заголовок способа доставки из реестра настроек:
				 * @see Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form::getCarrierName()
				 * @see Mage_Checkout_Block_Cart_Shipping::getCarrierName()
				 * @see Mage_Checkout_Block_Multishipping_Shipping::getCarrierName()
				 * @see Mage_Checkout_Block_Onepage_Shipping_Method_Available::getCarrierName()
				 */
				,'carrier_title' => $this->getCarrier()->getTitle()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by addRate()
	 * @param float $price
	 * @return float
	 */
	private function roundPrice($price) {return $this->store()->roundPrice($price);}

	/**
	 * @used-by fromBase()
	 * @used-by toBase()
	 * @used-by roundPrice()
	 * @return Mage_Core_Model_Store
	 */
	private function store() {return $this->getCarrier()->getRmStore();}

	/**
	 * @used-by addRate()
	 * @param float $amount
	 * @return float
	 */
	private function toBase($amount) {
		return rm_currency()->convertToBase($amount, $this->currencyCode(), $this->store());
	}
}