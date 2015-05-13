<?php
abstract class Df_UkrPoshta_Model_Method_Universal extends Df_UkrPoshta_Model_Method {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTransportType();

	/**
	 * @override
	 * @return Df_UkrPoshta_Model_Request_Rate_Universal
	 */
	protected function createApiRate() {
		return Df_UkrPoshta_Model_Request_Rate_Universal::i($this->getQueryParams());
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getApiRateClass() {
		return Df_UkrPoshta_Model_Request_Rate_Universal::_CLASS;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getQueryParams() {
		return array_merge(parent::getQueryParams(), array(
			'book' => rm_bts(false)
			,'country' =>
					(
							Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE
						===
							$this->getRequest()->getDestinationCountryId()
					)
				? 'NotACountry'
				: $this->getDestinationCountryNumericCode()
			,'direction' =>
					(
							Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE
						===
							$this->getRequest()->getDestinationCountryId()
					)
				? 'Ukraine'
				: 'ForeignCountries'
			,'handPersonally' => rm_bts(true)
			,'isBulky' => rm_bts(false)
			,'isFragile' => rm_bts(false)
			,'mailCategory' =>
				(0 < $this->getRmConfig()->admin()->getDeclaredValuePercent())
				? 'Declared'
				: 'Ordinary'
			,'mailKind' => 'Parcel'
			,'mass' => $this->getWeightKilogrammes()
			,'massGramme' => $this->getWeightGrammes()
			,'packing' => rm_bts($this->getRmConfig()->service()->needPacking())
			,'postpay' =>
				$this->getRmConfig()->service()->needAcceptCashOnDelivery()
				? rm_currency()->convertFromBaseToHryvnias($this->getRequest()->getPackageValue())
				: 0
			,'region' =>
				rm_bts(
					/**
					 * Обратите внимание,
					 * что тарифы УкрПочты не зависят от городов отправления и назначения,
					 * поэтому при расчёте информация о городах может отсутствовать.
					 * @link http://services.ukrposhta.com/CalcUtil/PostalMails.aspx
					 */
					$this->getRequest()->isDestinationCityRegionalCenter()
				)
			,'senderKind' => 'LegalEntity'
			,'switcher' => 'PostalMails'
			,'transferMethod' => $this->getTransportType()
			,'withAddress' => rm_bts(false)
			,'withF103' => rm_bts(false)
			,'withHanding' => rm_bts($this->getRmConfig()->service()->needPacking())
			,'withMessenger' => rm_bts($this->getRmConfig()->service()->enableSmsNotification())
			,'courierDostList' => $this->getDeliveryType()
		));
	}

	/** @return string */
	private function getDeliveryType() {
		/** @var string $result */
		$result = null;
		if ($this->getRmConfig()->service()->needGetCargoFromTheShopStore()) {
			$result =
					$this->needDeliverToHome()
				?
					'Zabir_Arrived'
				:
					'Zabir'
			;
		}
		else {
			$result =
					$this->needDeliverToHome()
				?
					'Arrived'
				:
					'Choose'
			;
		}
		df_result_string($result);
		return $result;
	}

	/**
	 * На всякий случай возвращаем строку, а не число,
	 * потому что первыми символами могут быть нули
	 * @return string
	 */
	private function getDestinationCountryNumericCode() {
		/** @var string $result */
		$result =
			strval(
				df_a(
					Zend_Locale::getTranslationList('NumericToTerritory', 'uk_UA')
					,$this->getRequest()->getDestinationCountryId()
				)
			)
		;
		df_result_string($result);
		return $result;
	}

	/** @return int */
	private function getWeightGrammes() {
		return
			rm_nat0(
					$this->getRequest()->getWeightInGrammes()
				-
					(1000 * $this->getWeightKilogrammes())
			)
		;
	}

	/** @return int */
	private function getWeightKilogrammes() {
		/** @var int $result */
		$result =
			intval(
				floor(
					$this->getRequest()->getWeightInKilogrammes()
				)
			)
		;
		df_result_integer($result);
		return $result;
	}

	const _CLASS = __CLASS__;
}