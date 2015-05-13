<?php
class Df_Cdek_Model_Request_Rate extends Df_Cdek_Model_Request {
	/** @return int */
	public function getDeliveryTimeMax() {
		if (!isset($this->{__METHOD__})) {
			$this->responseFailureDetect();
			$this->{__METHOD__} = rm_nat0(df_a($this->getResponseSuccessData(), 'deliveryPeriodMax'));
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getDeliveryTimeMin() {
		if (!isset($this->{__METHOD__})) {
			$this->responseFailureDetect();
			$this->{__METHOD__} = rm_nat0(df_a($this->getResponseSuccessData(), 'deliveryPeriodMin'));
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getRate() {
		if (!isset($this->{__METHOD__})) {
			$this->responseFailureDetect();
			$this->{__METHOD__} = rm_float(df_a($this->getResponseSuccessData(), 'price'));
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getServiceId() {
		if (!isset($this->{__METHOD__})) {
			$this->responseFailureDetect();
			$this->{__METHOD__} = rm_int(df_a($this->getResponseSuccessData(), 'tariffId'));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array('Content-Type' => 'application/json'));
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
	 * @return Df_Shipping_Model_Request
	 */
	protected function responseFailureDetectInternal() {
		/** @var mixed[][]|null $errors */
		$errors = $this->response()->json('error');
		if (!is_null($errors))  {
			df_assert_array($errors);
			rm_nat(count($errors));
			/** @var string[] $messages */
			$messages = array();
			foreach ($errors as $error) {
				/** @var mixed[] $error */
				df_assert_array($error);
				/** @var string $message */
				$message = df_a($error, 'text');
				df_assert_string($message);
				$messages[]= $message;
			}
			$this->responseFailureHandle(implode("\n", $messages));
		}
		return $this;
	}

	/**
	 * Обратите внимание, что по состоянию 2013-06-10 API не рассчитал стоимость доставки
	 * из Санкт-Петербурга в Могилёв-Подольский, а вот калькулятор на сайте СДЭК — рассчитал
	 * @override
	 * @return string
	 */
	protected function getResponseAsTextInternal() {
		return
			/**
			 * Сервер СДЭК может давать сбой:
			 * Notice: Undefined variable: data in
			 * /var/www/api/calculator/library_calculate_price/CalculateDelivery.php on line 202
			 */
			preg_replace(
				"#Notice:[^\n]+\n#"
				,''
				,parent::getResponseAsTextInternal()
			)
		;
	}

	/** @return mixed[] */
	private function getResponseSuccessData() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->response()->json('result');
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cdek_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}