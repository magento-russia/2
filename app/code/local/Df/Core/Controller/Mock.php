<?php
/**
 * 2015-03-10
 * Этот класс предоставляет публичный доступ к защищённoму (protected) методу
 * @see Mage_Core_Controller_Varien_Action::_getRefererUrl()
 * Обратите внимание на используемую технику:
 * класс @see Df_Core_Controller_Mock имеет доступ
 * к защищённым методам объекта класса @see Mage_Core_Controller_Varien_Action,
 * потому что является его наследником.
 * http://3v4l.org/LddSV
 */
final class Df_Core_Controller_Mock extends Mage_Core_Controller_Varien_Action {
	/**
	 * @used-by df_referer()
	 * @return string
	 */
	public static function getRefererUrl() {
		/** @var Mage_Core_Controller_Varien_Action $action */
		$action = Mage::app()->getFrontController()->getData('action');
		return $action->_getRefererUrl();
	}
}