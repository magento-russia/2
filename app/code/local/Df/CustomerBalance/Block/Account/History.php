<?php
class Df_CustomerBalance_Block_Account_History extends Df_Core_Block_Template_NoCache {
	/**
	 * Balance history action names
	 *
	 * @var array
	 */
	protected $_actionNames = null;

	/**
	 * Check if history can be shown to customer
	 * @return bool
	 */
	public function canShow() {
		return
				Df_CustomerBalance_Model_Settings::s()->isEnabled()
			&&
				Df_CustomerBalance_Model_Settings::s()->needShowHistory()
		;
	}

	/**
	 * Retreive history events collection
	 * @return Df_CustomerBalance_Model_Resource_Balance_History_Collection|bool
	 */
	public function getEvents() {
		/** @var Df_CustomerBalance_Model_Resource_Balance_History_Collection|bool $result */
		$result = false;
		/** @var int $customerId */
		$customerId = df_session_customer()->getCustomerId();
		if ($customerId) {
			$result = Df_CustomerBalance_Model_Balance_History::c();
			$result->addFieldToFilter('customer_id', $customerId);
			$result->addFieldToFilter('website_id', df_website_id());
			$result->setOrder('updated_at');
		}
		return $result;
	}

	/**
	 * Retreive action labels
	 * @return array
	 */
	public function getActionNames()
	{
		if (is_null($this->_actionNames)) {
			$this->_actionNames =
				Df_CustomerBalance_Model_Balance_History::s()
					->getActionNamesArray()
			;
		}
		return $this->_actionNames;
	}

	/**
	 * Retreive action label
	 *
	 * @param mixed $action
	 * @return string
	 */
	public function getActionLabel($action)
	{
		$names = $this->getActionNames();
		if (isset($names[$action])) {
			return $names[$action];
		}
		return '';
	}
}