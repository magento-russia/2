<?php
class Df_Reward_Block_Customer_Reward extends Df_Core_Block_Template_NoCache {
	/**
	 * Set template variables
	 * @return string
	 */
	protected function _toHtml()
	{
		$this->setBackUrl($this->getUrl('customer/account/'));
		return parent::_toHtml();
	}
}