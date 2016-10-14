<?php
class Df_Reward_Model_Resource_Reward_Rate_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * @override
	 * @return Df_Reward_Model_Resource_Reward_Rate
	 */
	public function getResource() {return Df_Reward_Model_Resource_Reward_Rate::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Reward_Model_Reward_Rate::_C;}
	const _C = __CLASS__;
}