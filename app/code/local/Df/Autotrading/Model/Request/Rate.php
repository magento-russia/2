<?php
class Df_Autotrading_Model_Request_Rate extends Df_Shipping_Model_Request {
	/** @return int */
	public function getDeliveryTime() {
		if (!isset($this->{__METHOD__})) {
			$this->responseFailureDetect();
			$this->{__METHOD__} = df_text()->parseFirstInteger(
				$this->getPqSidebarChildren()->filter(":contains('Время')")->text()
			);
			df_assert_gt0($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getRate() {
		if (!isset($this->{__METHOD__})) {
			$this->responseFailureDetect();
			/** @var int|null $result */
			$result =
				df_text()->parseFirstInteger(
					$this->getPqSidebarChildren()->filter(":contains('Сумма')")->text()
					, false
				)
			;
			if (is_null($result)) {
				$this->logRequest();
				$this->logResponseAsHtml();
				df_error('Невозможно рассчитать стоимость доставки при данных условиях');
			}
			if (0 === $result) {
				df_notify_me(
					'Автотрейдинг: при расчёте тарифа получили 0.'
					."\nПараметры запроса:\n%s"
					."\nОтвет сервера:\n\n%s"
					,rm_print_params($this->getPostParameters())
					,$this->response()->text()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(),array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
			,'Accept-Encoding' => 'gzip, deflate'
			,'Accept-Language' => 'en-us,en;q=0.5'
			,'Connection' => 'keep-alive'
			,'Host' => 'www.ae5000.ru'
			,'Referer' => 'http://www.ae5000.ru/rates/calculate_v2/'
			,'User-Agent' => Df_Core_Const::FAKE_USER_AGENT
		));
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
	 * @return Df_Shipping_Model_Request
	 */
	protected function responseFailureDetectInternal() {
		/** @var phpQueryObject $pqErrors */
		$pqErrors = $this->response()->pq('.calculator .error_message ul li');
		if (0 < count($pqErrors)) {
			/** @var string[] $errors */
			$errors = array();
			foreach ($pqErrors as $nodeError) {
				/** @var DOMNode $nodeError */
				$errors[]= df_pq($nodeError)->text();
			}
			$this->responseFailureHandle(implode("\n", $errors));
		}
		return $this;
	}

	/** @return phpQueryObject */
	private function getPqSidebarChildren() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->response()->pq('.sidebar_div')->children();
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Autotrading_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}