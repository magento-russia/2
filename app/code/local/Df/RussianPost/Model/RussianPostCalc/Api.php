<?php
class Df_RussianPost_Model_RussianPostCalc_Api extends Df_Core_Model {
	/** @return string[] */
	public function getRatesAsText() {return $this->getRequest()->getRatesAsText();}

	/** @return float */
	private function getDeclaredValue() {return $this->cfg(self::P__DECLARED_VALUE);}

	/** @return string */
	private function getDestinationPostalCode() {return $this->cfg(self::P__DESTINATION__POSTAL_CODE);}

	/** @return Df_RussianPost_Model_RussianPostCalc_Request */
	private function getRequest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_RussianPost_Model_RussianPostCalc_Request::i(
				$this->getSourcePostalCode()
				, $this->getDestinationPostalCode()
				, $this->getWeight()
				, $this->getDeclaredValue()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSourcePostalCode() {return $this->cfg(self::P__SOURCE__POSTAL_CODE);}

	/** @return float */
	private function getWeight() {return $this->cfg(self::P__WEIGHT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DECLARED_VALUE, RM_V_FLOAT)
			/**
			 * Не используем валидаторы для почтовых индексов,
			 * потому что приход сюда почтового индекса в неверном формате
			 * является ошибкой покупателя, а не программиста,
			 * и покупателю нужно показать понятное ему сообщение
			 * вместо сообщения валидатора.
			 */
			->_prop(self::P__WEIGHT, RM_V_FLOAT)
		;
	}
	const _C = __CLASS__;
	const P__DECLARED_VALUE = 'declared_value';
	const P__DESTINATION__POSTAL_CODE = 'destination__postal_code';
	const P__SOURCE__POSTAL_CODE = 'source__postal_code';
	const P__WEIGHT = 'weight';
	/**
	 * @static
	 * @param string $sourcePostalCode
	 * @param string $destinationPostalCode
	 * @param float $weight
	 * @param float $declaredValue
	 * @return Df_RussianPost_Model_RussianPostCalc_Api
	 */
	public static function i($sourcePostalCode, $destinationPostalCode, $weight, $declaredValue) {
		return new self(array(
			self::P__SOURCE__POSTAL_CODE => $sourcePostalCode
			, self::P__DESTINATION__POSTAL_CODE => $destinationPostalCode
			, self::P__WEIGHT => $weight
			, self::P__DECLARED_VALUE => $declaredValue
		));
	}
}