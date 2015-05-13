<?php
class Df_1C_Model_Cml2_Action_GenericImport_Upload extends Df_1C_Model_Cml2_Action_GenericImport {
	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {
		rm_file_put_contents($this->getFileCurrent()->getPathFull(), file_get_contents('php://input'));
		$this->setResponseBodyAsArrayOfStrings(array('success', ''));
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_GenericImport_Upload
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}