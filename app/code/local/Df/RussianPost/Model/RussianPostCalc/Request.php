<?php
class Df_RussianPost_Model_RussianPostCalc_Request extends Df_Shipping_Model_Request {
	/** @return string[] */
	public function getRatesAsText() {return $this->call(__FUNCTION__);}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
			,'Host' => 'russianpostcalc.ru'
			,'Referer' => 'http://russianpostcalc.ru/'
		) + parent::getHeaders();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'russianpostcalc.ru';}

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
		/** @var string[] $errorMessages */
		$errorMessages = array();
		foreach ($this->response()->pq('#content .errors ul li') as $errorListItem) {
			/** @var DOMNode $errorListItem */
			$errorMessages[]= df_trim($errorListItem->textContent);
		}
		if ($errorMessages) {
			/** @var bool $handled */
			$handled = false;
			if (1 === count($errorMessages)) {
				/** @var string $theOnlyMessage */
				$theOnlyMessage = rm_first($errorMessages);
				if ('Неверно введен индекс получателя!' === $theOnlyMessage) {
					$handled = true;
					/** @var string $destinationPostalCode */
					$destinationPostalCode =
						df_a($this->getPostParameters(), self::$POST_PARAM__DESTINATION__POSTAL_CODE)
					;
					df_error(df_no_escape(sprintf(
						'Похоже, что почтовый индекс «%s», который Вы ввели, не существует в России.'
						. '<br/>Пожалуйста, уточните Ваш правильный почтовый индекс на сайте'
						. ' <a href="http://ruspostindex.ru/">ruspostindex.ru</a>.'
						,$destinationPostalCode
					)));
				}
			}
			if (!$handled) {
				df_error(df_csv_pretty_quote($errorMessages));
			}
		}
	}

	/** @return string[] */
	private function _getRatesAsText() {
		/** @var string[] $result */
		$result = array();
		foreach ($this->response()->pq('#content > p') as $paragraph) {
			/** @var DOMNode $paragraph */
			/** @var string $nodeValue */
			$nodeValue = df_trim($paragraph->nodeValue);
			if (rm_starts_with($nodeValue, 'Доставка Почтой России')) {
				// строка вида:
				// Доставка Почтой России: 347.6 руб. Контрольный срок: 14* дн.
				// или:
				// Доставка Почтой России 1 класс: 382.44 руб. Контрольный срок: 4* дн
				$result[]= $nodeValue;
			}
		}
		return $result;
	}

	/** @var string */
	private static $POST_PARAM__DESTINATION__POSTAL_CODE = 'to_index';
	/**
	 * @static
	 * @param string $sourcePostalCode
	 * @param string $destinationPostalCode
	 * @param float $weight
	 * @param float $declaredValue
	 * @return Df_RussianPost_Model_RussianPostCalc_Request
	 */
	public static function i($sourcePostalCode, $destinationPostalCode, $weight, $declaredValue) {
		df_param_string_not_empty($sourcePostalCode, 0);
		df_param_string_not_empty($destinationPostalCode, 1);
		df_param_float($weight, 2);
		df_assert_gt0($weight);
		df_param_float($declaredValue, 3);
		return new self(array(self::P__POST_PARAMS => array(
			'from_index' => $sourcePostalCode
			,self::$POST_PARAM__DESTINATION__POSTAL_CODE => $destinationPostalCode
			,'weight' => $weight
			,'ob_cennost_rub' => $declaredValue
			,'russianpostcalc' => 1
		)));
	}
}