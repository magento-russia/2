<?php
/**
 * Log grid container
 */
class Df_Logging_Block_Log extends Mage_Adminhtml_Block_Widget_Container {
	/**
	 * Header text getter
	 * @return string
	 */
	public function getHeaderText()
	{
		return Df_Logging_Helper_Data::s()->__('Admin Actions Log');
	}

	/**
	 * Grid contents getter
	 * @return string
	 */
	public function getGridHtml()
	{
		return $this->getChildHtml();
	}
}