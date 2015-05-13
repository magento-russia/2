<?php
class Df_Reward_Model_Setup_1_0_1 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var string $tableReward */
		$tableReward = rm_table('df_reward/reward');
		/** @var string $tableRewardHistory */
		$tableRewardHistory = rm_table('df_reward/reward_history');
		/** @var string $tableRewardRate */
		$tableRewardRate = rm_table('df_reward/reward_rate');
		/** @var string $tableRewardWrong */
		$tableRewardWrong = rm_table($tableReward);
		/** @var string $tableRewardHistoryWrong */
		$tableRewardHistoryWrong = rm_table($tableRewardHistory);
		/** @var string $tableRewardRateWrong */
		$tableRewardRateWrong = rm_table($tableRewardRate);
		/** @var array(string => string) $renames */
		$renames = array(
			$tableRewardWrong => $tableReward
			,$tableRewardHistoryWrong => $tableRewardHistory
			,$tableRewardRateWrong => $tableRewardRate
		);
		foreach ($renames as $wrongName => $correctName) {
			/** @var string $wrongName */
			/** @var string $correctName */
			if (
					($wrongName !== $correctName)
					/**
					 * @link http://magento-forum.ru/topic/4039/
					 */
				&&
					$this->getSetup()->tableExists($wrongName)
				&&
					!$this->getSetup()->tableExists($correctName)
			) {
				$this->getSetup()->run("RENAME TABLE `{$wrongName}` TO `{$correctName}`;");
			}
		}
		rm_cache_clean();
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Reward_Model_Setup_1_0_1
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}