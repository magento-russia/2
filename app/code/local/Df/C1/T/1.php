<?php
namespace Df\C1\T;
class Main extends \PHPUnit\Framework\TestCase {
	/** @test */
	public function t1() {
		df_db_column_exists('customer/customer_group', 'df_1c_id');
		//\Mage_Core_Model_Resource_Setup::applyAllUpdates();
	}
}


