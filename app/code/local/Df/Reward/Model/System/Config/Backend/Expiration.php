<?php
/**
 * Backend model for "Reward Points Lifetime"
 *
 */
class Df_Reward_Model_System_Config_Backend_Expiration extends Mage_Core_Model_Config_Data {
	const XML_PATH_EXPIRATION_DAYS = 'df_reward/general/expiration_days';

	/**
	 * Update history expiration date to simplify frontend calculations
	 * @return Df_Reward_Model_System_Config_Backend_Expiration
	 */
	protected function _beforeSave()
	{
		parent::_beforeSave();
		if (!$this->isValueChanged()) {
			return $this;
		}

		$websiteIds = array();
		if ($this->getWebsiteCode()) {
			$websiteIds = array(Mage::app()->getWebsite($this->getWebsiteCode())->getId());
		} else {
			$collection = Mage::getResourceModel('core/config_data_collection')
				->addFieldToFilter('path', self::XML_PATH_EXPIRATION_DAYS)
				->addFieldToFilter('scope', 'websites');
			$websiteScopeIds = array();
			foreach ($collection as $item) {
				$websiteScopeIds[]= $item->getScopeId();
			}
			foreach (Mage::app()->getWebsites() as $website) {
				/* @var $website Mage_Core_Model_Website */
				if (!in_array($website->getId(), $websiteScopeIds)) {
					$websiteIds[]= $website->getId();
				}
			}
		}
		if (count($websiteIds) > 0) {
			Mage::getResourceModel('df_reward/reward_history')
				->updateExpirationDate($this->getValue(), $websiteIds);
		}
		return $this;
	}

	/**
	 * The same as _beforeSave, but executed when website config extends default values
	 * @return Df_Reward_Model_System_Config_Backend_Expiration
	 */
	protected function _beforeDelete()
	{
		parent::_beforeDelete();
		if ($this->getWebsiteCode()) {
			$default = (string)Mage::getConfig()->getNode('default/' . self::XML_PATH_EXPIRATION_DAYS);
			$websiteIds = array(Mage::app()->getWebsite($this->getWebsiteCode())->getId());
			Mage::getResourceModel('df_reward/reward_history')
				->updateExpirationDate($default, $websiteIds);
		}
		return $this;
	}
}