<?php
class Df_C1_Setup_3_0_0 extends Df_C1_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {$this->add1CIdColumnToTable('customer/customer_group');}
}