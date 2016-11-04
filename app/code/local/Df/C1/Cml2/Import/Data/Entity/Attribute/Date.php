<?php
namespace Df\C1\Cml2\Import\Data\Entity\Attribute;
class Date extends \Df\C1\Cml2\Import\Data\Entity\Attribute {
	/**
	 * 2015-02-06
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom::getValueForDataflow()
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom::getValueForObject()
	 * Метод @used-by Df_Dataflow_Model_Import_Abstract_Row::getFieldValue()
	 * проверяет принадлежность результата
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom::getValueForDataflow()
	 * одному из типов: string|int|float|bool|null
	 * @override
	 * @param string|int|float|bool|null $value
	 * @return string|int|float|bool|null
	 */
	public function convertValueToMagentoFormat($value) {
		return df_dtss($value, 'dd.MM.yyyy H:mm:ss', \Varien_Date::DATETIME_INTERNAL_FORMAT, true);
	}

	/**
	 * @override
	 * @return string
	 */
	public function getBackendModel() {return 'eav/entity_attribute_backend_datetime';}

	/**
	 * @override
	 * @return string
	 */
	public function getBackendType() {return 'datetime';}

	/**
	 * @override
	 * @return string
	 */
	public function getFrontendInput() {return 'date';}

	/**
	 * @override
	 * @return string
	 */
	public function getSourceModel() {return '';}
}