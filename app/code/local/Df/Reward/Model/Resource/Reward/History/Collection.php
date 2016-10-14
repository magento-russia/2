<?php
class Df_Reward_Model_Resource_Reward_History_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * Join reward table and retrieve total balance total with customer_id
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	protected function _joinReward()
	{
		if ($this->getFlag('reward_joined')) {
			return $this;
		}
		$this->getSelect()->joinInner(
			array('reward_table' => df_table(Df_Reward_Model_Resource_Reward::TABLE))
			,'reward_table.reward_id = main_table.reward_id'
			, array('customer_id', 'points_balance_total' => 'points_balance')
		);
		$this->setFlag('reward_joined', true);
		return $this;
	}

	/**
	 * Getter for $_expiryConfig
	 *
	 * @param int $websiteId Specified Website Id
	 * @return array|Varien_Object
	 */
	protected function _getExpiryConfig($websiteId = null)
	{
		if ($websiteId !== null && isset($this->_expiryConfig[$websiteId])) {
			return $this->_expiryConfig[$websiteId];
		}
		return $this->_expiryConfig;
	}

	/**
	 * Setter for $_expiryConfig
	 *
	 * @param array $config
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	public function setExpiryConfig($config)
	{
		if (!is_array($config)) {
			return $this;
		}
		$this->_expiryConfig = $config;
		return $this;
	}

	/**
	 * Join reward table to filter history by customer id
	 *
	 * @param string $customerId
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	public function addCustomerFilter($customerId)
	{
		if ($customerId) {
			$this->_joinReward();
			$this->getSelect()->where('reward_table.customer_id = ?', $customerId);
		}
		return $this;
	}

	/**
	 * Skip Expired duplicates records (with action = -1)
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	public function skipExpiredDuplicates()
	{
		$this->getSelect()->where('main_table.is_duplicate_of IS null');
		return $this;
	}

	/**
	 * @param int|int[] $websiteId
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	public function addWebsiteFilter($websiteId) {
		$this->getSelect()->where('main_table.website_id IN (?)', df_array($websiteId));
		return $this;
	}

	/**
	 * Join additional customer information, such as email, name etc.
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	public function addCustomerInfo()
	{
		if ($this->getFlag('customer_added')) {
			return $this;
		}
		$this->_joinReward();
		/** @var Df_Customer_Model_Customer $customer */
		$customer = Df_Customer_Model_Customer::i();
		$firstname = $customer->getAttribute('firstname');
		$lastname = $customer->getAttribute('lastname');
		/* @var Zend_Db_Adapter_Abstract $connection */
		$connection = $this->getConnection();
		$this->getSelect()
			->joinInner(
				array('ce' => $customer->getAttribute('email')->getBackend()->getTable())
				, 'ce.entity_id=reward_table.customer_id'
				, array('customer_email' => 'email')
			 )
			->joinLeft(
				array('clt' => $lastname->getBackend()->getTable())
				, df_db_quote_into(
					'clt.entity_id=reward_table.customer_id AND clt.attribute_id = ?'
					, $lastname->getAttributeId())
				, array('customer_lastname' => 'value')
			 )
			 ->joinLeft(
				 array('cft' => $firstname->getBackend()->getTable())
				 , df_db_quote_into(
					 'cft.entity_id=reward_table.customer_id AND cft.attribute_id = ?'
					 , $firstname->getAttributeId()
				 )
				 , array('customer_firstname' => 'value')
			 );
		$this->setFlag('customer_added', true);
		return $this;
	}

	/**
	 * Add correction to expiration date based on expiry calculation
	 * CASE ... WHEN ... THEN is used only in admin area to show expiration date for all stores
	 *
	 * @param int $websiteId
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	public function addExpirationDate($websiteId = null)
	{
		$expiryConfig = $this->_getExpiryConfig($websiteId);
		if (!$expiryConfig) {
			return $this;
		}

		if ($websiteId !== null) {
			$field =
				'static' === $expiryConfig->getExpiryCalculation()
				? 'expired_at_static'
				: 'expired_at_dynamic'
			;
			$this->getSelect()->columns(array('expiration_date' => $field));
		} else {
			$sql = " CASE main_table.website_id ";
			$cases = array();
			foreach ($expiryConfig as $wId => $config) {
				$field =
					'static' === $config->getExpiryCalculation()
					? 'expired_at_static'
					: 'expired_at_dynamic'
				;
				$cases[]= " WHEN '{$wId}' THEN `{$field}` ";
			}
			if ($cases) {
				$sql .= implode(' ', $cases) . ' END ';
				$this->getSelect()->columns( array('expiration_date' => new Zend_Db_Expr($sql)));
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Reward_Model_Resource_Reward_History
	 */
	public function getResource() {return Df_Reward_Model_Resource_Reward_History::s();}

	/**
	 * Return total amounts of points that will be expired soon (pre-configured days value) for specified website
	 * Result is grouped by customer
	 *
	 * @param int $websiteId Specified Website
	 * @param bool $subscribedOnly Whether to load expired soon points only for subscribed customers
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	public function loadExpiredSoonPoints($websiteId, $subscribedOnly = true)
	{
		$expiryConfig = $this->_getExpiryConfig($websiteId);
		if (!$expiryConfig) {
			return $this;
		}
		$inDays = (int)$expiryConfig->getExpiryDayBefore();
		// Empty Value disables notification
		if (!$inDays) {
			return $this;
		}

		// join info about current balance and filter records by website
		$this->_joinReward();
		$this->addWebsiteFilter($websiteId);
		$field =
			'static' === $expiryConfig->getExpiryCalculation()
			? 'expired_at_static'
			: 'expired_at_dynamic'
		;
		$expireAtLimit = $this->getResource()->formatDate(rm_today_add($inDays));
		$this->getSelect()
			->columns(array('total_expired' => new Zend_Db_Expr('SUM(`points_delta`-`points_used`)')))
			->where('`points_delta`-`points_used`>0')
			->where('`is_expired`=0')
			->where("`{$field}` IS NOT NULL") // expire_at - BEFORE_DAYS < NOW()
			->where("`{$field}` < ?", $expireAtLimit) // eq. expire_at - BEFORE_DAYS < NOW()
			->group('reward_table.customer_id')
			->order('reward_table.customer_id');
		if ($subscribedOnly) {
			$this->getSelect()->where('reward_table.reward_warning_notification=1');
		}
		return $this;
	}

	/**
	 * Order by primary key desc
	 * @return Df_Reward_Model_Resource_Reward_History_Collection
	 */
	public function setDefaultOrder() {
		$this->getSelect()->reset(Zend_Db_Select::ORDER);
		return $this->setOrder('history_id', 'DESC');
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Reward_Model_Reward_History::_C;}
	/** @var array */
	protected $_expiryConfig = array();
	const _C = __CLASS__;
}