<?php
class Df_Spsr_Model_Collector extends Df_Shipping_Collector {
	/**
	 * @override
	 * @return Df_Shipping_Model_Method[]
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			$this->getRateRequest()->checkCountryOriginIs(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA);
			/** @var Df_Shipping_Model_Method[] $result */
			if (!$this->getApi()->getRates()) {
				$result = array();
				if (
						$this->main()->configF()->needDisplayDiagnosticMessages()
					&&
						$this->getApi()->getErrorMessage()
				) {
					df_error($this->getApi()->getErrorMessage());
				}
			}
			else {
				/** @uses createMethodByRate() */
				$result = array_map(array($this, 'createMethodByRate'), $this->getApi()->getRates());
			}
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
			->setCarrier($this->main()->getCarrierCode())
			/**
			 * При оформлении заказа Magento игнорирует данное значение
			 * и берёт заголовок способа доставки из реестра настроек:
			 * @see Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form::getCarrierName()
			 * @see Mage_Checkout_Block_Cart_Shipping::getCarrierName()
			 * @see Mage_Checkout_Block_Multishipping_Shipping::getCarrierName()
			 * @see Mage_Checkout_Block_Onepage_Shipping_Method_Available::getCarrierName()
			 */
			->setCarrierTitle($this->main()->getTitle())
			->addData(array(
				Df_Shipping_Model_Method::P__CARRIER_INSTANCE => $this->main()
				,Df_Shipping_Model_Method::P__METHOD_TITLE => $rateTitle
			))
		;
		$result->setCost(
			rm_currency_h()->convertFromRoublesToBase(
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
					rm_currency_h()->convertFromBaseToRoubles($this->declaredValueBase())
				,Df_Spsr_Model_Api_Calculator::P__RM_CONFIG => $this->config()
			));
		}
		return $this->{__METHOD__};
	}
}