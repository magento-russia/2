<?php
class Df_1C_Model_Cml2_Import_Data_Entity_Attribute_Date
	extends Df_1C_Model_Cml2_Import_Data_Entity_Attribute {
	/**
	 * @override
	 * @param string $valueAsString
	 * @return string
	 */
	public function convertValueToMagentoFormat($valueAsString) {
		return df_dtss($valueAsString, self::FORMAT, Varien_Date::DATETIME_INTERNAL_FORMAT, true);
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

	/**
	 * Используется из @see Df_1C_Model_Cml2_Import_Data_Entity_Attribute::getTypeMap()
	 */
	const _CLASS = __CLASS__;
	const FORMAT = 'dd.MM.yyyy H:mm:ss';
}