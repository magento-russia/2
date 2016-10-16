<?php
class Df_Reward_Model_Resource_Reward_Rate extends Df_Core_Model_Resource {
	/**
	 * Fetch rate customer group and website
	 * @param Df_Reward_Model_Reward_Rate $rate
	 * @param integer $customerGroupId
	 * @param integer $websiteId
	 * @param $direction
	 * @return Df_Reward_Model_Resource_Reward_Rate
	 */
	public function fetch(Df_Reward_Model_Reward_Rate $rate, $customerGroupId, $websiteId, $direction)
	{
		$select = $this->_getReadAdapter()->select()
			->from($this->getMainTable())
			->where('website_id IN (?, 0)', (int)$websiteId)
			->where('customer_group_id IN (?, 0)', $customerGroupId)
			->where('direction = ?', $direction)
			->order('website_id DESC')
			->order('customer_group_id DESC')
			->limit(1)
		;
		$row = $this->_getReadAdapter()->fetchRow($select);
		if ($row) {
			$rate->addData($row);
		}
		$this->_afterLoad($rate);
		return $this;
	}

	/**
	 * Retrieve rate data bu given params or empty array if rate with such params does not exists
	 *
	 * @param integer $websiteId
	 * @param integer $customerGroupId
	 * @param integer $direction
	 * @return array
	 */
	public function getRateData($websiteId, $customerGroupId, $direction)
	{
		$result = true;
		$select = $this->_getWriteAdapter()->select()
			->from($this->getMainTable())
			->where('website_id = ?', $websiteId)
			->where('customer_group_id = ?', $customerGroupId)
			->where('direction = ?', $direction)
		;
		$data = $this->_getWriteAdapter()->fetchRow($select);
		if ($data) {
			return $data;
		}
		return array();
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Reward_Model_Reward_Rate::P__ID);}
	/**
	 * @used-by Df_Reward_Setup_1_0_0::_process()
	 * @used-by Df_Reward_Setup_1_0_1::_process()
	 */
	const TABLE = 'df_reward/reward_rate';
	/** @return Df_Reward_Model_Resource_Reward_Rate */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}