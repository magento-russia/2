<?php
class Df_1C_Cml2_Action_GenericImport_Upload extends Df_1C_Cml2_Action_GenericImport {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		rm_file_put_contents($this->getFileCurrent()->getPathFull(), file_get_contents('php://input'));
		$this->setResponseSuccess();
	}
}