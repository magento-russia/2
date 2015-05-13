<?php
class Df_Reward_Model_Setup_2_20_6 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void	 */
	public function process() {
		/** @var string $t_HISTORY */
		$t_HISTORY = rm_table('df_reward/reward_history');
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
		rm_cache_clean();
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Reward_Model_Setup_2_20_6
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}