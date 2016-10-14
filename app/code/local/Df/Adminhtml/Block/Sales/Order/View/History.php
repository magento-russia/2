<?php
class Df_Adminhtml_Block_Sales_Order_View_History extends Mage_Adminhtml_Block_Sales_Order_View_History {
	/**
	 * Цель перекрытия —
	 * позволить сохранять некоторые теги HTML (т.е. форматирование)
	 * в комментариях к заказу.
	 * @override
	 * @param string|string[] $data
	 * @param string[]|null $allowedTags [optional]
	 * @return string
	 */
	public function escapeHtml($data, $allowedTags = null) {
		$allowedTags = is_null($allowedTags) ? array() : $allowedTags;
		/** @var Df_Sales_Model_Settings_OrderComments $settings */
		$settings = df_cfg()->sales()->orderComments();
		if ($settings->preserveSomeTagsInAdminOrderView()) {
			$allowedTags = array_merge(
				$allowedTags, $settings->getTagsToPreserveInAdminOrderView()
			);
		}
		if ($settings->preserveLineBreaksInAdminOrderView()) {
			$allowedTags[]= 'br';
			$data = df_t()->nl2br($data);
		}
		/**
		 * 2015-02-06
		 * Т.к. ключи массива — целочисленные, то результат применения @uses array_merge()
		 * может содержать повторяющиеся элементы,
		 * которые мы удаляем посредством @uses array_unique().
		 * http://php.net/manual/function.array-merge.php
		 * «If, however, the arrays contain numeric keys,
		 * the later value will not overwrite the original value, but will be appended.»
		 */
		$allowedTags = rm_array_unique_fast($allowedTags);
		return parent::escapeHtml($data, $allowedTags);
	}
}