<?php
class Df_C1_Cml2_Action_Init extends Df_C1_Cml2_Action {
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