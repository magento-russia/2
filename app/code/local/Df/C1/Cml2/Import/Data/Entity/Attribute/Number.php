<?php
class Df_C1_Cml2_Import_Data_Entity_Attribute_Number
	extends Df_C1_Cml2_Import_Data_Entity_Attribute {
	/**
	 * @override
	 * @return string
	 */
	public function getBackendModel() {return '';}

	/**
	 * @override
	 * @return string
	 */
	public function getBackendType() {return 'varchar';}

	/**
	 * @override
	 * @return string
	 */
	public function getFrontendInput() {return 'text';}

	/**
	 * @override
	 * @return string
	 */
	public function getSourceModel() {return '';}

	/** @used-by Df_C1_Cml2_Import_Data_Entity_Attribute::getTypeMap() */

}