<?php
/**
 * @method Df_Reward_Model_Resource_Reward_History getResource()
 */
class Df_Reward_Model_Reward_History extends Df_Core_Model {
	/**
	 * Processing object before save data.
	 * Prepare history data
	 * @return Df_Reward_Model_Reward_History
	 */
	protected function _beforeSave()
	{
		if ($this->getWebsiteId()) {
			$this->setBaseCurrencyCode(
				Mage::app()->getWebsite($this->getWebsiteId())->getBaseCurrencyCode()
			);
		}
		if ($this->getPointsDelta() < 0) {
			$this->_spendAvailablePoints($this->getPointsDelta());
		}

		$now = $this->getResource()->formatDate(time());
		$this
			->addData(
				array(
					'created_at' => $now
					,'expired_at_static' => null
					,'expired_at_dynamic' => null
					,'notification_sent' => 0
			)
			)
		;
		$lifetime =
			intval(
				df_h()->reward()->getGeneralConfig(
					'expiration_days'
					,$this->getWebsiteId()
				)
			)
		;
		if ($lifetime > 0) {
			$expireAt = new Zend_Date($now);
			$expireAt->addDay($lifetime);
			$expired = $this->getResource()->formatDate($expireAt);
			$this
				->addData(
					array(
						'expired_at_static' => $expired
						,'expired_at_dynamic' => $expired
					)
				)
			;
		}
		return parent::_beforeSave();
	}

	/**
	 * Setter
	 *
	 * @param Df_Reward_Model_Reward $reward
	 * @return Df_Reward_Model_Reward_History
	 */
	public function setReward($reward)
	{
		$this->_reward = $reward;
		return $this;
	}

	/**
	 * Getter
	 * @return Df_Reward_Model_Reward
	 */
	public function getReward()
	{
		return $this->_reward;
	}

	/**
	 * Create history data from reward object
	 * @return Df_Reward_Model_Reward_History
	 */
	public function prepareFromReward()
	{
		$store = $this->getReward()->getStore();
		if ($store === null) {
			$store = Mage::app()->getStore();
		}
		$this->setRewardId($this->getReward()->getId())
			->setWebsiteId($this->getReward()->getWebsiteId())
			->setStoreId($store->getId())
			->setPointsBalance($this->getReward()->getPointsBalance())
			->setPointsDelta($this->getReward()->getPointsDelta())
			->setCurrencyAmount($this->getReward()->getCurrencyAmount())
			->setCurrencyDelta($this->getReward()->getCurrencyDelta())
			->setAction($this->getReward()->getAction())
			->setComment($this->getReward()->getComment());
		$this->addAdditionalData(array(
			'rate' => array(
				'points' => $this->getReward()->getRate()->getPoints(),'currency_amount' => $this->getReward()->getRate()->getCurrencyAmount(),'direction' => $this->getReward()->getRate()->getDirection(),'currency_code' => Mage::app()->getWebsite($this->getReward()->getWebsiteId())->getBaseCurrencyCode()
			)
		));
		if ($this->getReward()->getIsCappedReward()) {
			$this->addAdditionalData(array(
				'is_capped_reward' => true,'cropped_points'	=> $this->getReward()->getCroppedPoints()
			));
		}
		return $this;
	}

	/**
	 * Getter.
	 * Unserialize if need
	 * @return array
	 */
	public function getAdditionalData()
	{
		if (is_string($this->_getData('additional_data'))) {
			$this->setData('additional_data', unserialize($this->_getData('additional_data')));
		}
		return $this->_getData('additional_data');
	}

	/**
	 * Getter.
	 * Return value of unserialized additional data item by given item key
	 *
	 * @param string $key
	 * @return mixed | null
	 */
	public function getAdditionalDataByKey($key)
	{
		$data = $this->getAdditionalData();
		if (is_array($data) && !empty($data) && isset($data[$key])) {
			return $data[$key];
		}
		return null;
	}

	/**
	 * Add additional values to additional_data
	 *
	 * @param array $data
	 * @return Df_Reward_Model_Reward_History
	 */
	public function addAdditionalData($data)
	{
		if (is_array($data)) {
			$additional = $this->getDataSetDefault('additional_data', array());
			if (!is_array($additional)) {
				if (is_string($additional)) {
					$additional = @unserialize($additional);
				}
				if (!is_array($additional)) {
					$additional = array();
				}
			}
			foreach ($data as $k => $v) {
				$additional[$k] = $v;
			}
			$this->setData('additional_data', $additional);
		}
		return $this;
	}

	/**
	 * Retrieve translated and prepared message
	 * @return string
	 */
	public function getMessage()
	{
		if (!$this->hasData('message')) {
			$action = Df_Reward_Model_Reward::s()->getActionInstance($this->getAction());
			$message = '';
			if ($action !== null) {
				$message = $action->getHistoryMessage($this->getAdditionalData());
			}
			$this->setData('message', $message);
		}
		return $this->_getData('message');
	}

	/**
	 * Rate text getter
	 * @return string|null
	 */
	public function getRateText()
	{
		$rate = $this->getAdditionalDataByKey('rate');
		if (isset($rate['points']) && isset($rate['currency_amount']) && isset($rate['direction'])) {
			return Df_Reward_Model_Reward_Rate::getRateText(
				(int)$rate['direction'], (int)$rate['points'], (float)$rate['currency_amount'],$this->getBaseCurrencyCode()
			);
		}
	}

	/**
	 * Check if history update with given action, customer and entity exist
	 *
	 * @param integer $customerId
	 * @param integer $action
	 * @param integer $websiteId
	 * @param mixed $entity
	 * @return boolean
	 */
	public function isExistHistoryUpdate($customerId, $action, $websiteId, $entity)
	{
		$result = $this->getResource()->isExistHistoryUpdate($customerId, $action, $websiteId, $entity);
		return $result;
	}

	/**
	 * Return total quantity rewards for specified action and customer
	 *
	 * @param int $action
	 * @param int $customerId
	 * @param integer $websiteId
	 * @return int
	 */
	public function getTotalQtyRewards($action, $customerId, $websiteId)
	{
		return $this->getResource()->getTotalQtyRewards($action, $customerId, $websiteId);
	}

	/**
	 * Getter for date when the record is supposed to expire
	 * @return string|null
	 */
	public function getExpiresAt()
	{
		if ($this->getPointsDelta() <= 0) {
			return null;
		}
		return df_h()->reward()->getGeneralConfig('expiry_calculation') == 'static'
			? $this->getExpiredAtStatic() : $this->getExpiredAtDynamic()
		;
	}

	/**
	 * Spend unused points for required amount
	 *
	 * @param int $required Points total that required
	 * @return Df_Reward_Model_Reward_History
	 */
	protected function _spendAvailablePoints($required)
	{
		$this->getResource()->useAvailablePoints($this, $required);
		return $this;
	}
	protected $_reward = null;

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Reward_Model_Resource_Reward_History::mf());
	}
	const _CLASS = __CLASS__;
	const P__ID = 'history_id';
	/** @return Df_Reward_Model_Resource_Reward_History_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Reward_Model_Reward_History
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Reward_Model_Reward_History
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Reward_Model_Resource_Reward_History_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Reward_Model_Reward_History */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}