<?php
/**
 * Обратите внимание, что родительский класс —
 * именно @see Mage_Core_Model_Resource_Transaction даже в Magento CE 1.4.
 * Также обратите внимание, что родительский класс не наследуется ни от какого другого класса
 * (ни от Mage_Core_Model_Abstract, ни от Varien_Object).
 */
class Df_Core_Model_Resource_Transaction extends Mage_Core_Model_Resource_Transaction {
	const _C = __CLASS__;
	/**
	 * @used-by Df_Chronopay_StandardController::saveInvoice()
	 * @used-by Df_Payment_Model_Action_Abstract::saveInvoice()
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Resource_Transaction
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}