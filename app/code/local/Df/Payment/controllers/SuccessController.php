<?php
/**
 * 2016-10-21
 * Этот контроллер обрабатывает возвращение покупателя со страницы платёжной системы
 * назад в интернет-магазин после успешной оплаты заказа.
 * Раньше я использовал просто checkout/onepage/success,
 * однако в многомагазинной системе это приводит проблеме:
 * 1) если указывать checkout/onepage/success без кода магазина — то страница не отобразится (404)
 * 2) если указывать checkout/onepage/success с кодом магазина — то это приводит к проблемам
 * с туповатыми платёжными системами типа ROBOKASSA,
 * где адрес для возвращения покупателя в магазин намертво задаётся администратором
 * в личном кабинете платёжной системы,
 * и у модуля Magento нет возможности формировать его динамически,
 * подставляя туда код нужного магазина.
 * 3) Если добавить <checkout/> внутрь <direct_front_name>,
 * то Magento во все адреса checkout не будет добавлять код магазина.
 *
 * Вот для решения этой проблемы и предназначен этот контроллер.
 */
class Df_Payment_SuccessController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		/** @var Df_Sales_Model_Order|null $order */
		$order = df_last_order(false);
		$this->_redirect('checkout/onepage/success', [
			/**
			 * 2016-10-21
			 * @used-by Mage_Core_Model_Url::setRouteParams()
			 * https://github.com/OpenMage/magento-mirror/blob/1.9.3.0/app/code/core/Mage/Core/Model/Url.php#L632-L635
			 */
			'_store' => $order ? $order->getStoreId() : df_store_id()
		]);
	}
}