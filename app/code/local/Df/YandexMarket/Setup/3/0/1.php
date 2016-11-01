<?php
// 2016-10-18
class Df_YandexMarket_Setup_3_0_1 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$this->conn()->update(
			df_table('eav/attribute')
			,['backend_model' => \Df\YandexMarket\Config\Backend\Category::class]
			,['? = backend_model' => 'Df_YandexMarket_Model_Config_Backend_Category']
		);
		df_eav_reset();
	}
}