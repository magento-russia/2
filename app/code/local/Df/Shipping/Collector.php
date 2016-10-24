<?php
use Df\Shipping\Exception\MethodNotApplicable as EMethodNotApplicable;
abstract class Df_Shipping_Collector extends Df_Shipping_Model_Bridge {
	/**
	 * @used-by collect()
	 * @see Df_Shipping_Collector_Conditional::_collect()
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
	 * @used-by Df_Shipping_Collector_Child::_result()
	 * @used-by addError()
	 * @used-by addRate()
	 * @used-by call()
	 * @used-by r()
	 * @return Df_Shipping_Rate_Result
	 */
	protected function _result() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Df_Shipping_Rate_Result;
			/** @uses collect() */
			$this->call(function() {$this->collect();});
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
		$cost = df_float_positive($cost);
		if (!$code) {
			$code = $this->rateDefaultCode();
		}
		/** @var float $costBase */
		$costBase = $this->toBase($cost + $this->feeFixed()) + $this->feeDeclaredValueBase();
		/** @var float $price */
		$price = $this->roundPrice($costBase + $this->feeHandling($costBase));
		$this->_result()->append(Df_Shipping_Rate_Result_Method::i(
			$code, $title, $costBase, $price, $this->resultCommon(), $timeMin, $timeMax
		));
	}

	/**
	 * @used-by collect()
	 * @return string|string[]
	 */
	protected function allowedOrigIso2Additional() {return [];}

	/**
	 * @used-by _result()
	 * @used-by Df_Exline_Collector::_collect()
	 * @param \Closure $f
	 * @return void
	 */
	protected function call(\Closure $f) {
		try {
			$f();
		}
		catch (\Exception $e) {
			df_context('Служба доставки', $this->main()->getTitle());
			/** @var \Exception|\Df\Shipping\Exception $e) */
			/** @var bool $isSpecific */
			$isSpecific = $e instanceof \Df\Shipping\Exception;
			if (!$isSpecific) {
				$e = df_ewrap(df_ef($e));
			}
			$e->comment(df_print_params(dfa_unset($this->rr()->getData(), 'all_items')));
			df_log($e);
			/** @var string $mc */
			if ($isSpecific && $e->messageC()) {
				$mc = $e->messageC();
			}
			else {
				$this->_result()->markInternalError();
				$mc = $this->messageFailureGeneral();
			}
			$this->addError($mc);
		}
	}

	/**
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function checkCityDest() {
		if (!$this->cityDest()) {
			$this->error('Укажите город.');
		}
	}

	/**
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function checkCityOrig() {
		if (!$this->cityOrig()) {
			$this->error('Администратор должен указать город склада интернет-магазина.');
		}
	}

	/**
	 * @used-by Df_NovaPoshta_Collector::_collect()
	 * @param string|string[] $allowedIso2
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function checkCountryDestIs($allowedIso2) {
		$allowedIso2 = df_array($allowedIso2);
		if (!in_array($this->countryDestIso2(), $allowedIso2)) {
			$this->errorInvalidCountryDest();
		}
	}

	/**
	 * @used-by Df_InTime_Collector::_collect()
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function checkStreetDest() {
		if (!$this->streetDest()) {
			$this->error('Укажите улицу, дом, квартиру.');
		}
	}

	/**
	 * @param int|float $weightInKilogrammes
	 * @return void
	 * @throws \Df\Shipping\Exception
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
	 * @used-by Df_Exline_Collector::locationOrigId()
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

	/** @return Df_Shipping_Config_Area_Admin */
	protected function configA() {return $this->config()->admin();}

	/** @return Df_Shipping_Config_Area_Frontend */
	protected function configF() {return $this->config()->frontend();}

	/** @return Df_Shipping_Config_Area_Service */
	protected function configS() {return $this->config()->service();}

	/**
	 * @used-by countryDestUc()
	 * @return string
	 */
	protected function countryDest() {return $this->rr()->getDestinationCountry()->getNameRussian();}

	/**
	 * @used-by Df_Shipping_Collector_Conditional_WithForeign::childClass()
	 * @return string|null
	 */
	protected function countryDestIso2() {return $this->rr()->getDestinationCountryId();}

	/**
	 * @used-by Df_Kazpost_Collector::collectForeign()
	 * @return string
	 */
	protected function countryDestUc() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_strtoupper($this->countryDest());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_NovaPoshta_Collector::responseRate()
	 * @return float
	 */
	protected function declaredValue() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->fromBase(
				$this->rr()->getPackageValue()
				* $this->configA()->getDeclaredValuePercent()
				/ 100
			);
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
	 * @throws \Df\Shipping\Exception
	 */
	protected function errorInvalidCityDest() {
		$this->error(
			'Доставка <b>%s</b> невозможна, либо название населённого пункта написано неверно.'
			, $this->rr()->вМесто()
		);
	}

	/**
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function errorInvalidCityOrig() {
		$this->error(
			'Доставка <b>%s</b> невозможна, либо название населённого пункта написано неверно.'
			, $this->rr()->изМеста()
		);
	}

	/**
	 * @return void
	 * @throws \Df\Shipping\Exception
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
	 * перекрывается методом @see Df_Shipping_Collector_Child::rateDefaultCode()
	 * @used-by addRate()
	 * @return string
	 */
	protected function rateDefaultCode() {return 'standard';}

	/**
	 * @used-by Df_NovaPoshta_Collector::responseRate()
	 * @return Df_Shipping_Rate_Request
	 */
	protected function rr() {return $this[self::$P__RATE_REQUEST];}

	/**
	 * @used-by Df_InTime_Collector::_collect()
	 * @used-by checkStreetDest()
	 * @return string
	 */
	protected function streetDest() {return $this->rr()->getDestStreet();}

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
	 * @used-by Df_Exline_Collector::_collect()
	 * @return string
	 */
	protected function weightKgS() {return df_f2($this->weightKg());}

	/**
	 * @used-by Df_NovaPoshta_Collector::responseRate()
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
		if (!$this->_result()->getError()) {
			$this->_result()->append(new Mage_Shipping_Model_Rate_Result_Error(array(
				'error' => true, 'error_message' => $message
			) + $this->resultCommon()));
		}
	}

	/**
	 * @used-by collect()
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	private function checkCountryDest() {
		if (!$this->rr()->getDestinationCountry()) {
			$this->error('Укажите страну.');
		}
	}

	/**
	 * @used-by collect()
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	private function checkCountryOrig() {
		if (!$this->rr()->getOriginCountry()) {
			$this->error('Администратор должен указать страну склада интернет-магазина.');
		}
	}

	/**
	 * @used-by _result()
	 * @return void
	 */
	private function collect() {
		$this->checkCountryOrig();
		if (in_array($this->countryOrigIso2(), array_merge(
			array($this->domesticIso2()), df_array($this->allowedOrigIso2Additional())
		))) {
			$this->checkCountryDest();
			$this->_collect();
		}
	}

	/**
	 * @used-by configA()
	 * @used-by configF()
	 * @used-by configS()
	 * @return Df_Checkout_Module_Config_Facade
	 */
	private function config() {return $this->main()->config();}

	/**
	 * @used-by declaredValue()
	 * @used-by feeDeclaredValueBase()
	 * @return float
	 */
	private function declaredValueBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->rr()->getPackageValue()
				* $this->configA()->getDeclaredValuePercent()
				/ 100
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return void
	 * @throws EMethodNotApplicable
	 */
	private function error() {
		throw new EMethodNotApplicable(
			$this->main(), call_user_func_array('sprintf', func_get_args())
		);
	}

	/**
	 * @used-by checkWeightIsLE()
	 * @param int|float $maxWeightInKilogrammes
	 * @param string $conditionAsText
	 * @throws EMethodNotApplicable
	 */
	private function errorInvalidWeight($maxWeightInKilogrammes, $conditionAsText) {
		$this->error(strtr(
			'Вес посылки должен быть {не больше|меньше} {предел веса} кг,'
			. ' а вес Вашего заказа: {вес заказа} кг.'
			,array(
				'{не больше|меньше}' => $conditionAsText
				,'{предел веса}' => df_f2i($maxWeightInKilogrammes, 1)
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
		return df_currency_h()->convertFromBase($amount, $this->currencyCode(), $this->store());
	}

	/**
	 * @used-by call()
	 * @return string
	 */
	private function messageFailureGeneral() {
		return $this->main()->evaluateMessage(
			df_cfg()->shipping()->message()->getFailureGeneral($this->store())
		);
	}

	/**
	 * @used-by addError()
	 * @used-by addRate()
	 * @return array(string => string)
	 */
	private function resultCommon() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				/** @used-by Mage_Sales_Model_Quote_Address_Rate::importShippingRate() */
				'carrier' => $this->main()->getCarrierCode()
				/**
				 * @used-by Mage_Sales_Model_Quote_Address_Rate::importShippingRate()
				 * При оформлении заказа Magento игнорирует данное значение
				 * и берёт заголовок способа доставки из реестра настроек:
				 * @see Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form::getCarrierName()
				 * @see Mage_Checkout_Block_Cart_Shipping::getCarrierName()
				 * @see Mage_Checkout_Block_Multishipping_Shipping::getCarrierName()
				 * @see Mage_Checkout_Block_Onepage_Shipping_Method_Available::getCarrierName()
				 */
				,'carrier_title' => $this->main()->getTitle()
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
	 * @used-by addRate()
	 * @param float $amount
	 * @return float
	 */
	private function toBase($amount) {
		return df_currency_h()->convertToBase($amount, $this->currencyCode(), $this->store());
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__RATE_REQUEST, 'Df_Shipping_Rate_Request');
	}
	/** @var string */
	private static $P__RATE_REQUEST = 'rate_request';

	/**
	 * @used-by Df_Shipping_Carrier::collectRates()
	 * @param Df_Shipping_Carrier $carrier
	 * @param Mage_Shipping_Model_Rate_Request $rr
	 * @return Df_Shipping_Rate_Result
	 */
	public static function r(
		Df_Shipping_Carrier $carrier, Mage_Shipping_Model_Rate_Request $rr
	) {
		/** @var Df_Shipping_Collector $i */
		$i = df_ic(df_con($carrier, 'Collector', __CLASS__), __CLASS__, array(
			self::$P__MAIN => $carrier
			, self::$P__RATE_REQUEST => Df_Shipping_Rate_Request::i($carrier, $rr->getData())
		));
		return $i->_result();
	}
}