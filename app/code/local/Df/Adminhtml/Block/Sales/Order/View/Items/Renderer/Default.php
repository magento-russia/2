<?php
class Df_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default extends Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default {
	/**
	 * Цель перекрытия —
	 * позволить скрывать копейки при отображении цен в таблице заказов.
	 * @override
	 * @param $basePrice
	 * @param $price
	 * @param bool $strong
	 * @param string $separator
	 * @return string
	 */
	public function displayPrices($basePrice, $price, $strong = false, $separator = '<br />') {
		return
			rm_loc()->needHideDecimals()
			? $this->displayPricesDf($basePrice, $price, $strong, $separator)
			: parent::displayPrices($basePrice, $price, $strong, $separator)
		;
	}

	/**
	 * @param $basePrice
	 * @param $price
	 * @param bool $strong
	 * @param string $separator
	 * @return string
	 */
	private function displayPricesDf($basePrice, $price, $strong = false, $separator = '<br />') {
		return
			$this->displayRoundedPrices(
				$basePrice
				,$price
				,rm_currency()->getPrecision()
				,$strong
				,$separator
			)
		;
	}
}