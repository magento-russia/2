<?php
class Df_Dellin_Model_Request_Rate extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @return int
	 */
	protected function _getDeliveryTime() {
		return
			!$this->getDateDeparture() || !$this->getDateArrival()
			? 0
			:
					df()->date()->getNumberOfDaysBetweenTwoDates(
						$this->getDateDeparture(), $this->getDateArrival()
					)
				+
					// Добавляем по 2 дня на каждое дополнительное перемещение между пунктами.
					// Узлы для дополнительных перемещений могут содержать текст типа:
					// «Срок доставки груза из г. Новороссийск в г. Анапа(Красн.кр.) составит 1 день.
					// Отправка из филиала осуществляется по вс,пн,вт,чт..»
					2 * (count($this->getDateNodes()) - 2)
		;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function _getRate() {return rm_leaf_f($this->response()->xml('/data/price'));}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array(
			'Accept-Encoding' => 'gzip, deflate'
			,'Accept-Language' => 'en-us,en;q=0.5'
			,'Connection' => 'keep-alive'
			,'Host' => $this->getQueryHost()
		) + parent::getHeaders();
	}
	
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'public.services.dellin.ru';}

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	protected function getQueryParams() {
		return array_merge(parent::getQueryParams(),array(
			'date' => df_dts(df()->date()->tomorrow(), 'y-MM-dd')
			,'request' => 'xmlResult'
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/calculatorService2/index.html';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::GET;}

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function responseFailureDetect() {
		/** @var Df_Core_Sxe|null $errorNode */
		$errorNode = $this->response()->xml('/data/error');
		if (!is_null($errorNode)) {
			// Раньше текст диагностического сообщения имел шаблон:
			// При обращении к API службы «Деловые Линии» произошёл сбой: «%s».
			// Однако, как я понимаю, покупателям не нужно знать ни о каких API.
			df_error(df_quote_russian(df_trim(rm_leaf_s($errorNode))));
		}
	}
	
	/** @return Zend_Date|null */
	private function getDateArrival() {return rm_last($this->getDates());}
	
	/** @return Zend_Date|null */
	private function getDateDeparture() {return rm_first($this->getDates());}
	
	/** @return mixed[] */
	private function getDateNodes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->response()->xml('/data/time/part', $all = true);
			df_assert_array($this->{__METHOD__});
			df_assert_ge(2, count($this->{__METHOD__}));
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * @return Zend_Date[]
	 * @throws Exception
	 */
	private function getDates() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_merge_single(
				array_map(array($this, 'parseDates'), $this->getDateNodes())
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что один узел может содержатиь несколько дат, например:
	 * «Ближайшая отправка из г. Москва в направлении г. Новороссийск 01.10.2013.
	   Груз, отправленный этим рейсом, прибудет в г. Новороссийск ориентировочно 06.10.2013.».
	 * @param SimpleXMLElement $dateNode
	 * @return Zend_Date[]
	 */
	private function parseDates(SimpleXMLElement $dateNode) {
		/** @var Zend_Date[] $result */
		$result = array();
		/** @var string $dateNodeAsText */
		$dateNodeAsText = rm_leaf_sne($dateNode);
		/** @var string[] $matches */
		$matches = array();
		/** @var int $r */
		preg_match_all('#\d+\.\d+\.\d+#', $dateNodeAsText, $matches);
		/** @var string $dateAsText */
		if ($matches) {
			/** @var string[] $datesAsText */
			$datesAsText = df_a($matches, 0);
			foreach ($datesAsText as $dateAsText) {
				/** @var string $dateAsText */
				df_assert_string_not_empty($dateAsText);
				$result[] = new Zend_Date($dateAsText, $part = 'dd.MM.y');
			}
		}
		return $result;
	}

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Dellin_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__QUERY_PARAMS => $parameters));
	}
}