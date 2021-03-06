<?php
/**
 * Reward Helper
 */
class Df_Reward_Helper_Data extends Mage_Core_Helper_Abstract {
	const XML_PATH_SECTION_GENERAL = 'df_reward/general/';
	const XML_PATH_SECTION_POINTS = 'df_reward/points/';
	const XML_PATH_SECTION_NOTIFICATIONS = 'df_reward/notification/';
	protected $_expiryConfig;
	protected $_hasRates = true;

	/** @return Df_Varien_Data_Collection */
	public function getSalesRuleApplications() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Df_Varien_Data_Collection();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Setter for hasRates flag
	 *
	 * @param boolean $flag
	 * @return Df_Reward_Helper_Data
	 */
	public function setHasRates($flag)
	{
		$this->_hasRates = $flag;
		return $this;
	}

	/**
	 * Getter for hasRates flag
	 * @return boolean
	 */
	public function getHasRates() {
		return $this->_hasRates;
	}

	/**
	 * Check whether reward module is enabled in system config
	 * @return bool
	 */
	public function isEnabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					Mage::isInstalled()
				&&
					df_enabled(Df_Core_Feature::REWARD)
				&&
					Mage::getStoreConfigFlag('df_reward/general/enabled')
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Check whether reward module is enabled in system config on front per website
	 * @param integer $websiteId
	 * @return boolean
	 */
	public function isEnabledOnFront($websiteId = null) {
		if ($websiteId === null) {
			$websiteId = Mage::app()->getStore()->getWebsiteId();
		}
		return($this->isEnabled() && $this->getGeneralConfig('enabled_on_front', (int)$websiteId));
	}

	/**
	 * Retrieve value of given field and website from config
	 *
	 * @param string $section
	 * @param string $field
	 * @param integer $websiteId
	 * @return mixed
	 */
	public function getConfigValue($section, $field, $websiteId = null)
	{
		if ($websiteId === null) {
			$websiteId = Mage::app()->getWebsite()->getId();
		}
		return(string)Mage::app()->getConfig()->getNode($section . $field, 'website', (int)$websiteId);
	}

	/**
	 * Retrieve config value from General section
	 *
	 * @param string $field
	 * @param integer $websiteId
	 * @return mixed
	 */
	public function getGeneralConfig($field, $websiteId = null)
	{
		return $this->getConfigValue(self::XML_PATH_SECTION_GENERAL, $field, $websiteId);
	}

	/**
	 * @param int $pointsAmount
	 * @return string
	 */
	public function getPointsAmountAsText($pointsAmount) {
		// Когда изначально был чужим,
		// поэтому на всякий случай приводим количество баллов к целому типу
		$pointsAmount = intval($pointsAmount);
		/** @var string $result */
		$result =
			rm_sprintf(
				'%d %s'
				,$pointsAmount
				,df_text()->getNounForm($pointsAmount, array('балл', 'балла', 'баллов'))
			)
		;
		return $result;
	}

	/**
	 * Retrieve config value from Points section
	 *
	 * @param string $field
	 * @param integer $websiteId
	 * @return mixed
	 */
	public function getPointsConfig($field, $websiteId = null)
	{
		return $this->getConfigValue(self::XML_PATH_SECTION_POINTS, $field, $websiteId);
	}

	/**
	 * Retrieve config value from Notification section
	 *
	 * @param string $field
	 * @param integer $websiteId
	 * @return mixed
	 */
	public function getNotificationConfig($field, $websiteId = null)
	{
		return $this->getConfigValue(self::XML_PATH_SECTION_NOTIFICATIONS, $field, $websiteId);
	}

	/**
	 * Return acc array of websites expiration points config
	 * @return array
	 */
	public function getExpiryConfig()
	{
		if ($this->_expiryConfig === null) {
			$result = array();
			foreach (Mage::app()->getWebsites() as $website) {
				$websiteId = $website->getId();
				$result[$websiteId] = new Varien_Object(array(
					'expiration_days' => $this->getGeneralConfig('expiration_days', $websiteId),'expiry_calculation' => $this->getGeneralConfig('expiry_calculation', $websiteId),'expiry_day_before' => $this->getNotificationConfig('expiry_day_before', $websiteId)
				));
			}
			$this->_expiryConfig = $result;
		}
		return $this->_expiryConfig;
	}

	/**
	 * Format (add + or - sign) before given points count
	 *
	 * @param integer $points
	 * @return string
	 */
	public function formatPointsDelta($points)
	{
		$formatedPoints = $points;
		if ($points > 0) {
			$formatedPoints = '+'.$points;
		} else if ($points < 0) {
			$formatedPoints = '-'.(-1*$points);
		}
		return $formatedPoints;
	}

	/**
	 * Getter for "Learn More" landing page URL
	 * @return string
	 */
	public function getLandingPageUrl()
	{
		$pageIdentifier = Mage::getStoreConfig('df_reward/general/landing_page');
		return Mage::getUrl('', array('_direct' => $pageIdentifier));
	}

	/**
	 * Render a reward message as X points Y money
	 *
	 * @param int $points
	 * @param float|null $amount
	 * @param int|null $storeId
	 * @param string $pointsFormat
	 * @param string $amountFormat
	 * @return string
	 */
	public function formatReward($points, $amount = null, $storeId = null, $pointsFormat = '%s', $amountFormat = '%s')
	{
		/** @var string $result */
		$result = $this->getPointsAmountAsText($points);
		if (!is_null($amount)) {
			$result =
				rm_sprintf(
					'%s (%s)'
					,$result
					,rm_sprintf($amountFormat, $this->formatAmount($amount, true, $storeId))
				)
			;
		}
		return $result;
	}

	/**
	 * Format an amount as currency or rounded value
	 *
	 * @param double|string|null $amount
	 * @param bool $asCurrency
	 * @param int|null $storeId
	 * @return string|null
	 */
	public function formatAmount($amount, $asCurrency = true, $storeId = null)
	{
		if (null === $amount) {
			return  null;
		}
		return
				$asCurrency
			?
				Mage::app()->getStore($storeId)
					->convertPrice(
						$amount
						,true
						,false
					)
			:
				rm_sprintf('%.2F', $amount)
		;
	}

	/**
	 * Format points to currency rate
	 *
	 * @param int $points
	 * @param float $amount
	 * @param string $currencyCode
	 * @return string
	 */
	public function formatRateToCurrency($points, $amount, $currencyCode = null) {
		return
			implode(
				' = '
				,array(
					$this->getPointsAmountAsText($points)
					,$this->getCurrencyAmountAsText($amount, $currencyCode)
				)
			)
		;
	}

	/**
	 * Format currency to points rate
	 *
	 * @param int $points
	 * @param float $amount
	 * @param string $currencyCode
	 * @return string
	 */
	public function formatRateToPoints($points, $amount, $currencyCode = null) {
		return
			implode(
				' = '
				,array(
					$this->getCurrencyAmountAsText($amount, $currencyCode)
					,$this->getPointsAmountAsText($points)
				)
			)
		;
	}

	/**
	 * @param float $currencyAmount
	 * @param string|null $currencyCode[optional]
	 * @return string
	 */
	private function getCurrencyAmountAsText($currencyAmount, $currencyCode = null) {
		/** @var string $result */
		$result =
				is_null($currencyCode)
			?
				rm_sprintf(
					'%.0F %s'
					,$currencyAmount
					,df_text()->getNounForm(rm_floor($currencyAmount), array(
						'валютная единица'
						,'валютных единицы'
						,'валютных единиц'
					))
				)
			:
				Mage::app()->getLocale()
					->currency($currencyCode)
						->toCurrency(
							(float)$currencyAmount
						)
		;
		df_result_string($result);
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Reward_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}