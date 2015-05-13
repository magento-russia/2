<?php
class Df_WalletOne_Model_Form_Processor_AddPaymentMethods extends Df_Varien_Data_Form_Processor {
	/** @return Df_WalletOne_Model_Form_Processor_AddPaymentMethods */
	public function process() {
		foreach ($this->getFieldValues() as $subFieldName => $subFieldValue) {
			/** @var string|int $subFieldName */
			/** @var string|array $subFieldValue */

			if (!is_int($subFieldName)) {
				df_assert_string($subFieldName);
			}

			if (!is_array($subFieldValue)) {
				df_assert_string($subFieldValue);
			}

			$this->getForm()
				->addHiddenField(
					implode(
						'_'
						,array(
							$this->getFieldName()
							,df_string($subFieldName)
						)
					)
					,$this->getFieldName()
					,$subFieldValue
				)
			;
		}
		return $this;
	}

	/** @return string */
	private function getFieldName() {
		/** @var string $result */
		$result = $this->cfg(self::P__FIELD_NAME);
		df_result_string($result);
		return $result;
	}

	/** @return array */
	private function getFieldValues() {
		/** @var array $result */
		$result = $this->cfg(self::P__FIELD_VALUES);
		df_result_array($result);
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__FIELD_NAME, self::V_STRING_NE)
			->_prop(self::P__FIELD_VALUES, self::V_ARRAY)
		;
	}
	const _CLASS = __CLASS__;
	const P__FIELD_NAME = 'field_name';
	const P__FIELD_VALUES = 'field_values';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_WalletOne_Model_Form_Processor_AddPaymentMethods
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

}