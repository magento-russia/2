<?php
class Df_Reward_Block_Customer_Reward extends Df_Core_Block_Template_NoCache {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		$this->setBackUrl($this->getUrl('customer/account/'));
		return parent::_toHtml();
	}
}