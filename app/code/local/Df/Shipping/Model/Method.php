<?php
class Df_Shipping_Model_Method extends Mage_Shipping_Model_Rate_Result_Method {
	/**
	 * @param float $amountInRoubles
	 * @return float
	 */
	public function convertFromRoublesToBase($amountInRoubles) {
		return rm_currency()->convertFromRoublesToBase($amountInRoubles, $this->getRmStore());
	}

	/**
	 * @param string $message
	 * @return string
	 */
	public function evaluateMessage($message) {
		return $this->getCarrierInstance()->evaluateMessage($message, $this->getMessageVariables());
	}

	/** @return string */
	public function getCarrier() {
		return $this->_getData(self::P__CARRIER);
	}
	
	/** @return Df_Shipping_Model_Carrier */
	public function getCarrierInstance() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->_getData(self::P__CARRIER_INSTANCE);
			df_assert($this->{__METHOD__} instanceof Df_Shipping_Model_Carrier);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getCarrierTitle() {return $this->_getData(self::P__CARRIER_TITLE);}

	/** @return float */
	public function getCost() {return $this->_getData(self::P__COST);}

	/** @return string */
	public function getMethod() {return $this->_getData(self::P__METHOD);}

	/** @return Df_Shipping_Model_Carrier */
	public function getMethodInstance() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_h()->shipping()->getMagentoMainShippingModel()->getCarrierByCode($this->getCarrier())
			;
			df_assert($this->{__METHOD__} instanceof Df_Shipping_Model_Carrier);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getMethodTitle() {return $this->_getData(self::P__METHOD_TITLE);}

	/** @return string */
	public function getMethodTitleOriginal() {
		/** @var string $result */
		$result = $this->_getData(self::P__METHOD_TITLE);
		$result = df_trim($result, ':');
		if (!$result) {
			df_error('Безымянный способ доставки: %s', get_class($this));
		}
		return $result;
	}

	/**
	 * @override
	 * @return float
	 */
	public function getPrice() {
		return $this->getRmStore()->roundPrice($this->getCost() + $this->getHandlingFee());
	}
	
	/**
	 * Намеренно кэшируем результат нестандартно,
	 * потому что вроде как в некоторых сценариях Magento CE
	 * self::P__REQUEST может сначала быть пустым,
	 * а потом заполнен ядом Magento CE
	 * @return Df_Shipping_Model_Rate_Request|null
	 */
	public function getRequest() {
		/** @var Df_Shipping_Model_Rate_Request|null $result */
		$result = $this->_getData(self::P__REQUEST);
		if (!is_null($result)) {
			if (!($result instanceof Df_Shipping_Model_Rate_Request)) {
				df_assert($result instanceof Mage_Shipping_Model_Rate_Request);
				$result = $this->getCarrierInstance()->createRateRequest($result);
				$this->setData(self::P__REQUEST, $result);
			}
		}
		return $result;
	}

	/** @return Mage_Core_Model_Store */
	public function getRmStore() {
		return $this->getMethodInstance()->getRmStore();
	}

	/**
	 * Этот метод, как правило, перекрывают потомки
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = true;
		if ($result) {
			try {
				$this
					->checkCountryOriginIsNotEmpty()
					->checkCountryDestinationIsNotEmpty()
				;
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/** @return bool */
	public function needDisplayDiagnosticMessages() {
		return $this->getRmConfig()->frontend()->needDisplayDiagnosticMessages();
	}

	/**
	 * @param Df_Shipping_Model_Rate_Request $request
	 * @return Df_Shipping_Model_Method
	 */
	public function setRequest(Df_Shipping_Model_Rate_Request $request) {
		$this->setData(self::P__REQUEST, $request);
		return $this;
	}

	/**
	 * @param string $carrier
	 * @return Df_Shipping_Model_Method
	 */
	public function setCarrier($carrier) {
		df_param_string($carrier, 0);
		$this->setData(self::P__CARRIER, $carrier);
		return $this;
	}

	/**
	 *
		При оформлении заказа Magento игнорирует данное значение
		и берёт заголовок способа доставки из реестра настроек:
		public function getCarrierName($carrierCode)
		{
			if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
				return $name;
			}
			return $carrierCode;
		}
	 * @param string $carrierTitle
	 * @return Df_Shipping_Model_Method
	 */
	public function setCarrierTitle($carrierTitle) {
		df_param_string($carrierTitle, 0);
		$this->setData(self::P__CARRIER_TITLE, $carrierTitle);
		return $this;
	}

	/**
	 * cost = price - handling
	 *
	 * @param float $cost
	 * @return Df_Shipping_Model_Method
	 */
	public function setCost($cost) {
		df_param_float($cost, 0);
		$this->setData(self::P__COST, $cost);
		return $this;
	}

	/**
	 * @param string $method
	 * @return Df_Shipping_Model_Method
	 */
	public function setMethod($method) {
		df_param_string($method, 0);
		$this->setData(self::P__METHOD, $method);
		return $this;
	}

	/**
	 * @param string $methodTitle
	 * @return Df_Shipping_Model_Method
	 */
	public function setMethodTitle($methodTitle) {
		df_param_string($methodTitle, 0);
		$this->setData(self::P__METHOD_TITLE, $methodTitle);
		return $this;
	}

	/**
	 * @throws Df_Core_Exception_Client
	 * @param string $message
	 * @return void
	 */
	public function throwException($message) {
		/**
		 * Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
		 * @var mixed[] $arguments
		 */
		$arguments = func_get_args();
		$message = rm_sprintf($arguments);
		df_error(df_no_escape($this->evaluateMessage($message)));
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	public function throwExceptionInvalidCountryDestination() {
		$this->throwException(
			'Доставка <b>{в страну доставки}</b>'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	public function throwExceptionInvalidDestination() {
		$this->throwException(
			'Доставка <b>{в место доставки}</b>'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	public function throwExceptionInvalidOrigin() {
		$this->throwException(
			'Доставка <b>{из места отправки}</b>'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	public function throwExceptionNoCityDestination() {
		$this->throwException('Укажите город.');
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	public function throwExceptionNoCityOrigin() {
		$this->throwException('Администратор должен указать населённый пункт склада магазина.');
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCityDestinationIsNotEmpty() {
		if (!$this->getRequest()->getDestinationCity()) {
			$this->throwExceptionNoCityDestination();
		}
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCityOriginIsNotEmpty() {
		if (!$this->getRequest()->getOriginCity()) {
			$this->throwExceptionNoCityOrigin();
		}
		return $this;
	}

	/**
	 * @param string $countryIso2Code
	 * @return Df_Shipping_Model_Method
	 */
	protected function checkCountryDestinationIs($countryIso2Code) {
		$this->checkCountryDestinationIsNotEmpty();
		df_param_string_not_empty($countryIso2Code, 0);
		if ($countryIso2Code !==$this->getRequest()->getDestinationCountryId()) {
			$this->throwException(
				'Доставка {название службы и способа доставки в творительном падеже}'
				. ' возможна только <b>%s</b>.'
				, df_h()->directory()->country()->getByIso2Code($countryIso2Code)
					->getNameInFormDestination()
			);
		}
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryDestinationIsBerarus() {
		$this->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__BELARUS);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryDestinationIsKazakhstan() {
		$this->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__KAZAKHSTAN);
		return $this;
	}

	/**
	 * @param string $countryIso2Code
	 * @return Df_Shipping_Model_Method
	 */
	protected function checkCountryDestinationIsNot($countryIso2Code) {
		$this->checkCountryDestinationIsNotEmpty();
		if ($countryIso2Code === $this->getRequest()->getDestinationCountryId()) {
			$this->throwExceptionInvalidDestinationCountry();
		}
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryDestinationIsNotBerarus() {
		$this->checkCountryDestinationIsNot(Df_Directory_Helper_Country::ISO_2_CODE__BELARUS);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryDestinationIsNotKazakhstan() {
		$this->checkCountryDestinationIsNot(Df_Directory_Helper_Country::ISO_2_CODE__KAZAKHSTAN);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryDestinationIsNotRussia() {
		$this->checkCountryDestinationIsNot(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryDestinationIsNotUkraine() {
		$this->checkCountryDestinationIsNot(Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryDestinationIsNotEmpty() {
		if (is_null($this->getRequest()->getDestinationCountry())) {
			$this->throwExceptionNoCountryDestination();
		}
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryDestinationIsRussia() {
		$this->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryDestinationIsUkraine() {
		$this->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE);
		return $this;
	}

	/**
	 * @param string $countryIso2Code
	 * @return Df_Shipping_Model_Method
	 */
	protected function checkCountryOriginIs($countryIso2Code) {
		$this->checkCountryOriginIsNotEmpty();
		$this->getRequest()->checkCountryOriginIs($countryIso2Code);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryOriginIsBerarus() {
		$this->checkCountryOriginIs(Df_Directory_Helper_Country::ISO_2_CODE__BELARUS);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryOriginIsKazakhstan() {
		$this->checkCountryOriginIs(Df_Directory_Helper_Country::ISO_2_CODE__KAZAKHSTAN);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryOriginIsNotEmpty() {
		if (is_null($this->getRequest()->getOriginCountry())) {
			$this->throwExceptionNoCountryOrigin();
		}
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryOriginIsRussia() {
		$this->checkCountryOriginIs(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA);
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkCountryOriginIsUkraine() {
		$this->checkCountryOriginIs(Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE);
		return $this;
	}

	/**
	 * @param int|float $maxDimensionLimitInMetres
	 * @return Df_Shipping_Model_Method
	 */
	protected function checkDimensionMaxIsLE($maxDimensionLimitInMetres) {
		if ($this->getRequest()->getDimensionMaxRoughInMetres() > $maxDimensionLimitInMetres) {
			$this->throwException(strtr(
				'Доставка {название службы и способа доставки в творительном падеже} невозможна,'
				. ' потому что габариты груза не должны превышать {предел габаритов} м'
				. ' по любому из размеров, а габариты Вашего груза'
				. ' по одному из размеров: {наибольший из габаритов груза} м.'
				,array(
					'{предел габаритов}' => $this->formatDimension($maxDimensionLimitInMetres)
					,'{наибольший из габаритов груза}' =>
						$this->formatDimension($this->getRequest()->getDimensionMaxRoughInMetres())
				)
			));
		}
		return $this;
	}

	/**
	 * @param int|float $minDimensionLimitInMetres
	 * @return Df_Shipping_Model_Method
	 */
	protected function checkDimensionMinIsLE($minDimensionLimitInMetres) {
		if ($this->getRequest()->getDimensionMinRoughInMetres() > $minDimensionLimitInMetres) {
			$this->throwException(strtr(
				'Доставка {название службы и способа доставки в творительном падеже} невозможна,'
				. ' потому что наименьший их размеров груза по длине, ширине и высоте'
				. ' не должен превышать {предел габаритов} м,'
				. ' а наименьши из размеров Вашего груза равен {наибольший из габаритов груза} м.'
				,array(
					'{предел габаритов}' => $this->formatDimension($minDimensionLimitInMetres)
					,'{наибольший из габаритов груза}' =>
						$this->formatDimension($this->getRequest()->getDimensionMinRoughInMetres())
				)
			));
		}
		return $this;
	}

	/**
	 * @param int|float $sumDimensionsLimitInMetres
	 * @return Df_Shipping_Model_Method
	 */
	protected function checkDimensionsSumIsLE($sumDimensionsLimitInMetres) {
		if ($this->getRequest()->getDimensionsSumRoughInMetres() > $sumDimensionsLimitInMetres) {
			$this->throwException(strtr(
				'Доставка {название службы и способа доставки в творительном падеже} невозможна,'
				. ' потому что сумма размеров груза по длине, ширине и высоте'
				. ' не должны превышать {предел суммы габаритов} м,'
				. ' а для Вашего груза'
				. ' этот параметр составляет: {сумма габаритов груза} м.'
				,array(
					'{предел суммы габаритов}' => $this->formatDimension($sumDimensionsLimitInMetres)
					,'{сумма габаритов груза}' =>
						$this->formatDimension($this->getRequest()->getDimensionsSumRoughInMetres())
				)
			));
		}
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkOriginAndDestinationCitiesAreDifferent() {
		if (
			df_strings_are_equal_ci(
				$this->getRequest()->getOriginCity()
				,$this->getRequest()->getDestinationCity()
			)
		) {
			$this->throwException(
				df_cfg()->shipping()->message()->getFailureSameLocation(
					$this->getRequest()->getStoreId()
				)
			);
		}
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkOriginIsMoscow() {
		if (!$this->getRequest()->isOriginMoscow()) {
			$this->throwException(
				'Доставка {название службы и способа доставки в творительном падеже}'
				. ' возможна только из Москвы, однако склад магазина расположен {в месте отправки}.'
			);
		}
		return $this;
	}
	
	/** @return Df_Shipping_Model_Method */
	protected function checkRegionalCenterDestinationIsNotEmpty() {
		if (!$this->getRequest()->getDestinationRegionalCenter()) {
			$this->throwException(
				'К сожалению, у системы не получилось определить областной центр для региона «%s».'
				,$this->getRequest()->getDestinationRegionName()
			);
		}
		return $this;
	}	

	/** @return Df_Shipping_Model_Method */
	protected function checkRegionalCenterOriginIsNotEmpty() {
		if (!$this->getRequest()->getOriginRegionalCenter()) {
			$this->throwException(
				strtr(
					'Система не знает областной центр для региона «{регион}».'
					. 'Чтобы дать системе эту информацию,'
					. ' для {страны} надо установить справочник регионов.'
					,array(
						'{страны}' =>
							is_null($this->getRequest()->getOriginCountry())
							? '{страны}'
							: $this->getRequest()->getOriginCountry()->getNameInCaseGenitive()
						,'{регион}' => $this->getRequest()->getOriginRegionName()
					)
				)
			);
		}
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkRegionDestinationIsNotEmpty() {
		if (!$this->getRequest()->getDestinationRegionName()) {
			$this->throwExceptionNoRegionDestination();
		}
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	protected function checkRegionOriginIsNotEmpty() {
		if (!$this->getRequest()->getOriginRegionName()) {
			$this->throwExceptionNoRegionOrigin();
		}
		return $this;
	}

	/**
	 * @param int|float $weightInKilogrammes
	 * @return Df_Shipping_Model_Method
	 */
	protected function checkWeightIsGT($weightInKilogrammes) {
		if ($this->getRequest()->getWeightInKilogrammes() <= $weightInKilogrammes) {
			$this->throwExceptionInvalidWeight($weightInKilogrammes, 'больше');
		}
		return $this;
	}

	/**
	 * @param int|float $weightInKilogrammes
	 * @return Df_Shipping_Model_Method
	 */
	protected function checkWeightIsLE($weightInKilogrammes) {
		if ($this->getRequest()->getWeightInKilogrammes() > $weightInKilogrammes) {
			$this->throwExceptionInvalidWeight($weightInKilogrammes, 'не больше');
		}
		return $this;
	}

	/**
	 * @param int|null $timeOfDeliveryMin
	 * @param int|null $timeOfDeliveryMax[optional]
	 * @return string
	 */
	protected function formatTimeOfDelivery($timeOfDeliveryMin = null, $timeOfDeliveryMax = null) {
		if (!is_null($timeOfDeliveryMin)) {
			df_param_integer($timeOfDeliveryMin, 0);
		}
		if (!is_null($timeOfDeliveryMax)) {
			df_param_integer($timeOfDeliveryMax, 0);
		}
		/** @var string $result */
		$result =
			!$timeOfDeliveryMin && !$timeOfDeliveryMax
			? ''
			: rm_sprintf(
				'%s %s,'
				,!$timeOfDeliveryMax || ($timeOfDeliveryMin === $timeOfDeliveryMax)
				? $timeOfDeliveryMin
				: implode('-', array($timeOfDeliveryMin, $timeOfDeliveryMax))
				,$this->getTimeOfDeliveryNounForm(
					!$timeOfDeliveryMax || ($timeOfDeliveryMin === $timeOfDeliveryMax)
					? $timeOfDeliveryMin
					: $timeOfDeliveryMax
				)
			)
		;
		return $result;
	}

	/**
	 * @param string|null $locationName
	 * @param bool $isDestination [optional]
	 * @param bool $locationIsRegion [optional]
	 * @return int|string
	 */
	protected function getLocationId($locationName, $isDestination = true, $locationIsRegion = false) {
		df_param_string($locationName, 0);
		df_param_boolean($isDestination, 1);
		if (!$locationName) {
			if ($isDestination) {
				if ($locationIsRegion) {
					$this->throwExceptionNoRegionDestination();
				}
				else {
					$this->throwExceptionNoCityDestination();
				}
			}
			else {
				if ($locationIsRegion) {
					$this->throwExceptionNoRegionOrigin();
				}
				else {
					$this->throwExceptionNoCityOrigin();
				}
			}
		}
		/** @var string|int|null $result */
		$result = null;
		try {
			$result = $this->getLocationIdByName($locationName, $isDestination);
		}
		catch (Df_Core_Exception_Client $e) {}
		if (!$result) {
			if ($isDestination) {
				$this->throwExceptionInvalidDestination();
			}
			else {
				$this->throwExceptionInvalidOrigin();
			}
		}
		return $result;
	}

	/**
	 * @param string $locationName
	 * @param bool $isDestination[optional]
	 * @return string|int|null
	 */
	protected function getLocationIdByName($locationName, $isDestination = true) {
		return df_a($this->getLocations(), $this->normalizeLocationName($locationName));
	}

	/** @return string|int */
	protected function getLocationIdDestination() {
		return $this->getLocationId($this->getRequest()->getDestinationCity(), $isDestination = true);
	}

	/** @return string|int */
	protected function getLocationIdOrigin() {
		return $this->getLocationId($this->getRequest()->getOriginCity(), $isDestination = false);
	}

	/**
	 * @abstract
	 * @return string[]|int[]
	 */
	protected function getLocations() {
		df_error('Абстрактный метод');
		return array();
	}

	/** @return array(string => string) */
	protected function getMessageVariables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_merge(
					$this->getRequest()->getMessageVariables()
					,array(
						'{method}' => $this->getMethodTitleOriginal()
						,'{название способа доставки в именительном падеже}' => $this->getMethodTitleOriginal()
						,'{название службы и способа доставки в творительном падеже}' =>
							strtr(
								$this->getCarrierInstance()->hasTheOnlyMethod()
								? 'службой «<b>{carrier}</b>»'
								: 'способом «<b>{method}</b>» службы «<b>{carrier}</b>»'
								,array(
									'{carrier}' => $this->getCarrierTitle()
									,'{method}' => $this->getMethodTitleOriginal()
								)
							)
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param int $timeInDays
	 * @return string
	 */
	protected function getTimeOfDeliveryNounForm($timeInDays) {
		df_param_integer($timeInDays, 0);
		/** @var string $result */
		$result = df_text()->getNounForm($timeInDays, array('день', 'дня', 'дней'));
		df_result_string($result);
		return $result;
	}

	/** @return Df_Shipping_Model_Config_Facade */
	protected function getRmConfig() {return $this->getMethodInstance()->getRmConfig();}

	/** @return bool */
	protected function needShowMethodName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!df_is_admin() && $this->getRmConfig()->frontend()->needShowMethodName()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $locationName
	 * @return string
	 */
	protected function normalizeLocationName($locationName) {
		return mb_strtoupper(df_trim(df_nts($locationName)));
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	protected function throwExceptionCalculateFailure() {
		$this->throwException(
			df_cfg()->shipping()->message()->getFailureGeneral($this->getRequest()->getStoreId())
		);
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	protected function throwExceptionInvalidDestinationCountry() {
		$this->throwException(
			'Доставка {в страну доставки}'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/**
	 * @return Df_Shipping_Model_Method
	 * @throws Exception
	 */
	protected function throwExceptionNoRate() {
		$this->throwException(
			'Доставка {название службы и способа доставки в творительном падеже}'
			.' невозможна по причине невозможности тарификации.'
		);
		return $this;
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	protected function throwExceptionNoCountryDestination() {
		$this->throwException('Укажите страну.');
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	protected function throwExceptionNoCountryOrigin() {
		$this->throwException(
			'Администратор должен указать страну магазина в графе'
			. ' «Система» → «Настройки» → «Продажи» → «Доставка: общие настройки» →'
			. ' «Расположение магазина» → «Страна».'
		);
	}

	/** @return float */
	private function getHandlingFee() {
		if (!isset($this->{__METHOD__})) {
			/** @var float $result */
			$this->{__METHOD__} =
					$this->getCost()
				*
					$this->getRmConfig()->admin()->feePercent()
				/
					100
				+
					$this->getRmConfig()->admin()->feeFixed()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param float $dimensionInMetres
	 * @return string
	 */
	private function formatDimension($dimensionInMetres) {
		return rm_sprintf(is_int($dimensionInMetres) ? '%d' : '%.1f', $dimensionInMetres);
	}

	/**
	 * @param int|float $maxWeightInKilogrammes
	 * @param string $conditionAsText
	 * @throws Df_Core_Exception_Client
	 */
	private function throwExceptionInvalidWeight($maxWeightInKilogrammes, $conditionAsText) {
		$this->throwException(strtr(
			'Доставка {название службы и способа доставки в творительном падеже} невозможна,'
			. ' потому что вес груза должен быть {не больше|меньше} {предел веса} кг,'
			. ' а вес Вашего заказа: {вес заказа} кг.'
			,array(
				'{не больше|меньше}' => $conditionAsText
				,'{предел веса}' => rm_flits($maxWeightInKilogrammes, 1)
				,'{вес заказа}' => $this->getRequest()->getWeightKgSD()
			)
		));
	}

	/** @return Df_Shipping_Model_Method */
	private function throwExceptionNoRegionDestination() {
		$this->throwException('Укажите область.');
		return $this;
	}

	/** @return Df_Shipping_Model_Method */
	private function throwExceptionNoRegionOrigin() {
		$this->throwException(
			'Администратор магазина должен указать область магазина в графе'
			. ' «Система» → «Настройки» → «Продажи» → «Доставка:'
			. ' общие настройки»→ «Расположение магазина» → «Область, край, республика».'
		);
		return $this;
	}

	const _CLASS = __CLASS__;
	const P__CARRIER = 'carrier';
	const P__CARRIER_INSTANCE = 'carrier_instance';
	const P__CARRIER_TITLE = 'carrier_title';
	const P__COST = 'cost';
	const P__METHOD = 'method';
	const P__METHOD_TITLE = 'method_title';
	const P__REQUEST = 'request';
}