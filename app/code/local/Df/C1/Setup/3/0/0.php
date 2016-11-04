<?php
class Df_C1_Setup_3_0_0 extends \Df\C1\Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$this->add1CIdColumnToTable('eav/attribute_option');
		$this->add1CIdColumnToTable('catalog/eav_attribute');
		$this->add1CIdColumnToTable('customer/customer_group');
		df_conn()->update(df_table('eav/attribute'),
			['attribute_code' => \Df\C1\C::ENTITY_EXTERNAL_ID]
			,['? = attribute_code ' => \Df\C1\C::ENTITY_EXTERNAL_ID_OLD]
		);
	}
}