<?php
namespace Df\C1\Cml2\Import\Data\Entity\Attribute;
/**
 * 2016-11-04
 * «Boolean» (unlike «Bool») is not a reserved word in PHP 7 nor PHP 5.x
 * https://3v4l.org/OP3MZ
 * http://php.net/manual/en/reserved.other-reserved-words.php
 */
class Boolean extends \Df\C1\Cml2\Import\Data\Entity\Attribute {
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
		return dfa(array('true' => '1', 'false' => '0'), $value, '');
	}

	/**
	 * @override
	 * @return string
	 */
	public function getBackendModel() {return '';}

	/**
	 * @override
	 * @return string
	 */
	public function getBackendType() {return 'int';}

	/**
	 * @override
	 * @return string
	 */
	public function getFrontendInput() {return 'select';}

	/**
	 * @override
	 * @return string
	 */
	public function getSourceModel() {return 'eav/entity_attribute_source_boolean';}
}