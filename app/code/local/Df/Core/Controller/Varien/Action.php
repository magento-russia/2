<?php
class Df_Core_Controller_Varien_Action extends Mage_Core_Controller_Varien_Action {
	/**
	 * @param string|null $defaultUrl [optional]
	 * @return string
	 */
	public function getRefererUrl($defaultUrl = null) {
		$result = parent::_getRefererUrl();
		if (
				!is_null($defaultUrl)
			&&
				(Mage::app()->getStore()->getBaseUrl() === $result)
		) {
			$result = $defaultUrl;
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @return Df_Core_Controller_Varien_Action
	 */
	public static function s() {
		/** @var Df_Core_Controller_Varien_Action $result */
		static $result;
		if (!isset($result)) {
			/** @var Mage_Core_Controller_Varien_Action $action */
			$action = Mage::app()->getFrontController()->getDataUsingMethod('action');
			df_assert($action instanceof Mage_Core_Controller_Varien_Action);
			$result = new Df_Core_Controller_Varien_Action($action->getRequest(), $action->getResponse());
			Mage::app()->getFrontController()->setDataUsingMethod('action', $action);
		}
		return $result;
	}
}