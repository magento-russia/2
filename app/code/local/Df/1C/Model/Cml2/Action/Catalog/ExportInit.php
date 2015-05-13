<?php
class Df_1C_Model_Cml2_Action_Catalog_ExportInit extends Df_1C_Model_Cml2_Action_Init {
	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {
		parent::processInternal();
		Df_1C_Model_Cml2_Session_ByIp::s()->setFlag_catalogHasJustBeenExported(false);
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Init
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}


