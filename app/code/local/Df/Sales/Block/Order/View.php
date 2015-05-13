<?php
class Df_Sales_Block_Order_View extends Mage_Sales_Block_Order_View {
	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности сохранения переносов строк
	 * при отображении комментария к заказу в личном кабинете покупателя.
	 * @override
	 * @param mixed $data
	 * @param array $allowedTags[optional]
	 * @return string
	 */
	public function escapeHtml($data, $allowedTags = null) {
		if (
				df_enabled(Df_Core_Feature::SALES)
			&&
				df_cfg()->sales()->orderComments()->preserveLineBreaksInCustomerAccount()
		) {
			if (is_null($allowedTags)) {
				$allowedTags = array();
			}
			$allowedTags[]= 'br';
			$data = nl2br($data);
		}
		return df_text()->escapeHtml($data, $allowedTags);
	}
}