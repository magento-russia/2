<?php
abstract class Df_C1_Setup extends Df_Core_Setup {
	/**
	 * Не забывайте после вызова этого метода вызывать @see df_cache_clean().
	 * @used-by Df_C1_Setup_1_0_2::process()
	 * @used-by Df_C1_Setup_2_44_0::process()
	 * @param string $table
	 * @return void
	 */
	protected function add1CIdColumnToTable($table) {
		/** @var string $old */
		$old = Df_C1_Const::ENTITY_EXTERNAL_ID_OLD;
		/** @var string $new */
		$new = Df_C1_Const::ENTITY_EXTERNAL_ID;
		df_db_column_exists($table, $old)
			? df_db_column_rename($table, $old, $new)
			: df_db_column_add($table, $new, 'varchar(255) default null')
		;
	}
}

