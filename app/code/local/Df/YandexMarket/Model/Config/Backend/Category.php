<?php
class Df_YandexMarket_Model_Config_Backend_Category
	extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract {
	/**
	 * @overide
	 * @param Varien_Object $object
	 * @return Df_YandexMarket_Model_Config_Backend_Category
	 */
	public function beforeSave($object) {
		try {
			/** @var string|null $value */
			$value = $object->getData($this->getAttribute()->getAttributeCode());
			if ($value && !Df_YandexMarket_Model_Categories::s()->isPathValid($value)) {
				df_error(
					"Категория «%s» отсутствует"
					. " в <a href='%s'>официальном перечне категорий Яндекс.Маркета</a>"
					,$value
					,df_cfg()->yandexMarket()->other()->getCategoriesReferenceBookUrl()
				);
			}
		}
		catch (Exception $e) {
			df_exception_to_session($e);
		}
		return $this;
	}
}