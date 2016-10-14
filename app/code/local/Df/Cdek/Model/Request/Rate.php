<?php
class Df_Cdek_Model_Request_Rate extends Df_Cdek_Model_Request {
	/** @return int */
	public function getServiceId() {return $this->call(__FUNCTION__);}

	/**
	 * @override
	 * @return int|string
	 */
	protected function _getDeliveryTimeMax() {return $this->getResponseSuccessData('deliveryPeriodMax');}

	/**
	 * @override
	 * @return int|string
	 */
	protected function _getDeliveryTimeMin() {return $this->getResponseSuccessData('deliveryPeriodMin');}

	/**
	 * @override
	 * @return float|int|string
	 */
	protected function _getRate() {return $this->getResponseSuccessData('price');}

	/** @return int */
	protected function _getServiceId() {return rm_int($this->getResponseSuccessData('tariffId'));}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array('Content-Type' => 'application/json') + parent::getHeaders();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPostRawData() {
		return Zend_Json::encode(array_merge(array('version' => '1.0'), $this->getPostParameters()));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/calculator/calculate_price_by_json.php';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needLogNonExceptionErrors() {return false;}

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function responseFailureDetect() {
		/** @var mixed[][]|null $errors */
		$errors = $this->response()->json('error');
		if ($errors)  {
			df_error(df_concat_n(array_column($errors, 'text')));
		}
	}

	/**
	 * Обратите внимание, что по состоянию 2013-06-10 API не рассчитал стоимость доставки
	 * из Санкт-Петербурга в Могилёв-Подольский, а вот калькулятор на сайте СДЭК — рассчитал.
	 * Сервер СДЭК может давать сбой:
	 * Notice: Undefined variable: data in
	 * /var/www/api/calculator/library_calculate_price/CalculateDelivery.php on line 202
	 * @override
	 * @return string
	 */
	protected function getResponseAsTextInternal() {
		return preg_replace(
			"#Notice:[^\n]+\n#"
			,''
			,parent::getResponseAsTextInternal()
		);
	}

	/**
	 * @param string $key
	 * @return mixed|null
	 */
	private function getResponseSuccessData($key) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->response()->json('result');
			df_result_array($this->{__METHOD__});
		}
		return df_a($this->{__METHOD__}, $key);
	}

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cdek_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}