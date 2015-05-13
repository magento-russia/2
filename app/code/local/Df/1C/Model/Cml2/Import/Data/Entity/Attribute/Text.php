<?php
class Df_1C_Model_Cml2_Import_Data_Entity_Attribute_Text
	extends Df_1C_Model_Cml2_Import_Data_Entity_Attribute {
	/**
	 * @override
	 * @return string
	 */
	public function getBackendModel() {
		return '';
	}

	/**
	 * @override
	 * @return string
	 */
	public function getBackendType() {
		return 'varchar';
	}

	/**
	 * @override
	 * @return string
	 */
	public function getFrontendInput() {
		return 'text';
	}

	/**
	 * @override
	 * @return string
	 */
	public function getSourceModel() {
		return '';
	}

	/**
	 * Используется из @see Df_1C_Model_Cml2_Import_Data_Entity_Attribute::create()
	 */
	const _CLASS = __CLASS__;
}