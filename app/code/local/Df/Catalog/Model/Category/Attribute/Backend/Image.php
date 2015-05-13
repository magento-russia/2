<?php
class Df_Catalog_Model_Category_Attribute_Backend_Image
	extends Mage_Catalog_Model_Category_Attribute_Backend_Image {
	/**
	 * Цель перекрытия —
	 * устранение сбоя «$_FILES array is empty»
	 * при программном сохранении товарного раздела в устаревших версиях Magento CE
	 * (заметил в Magento CE 1.6.1.0).
	 * @override
	 * @param Varien_Object|Df_Catalog_Model_Category $object
	 * @return Df_Catalog_Model_Category_Attribute_Backend_Image
	 */
	public function afterSave($object) {
		/** @var mixed $value */
		$value = $object->getData($this->getAttribute()->getName());
		if (
				// Это условие взято из родительской реализации метода.
				// В родительской реализации при выполнении этого условия
				// не происходит обращение к $_FILES.
				(is_array($value) && !empty($value['delete']))
			||
				// это условие позволяет избежать сбоя «$_FILES array is empty»
				!empty($_FILES)
		) {
			parent::afterSave($object);
		}
		return $this;
	}
}


 