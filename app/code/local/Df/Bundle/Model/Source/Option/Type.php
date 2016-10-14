<?php
class Df_Bundle_Model_Source_Option_Type extends Mage_Bundle_Model_Source_Option_Type {
	/**
	 * Цель перекрытия —
	 * перевод названий типов элементов управления,
	 * которые будут доступны на витринном экране товарного комплекта:
	 * «Drop-down», «Radio Buttons», «Checkbox», «Multiple Select».
	 * Эти элементы управления описаны в настроечной ветке
	 * global/catalog/product/options/bundle/types
	 * в файле config.xml модуля Mage_Bundle.
	 * @return string[][]
	 */
	public function toOptionArray() {
		/** @var string[][] $result */
		$result = parent::toOptionArray();
		foreach ($result as &$item) {
			/** @var string[] $item */
			$item['label'] = df_mage()->bundleHelper()->__(df_a($item, 'label'));
		}
		return $result;
	}
}