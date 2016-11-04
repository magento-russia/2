<?php
namespace Df\C1\Cml2\Action\Catalog\Export;
class Df_C1_Cml2_Action_Catalog_Export_Finish extends Df_C1_Cml2_Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {$this->setResponseLines(array('finished' => 'yes'));}
}


