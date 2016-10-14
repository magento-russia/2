<?php
class Df_Reward_Setup_1_0_1 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$tableReward = rm_table(Df_Reward_Model_Resource_Reward::TABLE);
		$tableRewardHistory = rm_table(Df_Reward_Model_Resource_Reward_History::TABLE);
		$tableRewardRate = rm_table(Df_Reward_Model_Resource_Reward_Rate::TABLE);
		$tableRewardWrong = rm_table($tableReward);
		$tableRewardHistoryWrong = rm_table($tableRewardHistory);
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
					/** http://magento-forum.ru/topic/4039/ */
				&&
					$this->getSetup()->tableExists($wrongName)
				&&
					!$this->getSetup()->tableExists($correctName)
			) {
				$this->run("RENAME TABLE `{$wrongName}` TO `{$correctName}`;");
			}
		}
	}
}