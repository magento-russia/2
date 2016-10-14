<?php
class Df_IPay_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
	/** @return string|null */
	public function getMobileNetworkOperator() {
		return $this->getInfoInstance()->getAdditionalInformation(self::INFO_KEY__MOBILE_NETWORK_OPERATOR);
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getCustomInformationKeys() {
		return array_merge(
			array(self::INFO_KEY__MOBILE_NETWORK_OPERATOR), parent::getCustomInformationKeys()
		);
	}

	/**
	 * @self getCustomInformationKeys()
	 * @used-by getMobileNetworkOperator()
	 * @used-by app/design/frontend/rm/default/template/df/ipay/form.phtml
	 */
	const INFO_KEY__MOBILE_NETWORK_OPERATOR = 'df_ipay__mobile_network_operator';
	/**
	 * 2015-03-17
	 * Обратите внимание, что мы вправе создавать наш объект таким способом,
	 * ибо он схож со способом создания помков @see Mage_Payment_Model_Method_Abstract
	 * ядром Magento. @see Mage_Payment_Helper_Data::getMethodInstance():
			$key = self::XML_PATH_PAYMENT_METHODS.'/'.$code.'/model';
			$class = Mage::getStoreConfig($key);
			return Mage::getModel($class);
	 * @used-by Df_IPay_Model_Action_Abstract::method()
	 * @param Df_Core_Model_StoreM $store
	 * @return Df_IPay_Model_Payment
	 */
	public static function i(Df_Core_Model_StoreM $store) {return new self(array('store' => $store));}
}