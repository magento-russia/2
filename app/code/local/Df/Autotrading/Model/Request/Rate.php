<?php
class Df_Autotrading_Model_Request_Rate extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @return int
	 */
	protected function _getDeliveryTime() {
		return df_t()->firstInteger($this->getPqSidebarChildren()->filter(":contains('Время')")->text());
	}

	/**
	 * @override
	 * @return int
	 */
	protected function _getRate() {
		return df_t()->firstInteger($this->getPqSidebarChildren()->filter(":contains('Сумма')")->text());
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
			,'Accept-Encoding' => 'gzip, deflate'
			,'Accept-Language' => 'en-us,en;q=0.5'
			,'Connection' => 'keep-alive'
			,'Host' => 'www.ae5000.ru'
			,'Referer' => 'http://www.ae5000.ru/rates/calculate_v2/'
		) + parent::getHeaders();
	}

	/** @return array(string => string|int|float|bool) */
	protected function getPostParameters() {
		// «Тип груза»: «1 место»
		return array_merge(parent::getPostParameters(), array('type' => 'single'));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'www.ae5000.ru';}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/rates/calculate_v2/';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function responseFailureDetect() {
		/** @var phpQueryObject $pqErrors */
		$pqErrors = $this->response()->pq('.calculator .error_message ul li');
		if ($pqErrors->count()) {
			/** @var string[] $errors */
			$errors = array();
			foreach ($pqErrors as $nodeError) {
				/** @var DOMNode $nodeError */
				$errors[]= df_pq($nodeError)->text();
			}
			df_error(df_concat_n($errors));
		}
	}

	/** @return phpQueryObject */
	private function getPqSidebarChildren() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->response()->pq('.sidebar_div')->children();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Autotrading_Model_Api_Calculator::getApi()
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Autotrading_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}