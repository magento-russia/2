<?php
namespace Df\Shipping;
use Df_Directory_Model_Country as Country;
use Df\Shipping\Exception\MethodNotApplicable as EMethodNotApplicable;
abstract class Collector extends Bridge {
	/**
	 * @used-by collect()
	 * @see \Df\Shipping\Collector\Conditional::_collect()
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
	 * @used-by \Df\Shipping\Collector\Child::_result()
	 * @used-by addError()
	 * @used-by addRate()
	 * @used-by call()
	 * @used-by r()
	 * @return \Df\Shipping\Rate\Result
	 */
	protected function _result() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new \Df\Shipping\Rate\Result;
			/** @uses collect() */
			$this->call(function() {$this->collect();});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param float $cost
	 * @param string|null $code [optional]
	 * @param string|null $title [optional]
	 * @param \Zend_Date|int|null $timeMin [optional]
	 * @param \Zend_Date|int|null $timeMax [optional]
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
		$this->_result()->append(\Df\Shipping\Rate\Result\Method::i(
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
	 * @used-by \Df\Exline\Collector::_collect()
	 * @param \Closure $f
	 * @return void
	 */
	protected function call(\Closure $f) {
		try {$f();}
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
	 * @param string|string[] $allowedIso2
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function checkCountryDestIs($allowedIso2) {
		$allowedIso2 = df_array($allowedIso2);
		if (!in_array($this->dCountryIso2(), $allowedIso2)) {
			$this->errorInvalidCountryDest();
		}
	}

	/**
	 * 2016-10-29
	 * @used-by \Df\InTime\Collector::_collect()
	 * @used-by \Df\NovaPoshta\Collector::_collect()
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function checkCountryDestIsRU() {$this->checkCountryDestIs('RU');}

	/**
	 * 2016-10-29
	 * @used-by \Df\InTime\Collector::_collect()
	 * @used-by \Df\NovaPoshta\Collector::_collect()
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function checkCountryDestIsUA() {$this->checkCountryDestIs('UA');}

	/**
	 * @used-by \Df\InTime\Collector::_collect()
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
		if ($this->rr()->getWeightInKg() > $weightInKilogrammes) {
			$this->errorInvalidWeight($weightInKilogrammes, 'не больше');
		}
	}

	/** @return \Df\Shipping\Config\Area\Admin */
	protected function configA() {return $this->config()->admin();}

	/** @return \Df\Shipping\Config\Area\Frontend */
	protected function configF() {return $this->config()->frontend();}

	/** @return \Df\Shipping\Config\Area\Service */
	protected function configS() {return $this->config()->service();}

	/**
	 * @used-by dCityUc()
	 * @return string|null
	 */
	protected function dCity() {return $this->rr()->getDestinationCity();}

	/** @return string */
	protected function dCityUc() {return dfc($this, function() {return
		mb_strtoupper($this->dCity())
	;});}

	/**
	 * 2016-10-29
	 * @return Country
	 */
	protected function dCountry() {return $this->rr()->getDestinationCountry();}

	/**
	 * @used-by \Df\Shipping\Collector\Conditional\WithForeign::suffix()
	 * @return string
	 */
	protected function dCountryIso2() {return $this->dCountry()->getIso2Code();}

	/**
	 * @used-by Df_Kazpost_Collector::collectForeign()
	 * @return string
	 */
	protected function dCountryUc() {return
		mb_strtoupper($this->dCountry()->getNameRussian())
	;}

	/**
	 * 2016-10-29
	 * @return string|null
	 */
	protected function dRegion() {return $this->rr()->getDestinationRegionName();}

	/**
	 * 2016-10-29
	 * @return int|null
	 */
	protected function dRegionId() {return $this->rr()->getDestinationRegionId();}

	/**
	 * @used-by \Df\NovaPoshta\Collector::responseRate()
	 * @return float
	 */
	protected function declaredValue() {return dfc($this, function() {return
		$this->rr()->getPackageValue()
		* $this->configA()->getDeclaredValuePercent()
		/ 100
	;});}

	/**
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function errorInvalidCityDest() {$this->errorInvalidCity($to = true);}

	/**
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function errorInvalidCityOrig() {$this->errorInvalidCity($to = false);}

	/**
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function errorInvalidCountryDest() {
		$this->error('Доставка <b>%s</b> невозможна.', $this->rr()->вСтрану());
	}

	/**
	 * 2016-10-29
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function eUnknownDest() {$this->error(
		'К сожалению, мы не можем определить указанное Вами место доставки.'
		."<br/>Может быть, Вы неправильно указали город, область или страну?"
	);}

	/**
	 * 2016-10-29
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	protected function eUnknownOrig() {$this->error(
		'Не получается найти адрес магазина в справочнике службы доставки.'
		."\nАдминистратору магазина надо либо изменить соответствующие значения"
		. ' в разделе «Система» → «Настройки» → «Продажи» → «Доставка:'
		. ' общие настройки» → «Расположение магазина»,'
		. ', либо обратиться в службу технической поддержки Российской сборки Magento.'
	);}

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

	/** @return bool */
	protected function isInCity() {return dfc($this, function() {return
		df_strings_are_equal_ci($this->rr()->getData('city'), $this->rr()->getDestCity())
	;});}

	/**
	 * @used-by oCityUc()
	 * @return string
	 */
	protected function oCity() {return $this->rr()->getOriginCity();}

	/** @return string */
	protected function oCityUc() {return dfc($this, function() {return
		mb_strtoupper($this->oCity())
	;});}

	/**
	 * 2016-10-29
	 * @return Country
	 */
	protected function oCountry() {return $this->rr()->getOriginCountry();}

	/**
	 * @used-by collect()
	 * @used-by \Df\Exline\Collector::locationOrigId()
	 * @return string|null
	 */
	protected function oCountryIso2() {return $this->rr()->getOriginCountryId();}

	/**
	 * 2016-10-29
	 * @return string
	 */
	protected function oRegion() {return $this->rr()->getOriginRegionName();}

	/**
	 * 2016-10-29
	 * @return int
	 */
	protected function oRegionId() {return $this->rr()->getOriginRegionId();}

	/**
	 * перекрывается методом @see \Df\Shipping\Collector\Child::rateDefaultCode()
	 * @used-by addRate()
	 * @return string
	 */
	protected function rateDefaultCode() {return 'standard';}

	/**
	 * @used-by \Df\NovaPoshta\Collector::responseRate()
	 * @return \Df\Shipping\Rate\Request
	 */
	protected function rr() {return $this[self::$P__RATE_REQUEST];}

	/**
	 * @used-by \Df\InTime\Collector::_collect()
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
	protected function weightKg() {return $this->rr()->getWeightInKg();}

	/**
	 * @used-by \Df\Exline\Collector::_collect()
	 * @return string
	 */
	protected function weightKgS() {return df_f2($this->weightKg());}

	/**
	 * @used-by \Df\NovaPoshta\Collector::responseRate()
	 * @return bool
	 */
	protected function приезжатьНаСкладМагазина() {return
		$this->configS()->needGetCargoFromTheShopStore()
	;}

	/**
	 * 2015-03-22
	 * Добавляем не более одного диагностического сообщения для конкретного способа доставки.
	 * @used-by call()
	 * @param string $message
	 * @return void
	 */
	private function addError($message) {
		if (!$this->_result()->getError()) {
			$this->_result()->append(new \Mage_Shipping_Model_Rate_Result_Error([
				'error' => true, 'error_message' => $message
			] + $this->resultCommon()));
		}
	}

	/**
	 * @used-by _result()
	 * @return void
	 */
	private function collect() {
		if (!$this->rr()->getOriginCountry()) {
			$this->error('Администратор должен указать страну склада интернет-магазина.');
		}
		if (!$this->oCity()) {
			$this->error('Администратор должен указать город склада интернет-магазина.');
		}
		if (!$this->oRegion()) {
			$this->error('Администратор должен указать область склада интернет-магазина.');
		}
		if (in_array($this->oCountryIso2(), array_merge(
			[$this->domesticIso2()], df_array($this->allowedOrigIso2Additional())
		))) {
			if (!$this->rr()->getDestinationCountry()) {
				$this->error('Укажите страну.');
			}
			if (!$this->dCity()) {
				$this->error('Укажите город.');
			}
			if (!$this->dRegion()) {
				$this->error('Укажите область.');
			}
			$this->_collect();
		}
	}

	/**
	 * @used-by configA()
	 * @used-by configF()
	 * @used-by configS()
	 * @return \Df\Checkout\Module\Config\Facade
	 */
	private function config() {return $this->main()->config();}

	/**
	 * @used-by declaredValue()
	 * @used-by feeDeclaredValueBase()
	 * @return float
	 */
	private function declaredValueBase() {return dfc($this, function() {return
		$this->rr()->getPackageValue()
		* $this->configA()->getDeclaredValuePercent()
		/ 100
	;});}

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
	 * 2016-10-24
	 * @used-by errorInvalidCityDest()
	 * @used-by errorInvalidCityOrig()
	 * @param bool $to
	 * @return void
	 * @throws \Df\Shipping\Exception
	 */
	private function errorInvalidCity($to) {$this->error(
		'Доставка <b>%s</b> невозможна, либо название населённого пункта написано неверно.'
		, $to ? $this->rr()->вМесто() : $this->rr()->изМеста()
	);}

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
	private function feeDeclaredValueBase() {return dfc($this, function() {return
		$this->declaredValueBase() * 0.01 * $this->feePercentOfDeclaredValue()
	;});}

	/**
	 * @used-by addRate()
	 * @param float $costBase
	 * @return float
	 */
	private function feeHandling($costBase) {return
		$costBase * 0.01 * $this->configA()->feePercent() + $this->configA()->feeFixed()
	;}

	/**
	 * @used-by addRate()
	 * @param float $amount
	 * @return float
	 */
	private function fromBase($amount) {return
		df_currency_h()->convertFromBase($amount, $this->currencyCode(), $this->store())
	;}

	/**
	 * @used-by call()
	 * @return string
	 */
	private function messageFailureGeneral() {return $this->main()->evaluateMessage(
		df_cfg()->shipping()->message()->getFailureGeneral($this->store())
	);}

	/**
	 * @used-by addError()
	 * @used-by addRate()
	 * @return array(string => string)
	 */
	private function resultCommon() {return dfc($this, function() {return [
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
	];});}

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
	private function toBase($amount) {return
		df_currency_h()->convertToBase($amount, $this->currencyCode(), $this->store())
	;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__RATE_REQUEST, '\Df\Shipping\Rate\Request');
	}
	/** @var string */
	private static $P__RATE_REQUEST = 'rate_request';

	/**
	 * @used-by \Df\Shipping\Carrier::collectRates()
	 * @param \Df\Shipping\Carrier $carrier
	 * @param \Mage_Shipping_Model_Rate_Request $rr
	 * @return \Df\Shipping\Rate\Result
	 */
	public static function r(\Df\Shipping\Carrier $carrier, \Mage_Shipping_Model_Rate_Request $rr) {
		/** @var self $i */
		$i = df_ic(df_con($carrier, 'Collector', __CLASS__), __CLASS__, array(
			self::$P__MAIN => $carrier
			, self::$P__RATE_REQUEST => \Df\Shipping\Rate\Request::i($carrier, $rr->getData())
		));
		return $i->_result();
	}
}