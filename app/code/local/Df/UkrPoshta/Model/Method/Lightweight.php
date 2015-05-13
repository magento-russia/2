<?php
abstract class Df_UkrPoshta_Model_Method_Lightweight extends Df_UkrPoshta_Model_Method {
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
					->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE)
					/**
					 * Масса груза не должна превышать 30 килограммов
					 * http://services.ukrposhta.com/CalcUtil/CourierDelivery31.aspx
					 */
					->checkWeightIsLE(30);
				;
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getApiRateClass() {
		return Df_UkrPoshta_Model_Request_Rate_Lightweight::_CLASS;
	}

	/** @return int */
	private function getDeliveryType() {
		/** @var int $result */
		$result = null;
		if ($this->getRmConfig()->service()->needGetCargoFromTheShopStore()) {
			$result =
					$this->needDeliverToHome()
				?
					1
				:
					2
			;
		}
		else {
			$result =
					$this->needDeliverToHome()
				?
					3
				:
					4
			;
		}
		df_result_integer($result);
		return $result;
	}

	/**
	 * 0 - доставка между разными областями Украины
	 * 1 - доставка в пределах одной области
	 * 2 - доставка в пределах одного города
	 * @return int
	 */
	private function getDistanceCategory() {
		/** @var int $result */
		$result = 0;
		/** @var bool $regionsAreSame */
		$regionsAreSame =
				(
						$this->getRequest()->getOriginRegionId()
					===
						$this->getRequest()->getDestinationRegionId()
				)
			||
				// Для случая, если администратор не включил модуль "Адресные справочники"
				(
						$this->getRequest()->getOriginRegionName()
					===
						$this->getRequest()->getDestinationRegionName()
				)
		;
		/** @var bool $locationsAreSame */
		$locationsAreSame =
			/**
			 * Как ни странно, тарифы УкрПочты не зависят от городов отправления и назначения,
			 * поэтому при расчёте информация о городах может отсутствовать.
			 * @link http://services.ukrposhta.com/CalcUtil/PostalMails.aspx
			 */
				$this->getRequest()->getOriginCity()
			&&
				($this->getRequest()->getOriginCity() === $this->getRequest()->getDestinationCity())
		;
		if ($locationsAreSame) {
			$result = 2;
		}
		else if ($regionsAreSame) {
			$result = 1;
		}
		df_result_integer($result);
		return $result;
	}

	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getQueryParams() {
		return array_merge(parent::getQueryParams(), array(
			'Mass' => rm_nat0($this->getRequest()->getWeightInGrammes())
			,'declaredAfterPayment' =>
				$this->getRmConfig()->service()->needAcceptCashOnDelivery()
				? rm_currency()->convertFromBaseToHryvnias($this->getRequest()->getPackageValue())
				: 0
			,'mailCategoryList' =>
				(0 < $this->getRmConfig()->admin()->getDeclaredValuePercent())
				? 'Declared'
				: 'Ordinary'
			,'hiddenDeliveryType' => $this->getDistanceCategory()
			,'isRegistered' => rm_bts(true)
			,'radioListTypes' => $this->getDeliveryType()
			,'switcher' => 'CourierDelivery31'
			,'withAddress' => rm_bts(false)
			,'withPacking' => rm_bts($this->getRmConfig()->service()->needPacking())
		));
	}

	const _CLASS = __CLASS__;
}