<?php
class Df_Sales_Block_Order_View extends Mage_Sales_Block_Order_View {
	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности сохранения переносов строк
	 * при отображении комментария к заказу в личном кабинете покупателя.
	 * @override
	 * @param mixed $data
	 * @param array $allowedTags [optional]
	 * @return string
	 */
	public function escapeHtml($data, $allowedTags = null) {
		if (df_cfg()->sales()->orderComments()->preserveLineBreaksInCustomerAccount()) {
			if (is_null($allowedTags)) {
				$allowedTags = array();
			}
			$allowedTags[]= 'br';
			$data = df_t()->nl2br($data);
		}
		return parent::escapeHtml($data, $allowedTags);
	}
}