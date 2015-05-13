<?php
class Df_RussianPost_Model_RussianPostCalc_Api extends Df_Core_Model_Abstract {
	/** @return string[] */
	public function getRatesAsText() {
		return $this->getRequest()->getRatesAsText();
	}

	/** @return float */
	private function getDeclaredValue() {
		return $this->cfg(self::P__DECLARED_VALUE);
	}

	/** @return string */
	private function getDestinationPostalCode() {
		/** @var string $result */
		$result = $this->_getData(self::P__DESTINATION__POSTAL_CODE);
		df_h()->shipping()->assertPostalCodeDestination($result);
		return $result;
	}

	/** @return Df_RussianPost_Model_RussianPostCalc_Request */
	private function getRequest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_RussianPost_Model_RussianPostCalc_Request::i(
				df_text()->convertUtf8ToWindows1251(array(
					Df_RussianPost_Model_RussianPostCalc_Request
						::POST_PARAM__SOURCE__POSTAL_CODE => $this->getSourcePostalCode()
					,Df_RussianPost_Model_RussianPostCalc_Request
						::POST_PARAM__DECLARED_VALUE => $this->getDeclaredValue()
					,'russianpostcalc' => 1
					,Df_RussianPost_Model_RussianPostCalc_Request
						::POST_PARAM__DESTINATION__POSTAL_CODE => $this->getDestinationPostalCode()
					,Df_RussianPost_Model_RussianPostCalc_Request
						::POST_PARAM__WEIGHT => $this->getWeight()
				))
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSourcePostalCode() {
		/** @var string $result */
		$result = $this->_getData(self::P__SOURCE__POSTAL_CODE);
		df_h()->shipping()->assertPostalCodeSource($result);
		return $result;
	}

	/** @return float */
	private function getWeight() {
		return $this->cfg(self::P__WEIGHT);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DECLARED_VALUE, self::V_FLOAT)
			/**
			 * Не используем валидаторы для почтовых индексов,
			 * потому что приход сюда почтового индекса в неверном формате
			 * является ошибкой покупателя, а не программиста,
			 * и покупателю нужно показать понятное ему сообщение
			 * вместо сообщения валидатора.
			 */
			->_prop(self::P__WEIGHT, self::V_FLOAT)
		;
	}
	const _CLASS = __CLASS__;
	const P__DECLARED_VALUE = 'declared_value';
	const P__DESTINATION__POSTAL_CODE = 'destination__postal_code';
	const P__SOURCE__POSTAL_CODE = 'source__postal_code';
	const P__WEIGHT = 'weight';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_RussianPost_Model_RussianPostCalc_Api
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}