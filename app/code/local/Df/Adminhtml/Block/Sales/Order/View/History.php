<?php
class Df_Adminhtml_Block_Sales_Order_View_History extends Mage_Adminhtml_Block_Sales_Order_View_History {
	/**
	 * Цель перекрытия —
	 * позволить сохранять некоторые теги HTML (т.е. форматирование)
	 * в комментариях к заказу.
	 * @override
	 * @param mixed $data
	 * @param string[] $allowedTags[optional]
	 * @return string
	 */
	public function escapeHtml($data, $allowedTags = null) {
		if (df_enabled(Df_Core_Feature::SALES)) {
			if (is_null($allowedTags)) {
				$allowedTags = array();
			}
			if (df_cfg()->sales()->orderComments()->preserveSomeTagsInAdminOrderView()) {
				$allowedTags =
					array_merge(
						$allowedTags
						,df_cfg()->sales()->orderComments()
							->getTagsToPreserveInAdminOrderView()
					)
				;
			}
			if (df_cfg()->sales()->orderComments()->preserveLineBreaksInAdminOrderView()) {
				$allowedTags[]= 'br';
				$data = nl2br($data);
			}
			$allowedTags = rm_array_unique_fast($allowedTags);
		}
		/** @var string $result */
		$result =
			df_text()->escapeHtml($data, $allowedTags)
		;
		df_result_string($result);
		return $result;
	}
}