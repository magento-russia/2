<?php
// 2016-11-01
namespace Df\Core\T\lib;
class DbColumn extends \PHPUnit\Framework\TestCase {
	/**
	 * @test
	 * 2016-11-01
	 */
	public function df_db_column_add() {
		/** @var $name */
		$name = df_uid(4, 'test_');
		/** @var string $table */
		$table = 'customer/customer_group';
		df_db_column_add($table, $name);
		$this->assertTrue(df_db_column_exists($table, $name));
	}

	/**
	 * @test
	 * 2016-11-01
	 */
	public function df_db_column_exists() {
		/** @var string $table */
		$table = 'customer/customer_group';
		$this->assertTrue(df_db_column_exists($table, 'customer_group_id'));
		$this->assertFalse(df_db_column_exists($table, 'non_existent_column'));
	}

	/**
	 * @test
	 * 2016-11-01
	 */
	public function df_db_column_exists2() {
		$this->expectException(\Exception::class);
		df_db_column_exists('non_existent_table', 'customer_group_id');
	}
}