<?php
class Df_1C_Model_Cml2_Action_Init extends Df_1C_Model_Cml2_Action {
	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {
		/** @todo надо добавить поддержку формата ZIP */
		$this->setResponseBodyAsArrayOfStrings(array(
			$this->implodeResponseParam('zip', 'no')
			,$this->implodeResponseParam('file_limit', -1)
			,''
		));
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Init
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}