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
		$f_1C_ID = Df_C1_Const::ENTITY_EXTERNAL_ID;
		$t_TABLE = df_table($table);
		$this->dropColumn($t_TABLE, Df_C1_Const::ENTITY_EXTERNAL_ID_OLD);
		// Обратите внимание, что удаление колонки перед её созданием
		// позволяет нам беспроблемно проводить одну и ту же установку много раз подряд
		// (например, с целью тестирования или когда в процессе разработки
		// перед выпуском версии требуется доработать
		// ранее разработанный и запускавшийся доработать установщик).
		$this->dropColumn($t_TABLE, $f_1C_ID);
		$this->run("alter table {$t_TABLE} add column `{$f_1C_ID}` varchar(255) default null;");
	}
}

