<?php
class Df_Sales_Helper_Guest extends Mage_Sales_Helper_Guest {
	/**
	 * Цели перекрытия:
	 * 1) устранение сбоя
	 * «Fatal error: Call to a member function addCrumb() on a non-object»
	 * 2) использование для перевода начальной хлебной крошки словаря модуля Mage_Cms
	 * вместо словаря модуля Mage_Sales
	 *
	 * @param Mage_Core_Controller_Front_Action $controller
	 * @return void
	 */
	public function getBreadcrumbs($controller) {
		/** @var Mage_Page_Block_Html_Breadcrumbs|bool $breadcrumbs */
		$breadcrumbs = $controller->getLayout()->getBlock('breadcrumbs');
		/**
		 * Magento CE не делает эту проверку, и иногда происходит сбой:
		 * «Fatal error: Call to a member function addCrumb() on a non-object»
		 */
		if ($breadcrumbs) {
			$breadcrumbs->addCrumb('home', array(
				/**
				 * В отличие от родительского метода,
				 * используем для перевода расположенных ниже 2 строк
				 * словарь модуля Mage_Cms вместо словаря модуля Mage_Sales.
				 */
				'label' => df_mage()->cmsHelper()->__('Home')
				,'title' => df_mage()->cmsHelper()->__('Go to Home Page')
				,'link'  => Mage::getBaseUrl()
			));
			$breadcrumbs->addCrumb('cms_page', array(
				'label' => $this->__('Order Information')
				,'title' => $this->__('Order Information')
			));
		}
	}
}