<?php
class Df_RussianPost_Model_RussianPostCalc_Request extends Df_Shipping_Model_Request {
	/** @return string[] */
	public function getRatesAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->responseFailureDetect();
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
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
			,'Host' => 'russianpostcalc.ru'
			,'Referer' => 'http://russianpostcalc.ru/'
			,'User-Agent' => Df_Core_Const::FAKE_USER_AGENT
		);
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
	 * @return Df_RussianPost_Model_RussianPostCalc_Request
	 * @throws Exception
	 */
	protected function responseFailureDetectInternal() {
		/** @var string[] $errorMessages */
		$errorMessages = array();
		foreach ($this->response()->pq('#content .errors ul li') as $errorListItem) {
			/** @var DOMNode $errorListItem */
			$errorMessages[]= df_trim($errorListItem->textContent);
		}
		if (0 < count($errorMessages)) {
			/** @var bool $handled */
			$handled = false;
			if (1 === count($errorMessages)) {
				/** @var string $theOnlyMessage */
				$theOnlyMessage = rm_first($errorMessages);
				if ('Неверно введен индекс получателя!' === $theOnlyMessage) {
					$handled = true;
					/** @var string $destinationPostalCode */
					$destinationPostalCode =
						df_a($this->getPostParameters(), self::POST_PARAM__DESTINATION__POSTAL_CODE)
					;
					df_error(
						df_no_escape(
							rm_sprintf(
								'Похоже, что почтовый индекс «%s», который Вы ввели, не существует в России.'
								. '<br/>Пожалуйста, уточните Ваш правильный почтовый индекс на сайте'
								. ' <a href="http://ruspostindex.ru/">ruspostindex.ru</a>.'
								,$destinationPostalCode
							)
						)
					);
				}
			}
			if (!$handled) {
				$this->responseFailureHandle(df_quote_and_concat($errorMessages));
			}
		}
		return $this;
	}
	const _CLASS = __CLASS__;
	const POST_PARAM__DECLARED_VALUE = 'ob_cennost_rub';
	const POST_PARAM__DESTINATION__POSTAL_CODE = 'to_index';
	const POST_PARAM__SOURCE__POSTAL_CODE = 'from_index';
	const POST_PARAM__WEIGHT = 'weight';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_RussianPost_Model_RussianPostCalc_Request
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}