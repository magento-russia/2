<?php
class Df_Garantpost_Model_Request_DeliveryTime_Export extends Df_Garantpost_Model_Request_DeliveryTime {
	/**
	 * @return int
	 * @throws Exception
	 */
	public function getCapitalMax() {
		/** @var int $result */
		$result =
			df_a(
				df_a(
					$this->getResultAsInterval()
					,self::RESULT__CAPITAL
					,array()
				)
				,self::RESULT__DELIVERY_TIME__MAX
				,0
			)
		;
		try {
			df_result_integer($result);
		}
		catch (Exception $e) {
			df_notify_exception($e);
			if (df_is_it_my_local_pc()) {
				df_error($e);
			}
			$result = 0;
		}
		return $result;
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	public function getCapitalMin() {
		/** @var int $result */
		$result =
			df_a(
				df_a(
					$this->getResultAsInterval()
					,self::RESULT__CAPITAL
					,array()
				)
				,self::RESULT__DELIVERY_TIME__MIN
				,0
			)
		;
		try {
			df_result_integer($result);
		}
		catch (Exception $e) {
			//$this->logRequest($e);
			$this->logResponseAsHtml();
			df_notify_exception($e);
			if (df_is_it_my_local_pc()) {
				df_error($e);
			}
			$result = 0;
		}
		return $result;
	}

	/** @return int */
	public function getNonCapitalMax() {
		/** @var int $result */
		$result =
			df_a(
				df_a(
					$this->getResultAsInterval()
					,self::RESULT__NON_CAPITAL
					,array()
				)
				,self::RESULT__DELIVERY_TIME__MAX
				,0
			)
		;
		df_result_integer($result);
		return $result;
	}

	/** @return int */
	public function getNonCapitalMin() {
		/** @var int $result */
		$result =
			df_a(
				df_a(
					$this->getResultAsInterval()
					,self::RESULT__NON_CAPITAL
					,array()
				)
				,self::RESULT__DELIVERY_TIME__MIN
				,0
			)
		;
		df_result_integer($result);
		return $result;
	}

	/** @return array(string => array(string => int)) */
	public function getResultAsInterval() {
		if (!isset($this->{__METHOD__})) {
			/** @var phpQueryObject $pqDeliveryTime */
			$pqDeliveryTime = $this->response()->pq('#body_min_height table:first tr');
			/** @var string $deliveryTimeToCapitalAsText */
			$deliveryTimeToCapitalAsText = df_trim(df_pq('td', $pqDeliveryTime->eq(1))->text());
			df_assert_string($deliveryTimeToCapitalAsText);
			/** @var string $deliveryTimeToOtherLocationsAsText */
			$deliveryTimeToOtherLocationsAsText =
				df_trim(df_pq('td', $pqDeliveryTime->eq(2))->text())
			;
			df_assert_string($deliveryTimeToOtherLocationsAsText);
			/** @var $deliveryTimeToCapitalAsArray */
			$deliveryTimeToCapitalAsArray = explode('-', $deliveryTimeToCapitalAsText);
			df_assert_array($deliveryTimeToCapitalAsArray);
			/** @var $deliveryTimeToOtherLocationsAsArray */
			$deliveryTimeToOtherLocationsAsArray = explode('-', $deliveryTimeToOtherLocationsAsText);
			df_assert_array($deliveryTimeToOtherLocationsAsArray);
			$this->{__METHOD__} =
				array(
					self::RESULT__CAPITAL =>
						array(
							self::RESULT__DELIVERY_TIME__MIN =>
								df_a($deliveryTimeToCapitalAsArray, 0)
							,self::RESULT__DELIVERY_TIME__MAX =>
								df_a($deliveryTimeToCapitalAsArray, 1)
						)
					,self::RESULT__NON_CAPITAL =>
						array(
							self::RESULT__DELIVERY_TIME__MIN =>
								df_a($deliveryTimeToOtherLocationsAsArray, 0)
							,self::RESULT__DELIVERY_TIME__MAX =>
								df_a($deliveryTimeToOtherLocationsAsArray, 1)
						)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array('Referer' => 'http://www.garantpost.ru/tools/transint') + parent::getHeaders();
	}

	/** @return array(string => int) */
	protected function getQueryParams() {
		return array(
			'if_submit' => 1
			,self::POST_PARAM__DESTINATION_COUNTRY_ID => $this->getDestinationCountryId()
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/tools/transint';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::GET;}

	/** @return int */
	private function getDestinationCountryId() {
		return df_a(
			Df_Garantpost_Model_Request_Countries_ForDeliveryTime::s()->getResponseAsArray()
			, $this->getDestinationCountryIso2()
			, 0
		);
	}

	/**
	 * Возвращает 2-буквенный код страны по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * @return string
	 */
	private function getDestinationCountryIso2() {return $this->cfg(self::P__DESTINATION_COUNTRY_ISO2);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__DESTINATION_COUNTRY_ISO2, RM_V_ISO2);
	}
	const _C = __CLASS__;
	/**
	 * 2-буквенный код страны по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 */
	const P__DESTINATION_COUNTRY_ISO2  = 'destination_country_iso2';
	const POST_PARAM__DESTINATION_COUNTRY_ID = 'cid';
	const RESULT__CAPITAL = 'capital';
	const RESULT__DELIVERY_TIME__MAX= 'delivery_time__max';
	const RESULT__DELIVERY_TIME__MIN = 'delivery_time__min';
	const RESULT__NON_CAPITAL = 'non_capital';
	/**
	 * @static
	 * @param string $destinationCountryIso2Code
	 * @return Df_Garantpost_Model_Request_DeliveryTime_Export
	 */
	public static function i($destinationCountryIso2Code) {
		df_param_iso2($destinationCountryIso2Code, 0);
		return new self(array(
			self::P__DESTINATION_COUNTRY_ISO2 => $destinationCountryIso2Code
		));
	}
}