<?php
namespace Df\IPay;
class Method extends \Df\Payment\Method\WithRedirect {
	/**
	 * @used-by \Df\IPay\Config\Area\Service::getUrlPaymentPage()
	 * @return string|null
	 */
	public function operator() {return $this->iia(self::OPERATOR);}

	/**
	 * @override
	 * @return array
	 */
	protected function getCustomInformationKeys() {return array_merge(
		[self::OPERATOR], parent::getCustomInformationKeys()
	);}

	/**
	 * @used-by getCustomInformationKeys()
	 * @used-by operator()
	 * @used-by app/design/frontend/rm/default/template/df/ipay/form.phtml
	 */
	const OPERATOR = 'df_ipay__operator';
	/**
	 * 2015-03-17
	 * Обратите внимание, что мы вправе создавать наш объект таким способом,
	 * ибо он схож со способом создания помков @see Mage_Payment_Model_Method_Abstract
	 * ядром Magento. @see Mage_Payment_Helper_Data::getMethodInstance():
			$key = self::XML_PATH_PAYMENT_METHODS.'/'.$code.'/model';
			$class = Mage::getStoreConfig($key);
			return Mage::getModel($class);
	 * @used-by \Df\IPay\Action::method()
	 * @param \Df_Core_Model_StoreM $store
	 * @return self
	 */
	public static function i(\Df_Core_Model_StoreM $store) {return new self(['store' => $store]);}
}