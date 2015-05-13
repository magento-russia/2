<?php
class Df_Reward_Model_Resource_Reward_Rate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Reward_Model_Reward_Rate::mf(), Df_Reward_Model_Resource_Reward_Rate::mf());
	}
	const _CLASS = __CLASS__;
	/** @return Df_Reward_Model_Resource_Reward_Rate_Collection */
	public static function i() {return new self;}
}