<?php
namespace Df\C1\Cml2\Action;
class Init extends \Df\C1\Cml2\Action {
	/**
	 * @todo надо добавить поддержку формата ZIP
	 * @todo надо передавать реальное значение «file_limit»
	 * (и, соответственно, обрабатывать ситуацию, когда 1С передаёт данные порциями).
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		$this->setResponseLines(array('zip' => 'no', 'file_limit' => -1, ''));
	}
}