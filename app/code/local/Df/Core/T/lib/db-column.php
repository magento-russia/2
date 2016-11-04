<?php
// 2016-11-01
namespace Df\Core\T\lib;
class DbColumn extends \PHPUnit\Framework\TestCase {
	/**
	 * @test
	 * 2016-11-04
	 */
	public function df_db_column_add_drop() {
		/** @var $name */
		$name = df_uid(4, 'test_');
		df_db_column_add(self::$TABLE, $name, "int(11) unsigned NOT null DEFAULT '0'");
		$this->assertTrue(df_db_column_exists(self::$TABLE, $name));
		df_db_column_drop(self::$TABLE, $name);
		$this->assertFalse(df_db_column_exists(self::$TABLE, $name));
	}

	/**
	 * @test
	 * 2016-11-01
	 */
	public function df_db_column_exists() {
		$this->assertTrue(df_db_column_exists(self::$TABLE, 'customer_group_id'));
		$this->assertFalse(df_db_column_exists(self::$TABLE, 'non_existent_column'));
	}

	/**
	 * @test
	 * 2016-11-01
	 */
	public function df_db_column_exists2() {
		$this->expectException(\Exception::class);
		df_db_column_exists('non_existent_table', 'customer_group_id');
	}

	/**
	 * @test
	 * 2016-11-04
	 */
	public function df_db_column_rename() {
		/** @var string $from */
		$from = df_uid(4, 'test_');
		/** @var string $to */
		$to = df_uid(4, 'test_');
		df_db_column_add(self::$TABLE, $from);
		$this->assertTrue(df_db_column_exists(self::$TABLE, $from));
		df_db_column_rename(self::$TABLE, $from, $to);
		$this->assertFalse(df_db_column_exists(self::$TABLE, $from));
		$this->assertTrue(df_db_column_exists(self::$TABLE, $to));
		df_db_column_drop(self::$TABLE, $to);
		$this->assertFalse(df_db_column_exists(self::$TABLE, $to));
	}

	/** @var string */
	private static $TABLE = 'customer/customer_group';
}