<?php
class Df_Reward_Model_Config_Backend_Expiration extends Mage_Core_Model_Config_Data {
	const XML_PATH_EXPIRATION_DAYS = 'df_reward/general/expiration_days';

	/**
	 * Update history expiration date to simplify frontend calculations
	 * @return Df_Reward_Model_Config_Backend_Expiration
	 */
	protected function _beforeSave()
	{
		parent::_beforeSave();
		if (!$this->isValueChanged()) {
			return $this;
		}

		$websiteIds = array();
		if ($this->getWebsiteCode()) {
			$websiteIds = array(rm_website($this->getWebsiteCode())->getId());
		} else {
			/** @var Mage_Core_Model_Resource_Config_Data_Collection $collection */
			$collection = Df_Core_Model_Config_Data::c();
			$collection->addFieldToFilter('path', self::XML_PATH_EXPIRATION_DAYS);
			$collection->addFieldToFilter('scope', 'websites');
			/** @uses Df_Core_Model_Config_Data::getScopeId() */
			$websiteScopeIds = $collection->walk('getScopeId');
			foreach (Mage::app()->getWebsites() as $website) {
				/* @var $website Mage_Core_Model_Website */
				if (!in_array($website->getId(), $websiteScopeIds)) {
					$websiteIds[]= $website->getId();
				}
			}
		}
		if ($websiteIds) {
			Df_Reward_Model_Resource_Reward_History::s()->updateExpirationDate(
				$this->getValue(), $websiteIds
			);
		}
		return $this;
	}

	/**
	 * The same as _beforeSave, but executed when website config extends default values
	 * @return Df_Reward_Model_Config_Backend_Expiration
	 */
	protected function _beforeDelete() {
		parent::_beforeDelete();
		if ($this->getWebsiteCode()) {
			Df_Reward_Model_Resource_Reward_History::s()->updateExpirationDate(
				rm_leaf_s(rm_config_node('default',  self::XML_PATH_EXPIRATION_DAYS))
				, array(rm_website($this->getWebsiteCode())->getId())
			);
		}
		return $this;
	}
}