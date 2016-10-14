<?php
/**
 * Currency cell renderer for customerbalance grids
 *
 */
class Df_CustomerBalance_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency
	extends Df_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency {	/** @var array */
	protected static $_websiteBaseCurrencyCodes = array();

	/**
	 * Get currency code by row data
	 *
	 * @param Varien_Object $row
	 * @return string
	 */
	protected function _getCurrencyCode($row)
	{
		$websiteId = $row->getData('website_id');
		$orphanCurrency = $row->getData('base_currency_code');
		if ($orphanCurrency !== null) {
			return $orphanCurrency;
		}
		if (!isset(self::$_websiteBaseCurrencyCodes[$websiteId])) {
			self::$_websiteBaseCurrencyCodes[$websiteId] = rm_website($websiteId)->getBaseCurrencyCode();
		}
		return self::$_websiteBaseCurrencyCodes[$websiteId];
	}

	/**
	 * Stub getter for exchange rate
	 *
	 * @param Varien_Object $row
	 * @return int
	 */
	protected function _getRate($row)
	{
		return 1;
	}
}