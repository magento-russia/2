<?php
class Df_Dataflow_Model_Convert_Mapper_Url_Key extends Df_Dataflow_Model_Convert_Mapper_Abstract {
	/**
	 * @param array(string => string) $row
	 * @return array(string => string)
	 */
	protected function processRow(array $row) {
		$row[self::ATTRIBUTE_URL_KEY] =
			Df_Catalog_Helper_Product_Url::s()->extendedFormat(dfa($row, self::ATTRIBUTE_NAME))
		;
		return $row;
	}

	const ATTRIBUTE_NAME = 'name';
	const ATTRIBUTE_URL_KEY = 'url_key';
}