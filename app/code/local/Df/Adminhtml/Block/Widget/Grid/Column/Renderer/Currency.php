<?php
class Df_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency 
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency {
	/**
	 * Цель перекрытия —
	 * позволить скрывать копейки при отображении цен
	 * в различных административных таблицах.
	 * @override
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row) {
		return rm_loc()->needHideDecimals() ? $this->renderDf($row) : parent::render($row);
	}

	/**
	 * @param Varien_Object $row
	 * @return string|null
	 */
	private function renderDf(Varien_Object $row) {
		/** @var Varien_Object $column */
		$column = $this->getColumn();
		/** @var string|null $result */
		/** @var float|null $value */
		$value = $row->getData($column->getDataUsingMethod('index'));
		if (!$value) {
			$result = $column->getDataUsingMethod('default');
		}
		else {
			/** @var string|null $currencyCode */
			$currencyCode = $this->_getCurrencyCode($row);
			if (!$currencyCode) {
				$result = $value;
			}
			else {
				$value = df_float($value) * $this->_getRate($row);
				/** @var string $sign */
				$sign = $column->getDataUsingMethod('show_number_sign') && (0 < $value) ? '+' : '';
				$result = $sign . df_money_fl($value, $currencyCode);
			}
		}
		return $result;
	}
}