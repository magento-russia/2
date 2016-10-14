<?php
class Df_Reward_Setup_2_20_6 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$t_HISTORY = rm_table(Df_Reward_Model_Resource_Reward_History::TABLE);
		// здесь обязательно использовать именно двойные кавычки, а не одинарные
		$this->getSetup()
			->run("ALTER TABLE `{$t_HISTORY}` MODIFY `expired_at_static` datetime NULL DEFAULT NULL")
			->run("ALTER TABLE `{$t_HISTORY}` MODIFY `expired_at_dynamic` datetime NULL DEFAULT NULL")
			->run(
				"UPDATE `{$t_HISTORY}`
					SET `expired_at_static` = NULL
					WHERE `expired_at_static` < NOW() - INTERVAL 1 YEAR"
			)
			->run(
				"UPDATE `{$t_HISTORY}`
					SET `expired_at_dynamic` = NULL
					WHERE `expired_at_dynamic` < NOW() - INTERVAL 1 YEAR"
			)
		;
	}
}