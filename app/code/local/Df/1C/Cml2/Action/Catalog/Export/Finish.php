<?php
class Df_1C_Cml2_Action_Catalog_Export_Finish extends Df_1C_Cml2_Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {$this->setResponseLines(array('finished' => 'yes'));}
}


