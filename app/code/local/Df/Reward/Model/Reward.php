<?php
/**
 * @method int|null getCustomerId()
 * @method float|null getPointsBalance()
 * @method Df_Reward_Model_Resource_Reward getResource()
 *
 * @method Df_Reward_Model_Reward setAction(int $value)
 * @method Df_Reward_Model_Reward setActionEntity(Varien_Object $value)
 * @method Df_Reward_Model_Reward setCustomerId(int $value)
 * @method Df_Reward_Model_Reward setPointsDelta(float $value)
 * @method Df_Reward_Model_Reward setStore(int $value)
 * @method Df_Reward_Model_Reward setWebsiteId(int $value)
 */
class Df_Reward_Model_Reward extends Df_Core_Model {
	/** @return boolean */
	public function canUpdateRewardPoints() {
		/** @var Df_Reward_Model_Action_Abstract $action */
		$action = $this->getActionInstance($this->getAction());
		df_assert($action instanceof Df_Reward_Model_Action_Abstract);
		$action->setEntity($this->getActionEntity());
		// must be assigned as context object in observer etc.
		return $action->canAddRewardPoints();
	}

	/**
	 * Subscribe / Unsubscribe customer from Balance Update Notifications
	 * @param bool $flag Whether to set/unset Notifications
	 * @return Df_Reward_Model_Reward
	 */
	public function changeBalanceUpdateNotification($flag) {
		$this->getResource()->updateRewardRow($this, array('reward_update_notification' => $flag ? 1 : 0));
		return $this;
	}

	/**
	 * Subscribe / Unsubscribe customer from Balance Warning Notifications
	 * @param bool $flag Whether to set/unset Notifications
	 * @return Df_Reward_Model_Reward
	 */
	public function changeBalanceWarningNotification($flag) {
		$this->getResource()->updateRewardRow($this, array('reward_warning_notification' => $flag ? 1 : 0));
		return $this;
	}

	/**
	 * Delete orphan (points of deleted website) points by given customer
	 * @param Df_Customer_Model_Customer|int|null $customer [optional]
	 * @return Df_Reward_Model_Reward
	 */
	public function deleteOrphanPointsByCustomer($customer = null) {
		if ($customer === null) {
			$customer = $this->getCustomerId()?$this->getCustomerId():$this->getCustomer();
		}
		if (is_object($customer) && $customer instanceof Mage_Customer_Model_Customer) {
			$customer = $customer->getId();
		}
		if ($customer) {
			$this->getResource()->deleteOrphanPointsByCustomer($customer);
		}
		return $this;
	}

	/**
	 * Estimate available monetary reward for specified action
	 * May take points value or automatically determine from action
	 * @param Df_Reward_Model_Action_Abstract $action
	 * @return float|null
	 */
	public function estimateRewardAmount(Df_Reward_Model_Action_Abstract $action) {
		if (!$this->getCustomerId()) {
			return null;
		}
		$websiteId = $this->getWebsiteId();
		$rate = $this->getRateToCurrency();
		if (!$rate->getId()) {
			return null;
		}
		return $rate->calculateToCurrency($this->estimateRewardPoints($action), false);
	}

	/**
	 * Estimate available points reward for specified action
	 * @param Df_Reward_Model_Action_Abstract $action
	 * @return int|null
	 */
	public function estimateRewardPoints(Df_Reward_Model_Action_Abstract $action) {
		$websiteId = $this->getWebsiteId();
		$uncappedPts = (int)$action->getPoints($websiteId);
		$max = (int)df_h()->reward()->getGeneralConfig('max_points_balance', $websiteId);
		if ($max > 0) {
			return min(max($max - (int)$this->getPointsBalance(), 0), $uncappedPts);
		}
		return $uncappedPts;
	}

	/**
	 * @param string|int $action
	 * @param bool $isFactoryName [optional]
	 * @return Df_Reward_Model_Action_Abstract|null
	 */
	public function getActionInstance($action, $isFactoryName = false) {
		if ($isFactoryName) {
			$action = array_search($action, self::$_actionModelClasses);
			if (!$action) {
				return null;
			}
		}
		$instance = Mage::registry('_reward_actions' . $action);
		if ($instance) {
			return $instance;
		}
		if (isset(self::$_actionModelClasses[$action])) {
			$instance = df_model(self::$_actionModelClasses[$action]);
			$instance->setAction($action)
				->setReward($this)
				->setHistory($this->getHistory());
			Mage::register('_reward_actions' . $action, $instance);
			return $instance;
		}
		return null;
	}

	/**
	 * Recalculate currency amount if need.
	 * @return float
	 */
	public function getCurrencyAmount() {
		if ($this->_getData('currency_amount') === null) {
			$this->_prepareCurrencyAmount();
		}
		return $this->_getData('currency_amount');
	}

	/** @return Mage_Customer_Model_Customer */
	public function getCustomer() {
		if (!$this->_getData('customer') && $this->getCustomerId()) {
			/** @var Df_Customer_Model_Customer $customer */
			$customer = Df_Customer_Model_Customer::ld($this->getCustomerId());
			$this->setCustomer($customer);
		}
		return $this->_getData('customer');
	}

	/** @return integer */
	public function getCustomerGroupId() {
		if (!$this->_getData('customer_group_id') && $this->getCustomer()) {
			$this->setData('customer_group_id', $this->getCustomer()->getGroupId());
		}
		return $this->_getData('customer_group_id');
	}

	/** @return string */
	public function getFormatedCurrencyAmount() {
		return df_money_fl($this->getCurrencyAmount(), $this->getWebsiteCurrencyCode());
	}

	/** @return Df_Reward_Model_Reward_History */
	public function getHistory() {
		if (!$this->_getData('history')) {
			$this->setData('history', Df_Reward_Model_Reward_History::i());
			$this->getHistory()->setReward($this);
		}
		return $this->_getData('history');
	}

	/** @return integer */
	public function getPointsDelta() {
		if ($this->_getData('points_delta') === null) {
			$this->_preparePointsDelta();
		}
		return $this->_getData('points_delta');
	}

	/**
	 * Return points equivalent of given amount.
	 * Converting by 'to currency' rate and points round up
	 * @param float $amount
	 * @return integer
	 */
	public function getPointsEquivalent($amount) {
		$points = 0;
		if ($amount) {
			$ratePointsCount = $this->getRateToCurrency()->getPoints();
			$rateCurrencyAmount = $this->getRateToCurrency()->getCurrencyAmount();
			if ($rateCurrencyAmount > 0) {
				$delta = $amount / $rateCurrencyAmount;
				if ($delta > 0) {
					$points = $ratePointsCount * ceil($delta);
				}
			}
		}
		return $points;
	}

	/**
	 * Return rate depend on action
	 * @return Df_Reward_Model_Reward_Rate
	 */
	public function getRate() {
		return $this->_getRateByDirection($this->getRateDirectionByAction());
	}

	/** @return integer */
	public function getRateDirectionByAction() {
		switch($this->getAction()) {
			case self::REWARD_ACTION_ORDER_EXTRA:
				$direction = Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS;
				break;
			default:
				$direction = Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY;
				break;
		}
		return $direction;
	}

	/**
	 * Return rate to convert points to currency amount
	 * @return Df_Reward_Model_Reward_Rate
	 */
	public function getRateToCurrency() {
		return
			$this->_getRateByDirection(
				Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
			)
		;
	}

	/**
	 * Return rate to convert currency amount to points
	 * @return Df_Reward_Model_Reward_Rate
	 */
	public function getRateToPoints() {
		return
			$this->_getRateByDirection(
				Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
			)
		;
	}

	/**
	 * @override
	 * @return Df_Reward_Model_Resource_Reward_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * Getter for store (for emails etc)
	 * Trying get store from customer if its not assigned
	 * @return Df_Core_Model_StoreM|null
	 */
	public function getStore() {
		$store = null;
		if ($this->hasData('store') || $this->hasData('store_id')) {
			$store = $this->getDataSetDefault('store', $this->_getData('store_id'));
		} else if ($this->getCustomer() && $this->getCustomer()->getStoreId()) {
			$store = $this->getCustomer()->getStore();
			$this->setData('store', $store);
		}
		// намеренно возвращаем null (так в оригинале)
		return is_null($store) ? null : (is_object($store) ? $store : df_store($store));
	}

	/** @return string */
	public function getWebsiteCurrencyCode() {
		if (!$this->_getData('website_currency_code')) {
			$this->setData('website_currency_code', df_website($this->getWebsiteId())
				->getBaseCurrencyCode());
		}
		return $this->_getData('website_currency_code');
	}

	/** @return int */
	public function getWebsiteId() {
		if (!$this->_getData('website_id') && ($store = $this->getStore())) {
			$this->setData('website_id', $store->getWebsiteId());
		}
		return $this->_getData('website_id');
	}

	/**
	 * Check is enough points (currency amount) to cover given amount
	 * @param float $amount
	 * @return boolean
	 */
	public function isEnoughPointsToCoverAmount($amount) {
		$result = false;
		if ($this->getId()) {
			if ($this->getCurrencyAmount() >= $amount) {
				$result = true;
			}
		}
		return $result;
	}

	/** @return Df_Reward_Model_Reward */
	public function loadByCustomer() {
		if (
				!$this->_modelLoadedByCustomer
			&&
				$this->getCustomerId()
			&&
				$this->getWebsiteId()
		) {
			$this->getResource()
				->loadByCustomerId(
					$this
					,$this->getCustomerId()
					,$this->getWebsiteId()
				)
			;
			$this->_modelLoadedByCustomer = true;
		}
		return $this;
	}

	/**
	 * Prepare orphan points by given website id and website base currency code
	 * after website was deleted
	 * @param integer $websiteId
	 * @param string $baseCurrencyCode
	 * @return Df_Reward_Model_Reward
	 */
	public function prepareOrphanPoints($websiteId, $baseCurrencyCode) {
		if ($websiteId) {
			$this->getResource()->prepareOrphanPoints($websiteId, $baseCurrencyCode);
		}
		return $this;
	}

	/**
	 * Send Balance Update Notification to customer if notification is enabled
	 * @return Df_Reward_Model_Reward
	 */
	public function sendBalanceUpdateNotification() {
		if (!$this->getCustomer()->getRewardUpdateNotification()) {
			return $this;
		}
		$delta = (int)$this->getPointsDelta();
		if ($delta == 0) {
			return $this;
		}
		$store = df_store($this->getStore());
		/* @var Df_Core_Model_Email_Template $mail */
		$mail = Df_Core_Model_Email_Template::i();
		$mail->setDesignConfig(array(
			'area' => Df_Core_Const_Design_Area::FRONTEND, 'store' => $store->getId()
		));
		$templateVars = array(
			'store' => $store
			,'customer' => $this->getCustomer()
			,'unsubscription_url' => Df_Reward_Helper_Customer::s()->getUnsubscribeUrl('update')
			,'points_balance' => $this->getPointsBalance()
		);
		$mail->sendTransactional(
			$store->getConfig(
				self::XML_PATH_BALANCE_UPDATE_TEMPLATE)
			,$store->getConfig(self::XML_PATH_EMAIL_IDENTITY)
			,$this->getCustomer()->getEmail()
			,null,$templateVars,$store->getId()
		);
		if ($mail->getSentSuccess()) {
			$this->setBalanceUpdateSent(true);
		}
		return $this;
	}

	/**
	 * Send low Balance Warning Notification to customer if notification is enabled
	 * @param Varien_Object $item
	 * @return Df_Reward_Model_Reward
	 * @see Df_Reward_Model_Resource_Reward_History_Collection::loadExpiredSoonPoints()
	 */
	public function sendBalanceWarningNotification($item) {
		/* @var Mage_Core_Model_Email_Template $mail */
		$mail = Df_Core_Model_Email_Template::i();
		$mail->setDesignConfig(array(
			'area' => Df_Core_Const_Design_Area::FRONTEND, 'store' => $item->getStoreId()
		));
		$store = df_store($item->getStoreId());
		$templateVars = array(
			'store' => $store
			,'customer_name' => $item->getCustomerFirstname().' '.$item->getCustomerLastname()
			,'unsubscription_url' =>
				Df_Reward_Helper_Customer::s()->getUnsubscribeUrl('warning')
			,'remaining_days' => $store->getConfig('df_reward/notification/expiry_day_before')
			,'points_balance' => $item->getPointsBalanceTotal()
			,'points_expiring' => $item->getTotalExpired()
		);
		$mail->sendTransactional(
			$store->getConfig(self::XML_PATH_BALANCE_WARNING_TEMPLATE)
			,$store->getConfig(self::XML_PATH_EMAIL_IDENTITY)
			,$item->getCustomerEmail()
			,null
			,$templateVars,$store->getId()
		);
		return $this;
	}

	/**
	 * @param Df_Customer_Model_Customer $customer
	 * @return Df_Reward_Model_Reward
	 */
	public function setCustomer(Df_Customer_Model_Customer $customer) {
		$this->setData('customer_id', $customer->getId());
		$this->setData('customer_group_id', $customer->getGroupId());
		$this->setData('customer', $customer);
		return $this;
	}

	/** @return Df_Reward_Model_Reward */
	public function updateRewardPoints() {
		if ($this->canUpdateRewardPoints()) {
			$this->save();
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Reward_Model_Reward
	 */
	protected function _afterSave() {
		if ((int)$this->getPointsDelta() != 0 || $this->getCappedReward()) {
			$this->_prepareCurrencyAmount();
			$this->getHistory()
				->prepareFromReward()
				->save()
			;
			$this->sendBalanceUpdateNotification();
		}
		parent::_afterSave();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Reward_Model_Reward
	 */
	protected function _beforeSave() {
		$this
			->loadByCustomer()
			->_preparePointsDelta()
			->_preparePointsBalance()
		;
		parent::_beforeSave();
		return $this;
	}

	/**
	 * Convert points to currency
	 * @param integer $points
	 * @return float
	 */
	protected function _convertPointsToCurrency($points) {
		$ammount = 0;
		if ($points && $this->getRateToCurrency()) {
			$ammount = $this->getRateToCurrency()->calculateToCurrency($points);
		}
		return(float)$ammount;
	}

	/**
	 * Initialize and fetch if need rate by given direction
	 * @param integer $direction
	 * @return Df_Reward_Model_Reward_Rate
	 */
	protected function _getRateByDirection($direction) {
		if (!isset($this->{__METHOD__}[$direction])) {
			$this->{__METHOD__}[$direction] =
				Df_Reward_Model_Reward_Rate::i()->fetch(
					$this->getCustomerGroupId(), $this->getWebsiteId(), $direction
				)
			;
		}
		return $this->{__METHOD__}[$direction];
	}

	/**
	 * @override
	 * @return Df_Reward_Model_Resource_Reward
	 */
	protected function _getResource() {return Df_Reward_Model_Resource_Reward::s();}

	/**
	 * Prepare currency amount and currency delta
	 * @return Df_Reward_Model_Reward
	 */
	protected function _prepareCurrencyAmount() {
		$amount = 0;
		$amountDelta = 0;
		if ($this->hasPointsDelta()) {
			$amountDelta = $this->_convertPointsToCurrency($this->getPointsDelta());
		}
		$amount = $this->_convertPointsToCurrency($this->getPointsBalance());
		$this->setCurrencyDelta((float)$amountDelta);
		$this->setCurrencyAmount((float)($amount));
		return $this;
	}

	/**
	 * Prepare points balance
	 * @return Df_Reward_Model_Reward
	 */
	protected function _preparePointsBalance() {
		$points = 0;
		if ($this->hasPointsDelta()) {
			$points = $this->getPointsDelta();
		}
		$pointsBalance = 0;
		$pointsBalance = (int)$this->getPointsBalance() + $points;
		$maxPointsBalance = (int)(df_h()->reward()
			->getGeneralConfig('max_points_balance', $this->getWebsiteId()));
		if ($maxPointsBalance != 0 && ($pointsBalance > $maxPointsBalance)) {
			$pointsBalance = $maxPointsBalance;
			$pointsDelta   = $maxPointsBalance - (int)$this->getPointsBalance();
			$croppedPoints = (int)$this->getPointsDelta() - $pointsDelta;
			$this->setPointsDelta($pointsDelta)
				->setIsCappedReward(true)
				->setCroppedPoints($croppedPoints);
		}
		$this->setPointsBalance($pointsBalance);
		return $this;
	}

	/**
	 * Prepare points delta, get points delta from config by action
	 * @return Df_Reward_Model_Reward
	 */
	protected function _preparePointsDelta() {
		$delta = 0;
		$action = $this->getActionInstance($this->getAction());
		if ($action !== null) {
			$delta = $action->getPoints($this->getWebsiteId());
		}
		if ($delta) {
			if ($this->hasPointsDelta()) {
				$delta = $delta + $this->getPointsDelta();
			}
			$this->setPointsDelta((int)$delta);
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * 2015-02-07
		 * Операция «+» для массивов используется в оригинале.
		 * Обратите внимание, что операция «+» игнорирует те элементы второго массива,
		 * ключи которого присутствуют в первом массиве:
		 * «The keys from the first array will be preserved.
		 * If an array key exists in both arrays,
		 * then the element from the first array will be used
		 * and the matching key's element from the second array will be ignored.»
		 * http://php.net/manual/function.array-merge.php
		 * Например:
		 * array(1,2,3) + array(3,4,5) вернёт array(1,2,3).
		 * http://3v4l.org/utnNp
		 *
		 * Для ассоциативных массивов $b + $a по сути эквивалентно array_merge($a, $b)
		 * То есть, в операции «$b + $a» для ассоциативных массивов
		 * второй операнд (массив $a) выступает как бы «значениями по умолчанию»:
		 * эти значения попадают в результат в том случае,
		 * когда их ключи отсутствуют в первом операнде (массиве $b).
		 * @see array_merge() для ассоциативных массивов работает так:
		 * «If the input arrays have the same string keys,
		 * then the later value for that key will overwrite the previous one.»
		 * http://php.net/manual/function.array-merge.php
		 * Например:
				$a = array('a' => 1, 'b' => 2);
				$b = array('a' => 3);
				print_r(intval(($b + $a) === array_merge($a, $b)));
		 * вернёт «1» («true»).
		 * http://3v4l.org/hKICL
		 * Про оператор «===» для массивов:
		 * http://stackoverflow.com/a/5678990
		 */
		self::$_actionModelClasses = self::$_actionModelClasses + array(
			// значения по умолчанию,
			// смотрите комментарий выше о сути операции «+» для ассоциативных массивов
			self::REWARD_ACTION_ADMIN => 'df_reward/action_admin'
			,self::REWARD_ACTION_ORDER => 'df_reward/action_order'
			,self::REWARD_ACTION_REGISTER => 'df_reward/action_register'
			,self::REWARD_ACTION_NEWSLETTER => 'df_reward/action_newsletter'
			,self::REWARD_ACTION_INVITATION_CUSTOMER => 'df_reward/action_invitationCustomer'
			,self::REWARD_ACTION_INVITATION_ORDER => 'df_reward/action_invitationOrder'
			,self::REWARD_ACTION_REVIEW	=> 'df_reward/action_review'
			,self::REWARD_ACTION_TAG => 'df_reward/action_tag'
			,self::REWARD_ACTION_ORDER_EXTRA => 'df_reward/action_orderExtra'
			,self::REWARD_ACTION_CREDITMEMO => 'df_reward/action_creditmemo'
			,self::REWARD_ACTION_SALESRULE => 'df_reward/action_salesrule'
		);
	}

	/** @var bool */
	protected $_modelLoadedByCustomer = false;

	/** @used-by Df_Reward_Model_Resource_Reward_Collection::_construct() */

	const P__ACTION = 'action';
	const P__ACTION_ENTITY = 'action_entity';
	const P__ID = 'reward_id';
	const P__STORE = 'store';
	const REWARD_ACTION_ADMIN = 0;
	const REWARD_ACTION_ORDER = 1;
	const REWARD_ACTION_REGISTER = 2;
	const REWARD_ACTION_NEWSLETTER = 3;
	const REWARD_ACTION_INVITATION_CUSTOMER = 4;
	const REWARD_ACTION_INVITATION_ORDER = 5;
	const REWARD_ACTION_REVIEW = 6;
	const REWARD_ACTION_TAG = 7;
	const REWARD_ACTION_ORDER_EXTRA = 8;
	const REWARD_ACTION_CREDITMEMO = 9;
	const REWARD_ACTION_SALESRULE = 10;
	const XML_PATH_BALANCE_UPDATE_TEMPLATE = 'df_reward/notification/balance_update_template';
	const XML_PATH_BALANCE_WARNING_TEMPLATE = 'df_reward/notification/expiry_warning_template';
	const XML_PATH_EMAIL_IDENTITY = 'df_reward/notification/email_sender';

	/** @return Df_Reward_Model_Resource_Reward_Collection */
	public static function c() {return new Df_Reward_Model_Resource_Reward_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Reward_Model_Reward
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Reward_Model_Reward
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/** @return Df_Reward_Model_Reward */
	public static function s() {static $r; return $r ? $r : $r = new self;}
	/**
	 * Set action Id and action model class.
	 * Check if given action Id is not integer throw exception
	 * @param integer $actionId
	 * @param string $actionModelClass
	 */
	public static function setActionModelClass($actionId, $actionModelClass) {
		if (!is_int($actionId)) {
			Mage::throwException(df_h()->reward()->__('Given action ID has to be an integer value.'));
		}
		self::$_actionModelClasses[$actionId] = $actionModelClass;
	}
	/** @var array */
	static protected $_actionModelClasses = array();
}