<?php
class Df_1C_Model_Cml2_Action_Reference_Import extends Df_1C_Model_Cml2_Action_Reference {
	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {
		$this->setResponseBodyAsArrayOfStrings(array('success', ''));
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Reference_Import
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}