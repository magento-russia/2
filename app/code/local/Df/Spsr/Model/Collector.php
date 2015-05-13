<?php
class Df_Spsr_Model_Collector extends Df_Shipping_Model_Collector {
	/**
	 * @override
	 * @return Df_Shipping_Model_Method[]
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Shipping_Model_Method[] $result */
			$result = array();
			$this->getRateRequest()->checkCountryOriginIs(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA);
			if (0 === count($this->getApi()->getRates())) {
				if (
						$this->getCarrier()->getRmConfig()->frontend()->needDisplayDiagnosticMessages()
					&&
						$this->getApi()->getErrorMessage()
				) {
					df_error($this->getApi()->getErrorMessage());
				}
			}
			else {
				foreach ($this->getApi()->getRates() as $rate) {
					/** @var array $rate */
					df_assert_array($rate);
					$result[]= $this->createMethodByRate($rate);
				}
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	/**
	 * @param array $rate
	 * @return Df_Shipping_Model_Method
	 */
	private function createMethodByRate(array $rate) {
		/** @var Df_Shipping_Model_Method $result */
		$result = null;
		/** @var string $rateTitle */
		$rateTitle = df_a($rate, Df_Spsr_Model_Request_Rate::RATE__TITLE);
		df_assert_string($rateTitle);
		/** @var Df_Spsr_Model_Method $result */
		$result = Df_Spsr_Model_Method::i($rateTitle);
		$result
			->setRequest($this->getRateRequest())
			->setCarrier($this->getCarrier()->getCarrierCode())
			/**
			 * При оформлении заказа Magento игнорирует данное значение
			 * и берёт заголовок способа доставки из реестра настроек:
			 *
				public function getCarrierName($carrierCode)
				{
					if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
						return $name;
					}
					return $carrierCode;
				}
			 */
			->setCarrierTitle($this->getCarrier()->getTitle())
			->addData(
				array(
					Df_Shipping_Model_Method::P__CARRIER_INSTANCE => $this->getCarrier()
					,Df_Shipping_Model_Method::P__METHOD_TITLE => $rateTitle
				)
			)
		;
		$result->setCost(
			rm_currency()->convertFromRoublesToBase(
				rm_float(df_a($rate, Df_Spsr_Model_Request_Rate::RATE__COST))
				,$this->getRateRequest()->getStoreId()
			)
		);
		$result->setTimeOfDeliveryMax(
			rm_nat0(df_a($rate, Df_Spsr_Model_Request_Rate::RATE__TIME_OF_DELIVERY__MAX))
		);
		$result->setTimeOfDeliveryMin(
			rm_nat0(df_a($rate, Df_Spsr_Model_Request_Rate::RATE__TIME_OF_DELIVERY__MIN))
		);
		return $result;
	}

	/** @return Df_Spsr_Model_Api_Calculator */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Spsr_Model_Api_Calculator::i(array(
				Df_Spsr_Model_Api_Calculator::P__REQUEST => $this->getRateRequest()
				,Df_Spsr_Model_Api_Calculator::P__DECLARED_VALUE =>
					rm_currency()->convertFromBaseToRoubles($this->declaredValueBase())
				,Df_Spsr_Model_Api_Calculator::P__RM_CONFIG => $this->getRmConfig()
			));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}