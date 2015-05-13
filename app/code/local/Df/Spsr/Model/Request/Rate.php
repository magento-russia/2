<?php
class Df_Spsr_Model_Request_Rate extends Df_Spsr_Model_Request {
	/**
	 * @override
	 * @return string
	 */
	public function getErrorMessage() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Varien_Simplexml_Element|null $errorMessageAsSimpleXml */
			$errorMessageAsSimpleXml = $this->response()->xml('/root/Error');
			$this->{__METHOD__} =
				is_null($errorMessageAsSimpleXml)
				? ''
				: (string)$errorMessageAsSimpleXml
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(array(string => string|int)) */
	public function getRates() {
		if (!isset($this->{__METHOD__})) {
			/** @var array[] $result */
			$result = array();
			/** @var Df_Varien_Simplexml_Element[] $tariffsAsSimpleXml */
			$tariffsAsSimpleXml = $this->response()->xml('/root/Tariff', $all = true);
			try {
				foreach ($tariffsAsSimpleXml as $tariffAsSimpleXml) {
					/** @var Df_Varien_Simplexml_Element $tariffAsSimpleXml */
					/** @var array(string => string) $tariffAsArray */
					$tariffAsArray = $tariffAsSimpleXml->asArray();
					/** @var int $cost */
					$cost = ceil(rm_float(df_a($tariffAsArray, 'Total_Dost')));
					if (0 < $cost) {
						/** @var int[] $deliveryTimeAsArray */
						$deliveryTimeAsArray = rm_int(explode('-', df_a($tariffAsArray, 'DP')));
						$result[]=
							array(
								self::RATE__TITLE =>
									strtr(
										df_a($tariffAsArray,'TariffType')
										,array(
											'Услуги по доставке ' => ''
											,'Услуга по экспорту отправлений' => ''
											,'"' => ''
										)
									)
								,self::RATE__COST => $cost
								,self::RATE__TIME_OF_DELIVERY__MIN => rm_first($deliveryTimeAsArray)
								,self::RATE__TIME_OF_DELIVERY__MAX => rm_last($deliveryTimeAsArray)
							)
						;
					}
				}
			}
			catch (Exception $e) {
				Mage::logException($e);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string|array(string => string)
	 */
	protected function getQuery() {return 'TARIFFCOMPUTE_2&' . http_build_query($this->getQueryParams());}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/cgi-bin/postxml.pl';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::GET;}

	const _CLASS = __CLASS__;
	const POST__DECLARED_VALUE = 'Amount';
	const POST__NOTIFY_RECIPIENT_BY_SMS = 'SMS_Recv';
	const POST__NOTIFY_SENDER_BY_SMS = 'SMS';
	const POST__ENDORSE_DELIVERY_TIME = 'BeforeSignal';
	const POST__INSURANCE_TYPE = 'AmountCheck';
	const POST__LOCATION__DESTINATION = 'ToCity';
	const POST__LOCATION__SOURCE = 'FromCity';
	const POST__WEIGHT = 'Weight';
	const RATE__COST = 'cost';
	const RATE__TIME_OF_DELIVERY__MIN = 'time_of_delivery__min';
	const RATE__TIME_OF_DELIVERY__MAX = 'time_of_delivery__max';
	const RATE__TITLE = 'title';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Spsr_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__QUERY_PARAMS => $parameters));
	}
}