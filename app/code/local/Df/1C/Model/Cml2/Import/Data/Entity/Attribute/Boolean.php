<?php
class Df_1C_Model_Cml2_Import_Data_Entity_Attribute_Boolean
	extends Df_1C_Model_Cml2_Import_Data_Entity_Attribute {
	/**
	 * @override
	 * @param string $valueAsString
	 * @return string
	 */
	public function convertValueToMagentoFormat($valueAsString) {
		/** @var string $result */
		$result =
			df_a(
				array(
					'true' => '1'
					,'false' => '0'
				)
				,$valueAsString
				,''
			)
		;
		df_result_string($result);
		return $valueAsString;
	}

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
		return 'int';
	}

	/**
	 * @override
	 * @return string
	 */
	public function getFrontendInput() {
		return 'select';
	}

	/**
	 * @override
	 * @return string
	 */
	public function getSourceModel() {
		return 'eav/entity_attribute_source_boolean';
	}

	/**
	 * Используется из @see Df_1C_Model_Cml2_Import_Data_Entity_Attribute::getTypeMap()
	 */
	const _CLASS = __CLASS__;
}