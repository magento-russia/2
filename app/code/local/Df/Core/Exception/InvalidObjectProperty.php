<?php
namespace Df\Core\Exception;
use Df\Core\Exception;
/**
 * @used-by Df_Core_Block_Abstract::_validateByConcreteValidator()
 * @used-by Df_Core_Block_Template::_validateByConcreteValidator()
 * @used-by Df_Core_Model::_validateByConcreteValidator()
 */
class InvalidObjectProperty extends Exception {
	/**
	 * @param object $object
	 * @param string $propertyName
	 * @param mixed $propertyValue
	 * @param \Zend_Validate_Interface $failedValidator
	 */
	public function __construct(
		$object, $propertyName, $propertyValue, \Zend_Validate_Interface $failedValidator) {
		parent::__construct(sprintf(
			"«%s»: значение %s недопустимо для свойства «%s».\nСообщение проверяющего:\n%s"
			,get_class($object)
			,rm_debug_type($propertyValue)
			,$propertyName
			,df_cc_n($failedValidator->getMessages())
		));
	}
}