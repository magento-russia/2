<?php
namespace Df\C1\Cml2\Action\Catalog\Export;
class Finish extends \Df\C1\Cml2\Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {$this->setResponseLines(array('finished' => 'yes'));}
}


