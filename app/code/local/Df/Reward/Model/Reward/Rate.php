<?php
/**
 * @method Df_Reward_Model_Resource_Reward_Rate getResource()
 */
class Df_Reward_Model_Reward_Rate extends Df_Core_Model {
	const RATE_EXCHANGE_DIRECTION_TO_CURRENCY = 1;
	const RATE_EXCHANGE_DIRECTION_TO_POINTS   = 2;

	/**
	 * Rate text getter
	 *
	 * @param int $direction
	 * @param int $points
	 * @param float $amount
	 * @param string $currencyCode
	 * @return string|null
	 */
	public static function getRateText($direction, $points, $amount, $currencyCode = null)
	{
		switch($direction) {
			case self::RATE_EXCHANGE_DIRECTION_TO_CURRENCY:
				return df_h()->reward()->formatRateToCurrency($points, $amount, $currencyCode);
			case self::RATE_EXCHANGE_DIRECTION_TO_POINTS:
				return df_h()->reward()->formatRateToPoints($points, $amount, $currencyCode);
		}
		return null;
	}

	/**
	 * Processing object before save data.
	 * Prepare rate data
	 * @return Df_Reward_Model_Reward_Rate
	 */
	protected function _beforeSave()
	{
		parent::_beforeSave();
		$this->_prepareRateValues();
		return $this;
	}

	/**
	 * Validate rate data
	 * @return boolean | string
	 */
	public function validate()
	{
		return true;
	}

	/**
	 * Reset rate data
	 * @return Df_Reward_Model_Reward_Rate
	 */
	public function reset()
	{
		$this->setData(array());
		return $this;
	}

	/**
	 * Check if given rate data (website, customer group, direction)
	 * is unique to current (already loaded) rate
	 *
	 * @param integer $websiteId
	 * @param integer $customerGroupId
	 * @param integer $direction
	 * @return boolean
	 */
	public function getIsRateUniqueToCurrent($websiteId, $customerGroupId, $direction)
	{
		$data = $this->getResource()->getRateData($websiteId, $customerGroupId, $direction);
		if ($data && $data['rate_id'] != $this->getId()) {
			return false;
		}
		return true;
	}

	/**
	 * Prepare values in order to defined direction
	 * @return Df_Reward_Model_Reward_Rate
	 */
	protected function _prepareRateValues()
	{
		if ($this->_getData('direction') == self::RATE_EXCHANGE_DIRECTION_TO_CURRENCY) {
			$this->setData('points', (int)$this->_getData('value'));
			$this->setData('currency_amount', (float)$this->_getData('equal_value'));
		} else if ($this->_getData('direction') == self::RATE_EXCHANGE_DIRECTION_TO_POINTS) {
			$this->setData('currency_amount', (float)$this->_getData('value'));
			$this->setData('points', (int)$this->_getData('equal_value'));
		}
		return $this;
	}

	/**
	 * Fetch rate by customer group and website
	 * @param integer $customerGroupId
	 * @param integer $websiteId
	 * @param $direction
	 * @return Df_Reward_Model_Reward_Rate
	 */
	public function fetch($customerGroupId, $websiteId, $direction) {
		$this->setData('original_website_id', $websiteId)
			->setData('original_customer_group_id', $customerGroupId);
		$this->getResource()->fetch($this, $customerGroupId, $websiteId, $direction);
		return $this;
	}

	/**
	 * Calculate currency amount of given points by rate
	 *
	 * @param integer $points
	 * @param bool
	 * Whether to round points to integer or not
	 * @return float
	 */
	public function calculateToCurrency($points, $rounded = true)
	{
		$amount = 0;
		if ($this->getPoints()) {
			if ($rounded) {
				$roundedPoints = (int)($points/$this->getPoints());
			} else {
				$roundedPoints = round($points/$this->getPoints(), 2);
			}
			if ($roundedPoints) {
				$amount = $this->getCurrencyAmount()*$roundedPoints;
			}
		}
		return(float)$amount;
	}

	/**
	 * Calculate points of given amount by rate
	 *
	 * @param float $amount
	 * @return integer
	 */
	public function calculateToPoints($amount)
	{
		$points = 0;
		if ($this->getCurrencyAmount() && $amount >= $this->getCurrencyAmount()) {
			$amountValue = (int)($amount/$this->getCurrencyAmount());
			if ($amountValue) {
				$points = $this->getPoints()*$amountValue;
			}
		}
		return $points;
	}

	/**
	 * Retrieve option array of rate directions with labels
	 * @return array
	 */
	public function getDirectionsOptionArray()
	{
		$optArray = array(
			self::RATE_EXCHANGE_DIRECTION_TO_CURRENCY => df_h()->reward()->__('Points to Currency'),self::RATE_EXCHANGE_DIRECTION_TO_POINTS => df_h()->reward()->__('Currency to Points')
		);
		return $optArray;
	}

	/**
	 * Getter for currency part of the rate
	 * Formatted value returns string
	 *
	 * @param bool $formatted
	 * @return mixed|string
	 */
	public function getCurrencyAmount($formatted = false)
	{
		$amount = $this->_getData('currency_amount');
		if ($formatted) {
			$websiteId = $this->getOriginalWebsiteId();
			if ($websiteId === null) {
				$websiteId = $this->getWebsiteId();
			}
			$currencyCode = df_website($websiteId)->getBaseCurrencyCode();
			return rm_currency ($currencyCode)->toCurrency($amount);
		}
		return $amount;
	}

	/**
	 * Getter for points part of the rate
	 * Formatted value returns as int
	 *
	 * @param bool $formatted
	 * @return mixed|int
	 */
	public function getPoints($formatted = false)
	{
		$pts = $this->_getData('points');
		return $formatted ? (int)$pts : $pts;
	}

	/**
	 * @override
	 * @return Df_Reward_Model_Resource_Reward_Rate_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Reward_Model_Resource_Reward_Rate
	 */
	protected function _getResource() {return Df_Reward_Model_Resource_Reward_Rate::s();}

	/** @used-by Df_Reward_Model_Resource_Reward_Rate_Collection::_construct() */

	const P__ID = 'rate_id';

	/** @return Df_Reward_Model_Resource_Reward_Rate_Collection */
	public static function c() {return new Df_Reward_Model_Resource_Reward_Rate_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Reward_Model_Reward_Rate
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Reward_Model_Reward_Rate
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/** @return Df_Reward_Model_Reward_Rate */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}