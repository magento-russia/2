<?php
class Df_1C_Setup_3_0_0 extends Df_1C_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {$this->add1CIdColumnToTable('customer/customer_group');}
}