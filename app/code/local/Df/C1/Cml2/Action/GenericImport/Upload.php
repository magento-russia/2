<?php
namespace Df\C1\Cml2\Action\GenericImport;
class Upload extends \Df\C1\Cml2\Action\GenericImport {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		df_file_put_contents($this->getFileCurrent()->getPathFull(), file_get_contents('php://input'));
		$this->setResponseSuccess();
	}
}