<?php
/**
 * 2015-03-10
 * Параметр $class:
 * 1) может быть полным именем класса («Df_1C_Cml2_Action_Front»)
 * 2) может быть суффиксом класса («CustomerReturn»), в таком система ищет класс по стандартному пути:
 * <имя модуля>_Model_Action_<суффикс>
 * 3) может отсутствовать, тогда суффикс определяется по классу контроллера:
 * «Df_Alfabank_CustomerReturnController» => «CustomerReturn»
 * @used-by Df_1C_Cml2Controller::indexAction()
 * @used-by Df_Alfabank_CustomerReturnController::indexAction()
 * @used-by Df_Assist_ConfirmController::indexAction()
 * @used-by Df_EasyPay_ConfirmController::indexAction()
 * @used-by Df_Interkassa_ConfirmController::indexAction()
 * @used-by Df_IPay_ConfirmController::indexAction()
 * @used-by Df_IPay_ConfirmPaymentByShopController::indexAction()
 * @used-by Df_Kkb_ConfirmController::indexAction()
 * @used-by Df_LiqPay_ConfirmController::indexAction()
 * @used-by Df_Moneta_ConfirmController::indexAction()
 * @used-by Df_OnPay_ConfirmController::indexAction()
 * @used-by Df_PayOnline_ConfirmController::indexAction()
 * @used-by Df_Psbank_ConfirmController::indexAction()
 * @used-by Df_Qiwi_ConfirmController::indexAction()
 * @used-by Df_RbkMoney_ConfirmController::indexAction()
 * @used-by Df_Robokassa_ConfirmController::indexAction()
 * @used-by Df_Uniteller_ConfirmController::indexAction()
 * @used-by Df_WalletOne_ConfirmController::indexAction()
 * @used-by Df_WebMoney_ConfirmController::indexAction()
 * @used-by Df_WebPay_ConfirmController::indexAction()
 * @used-by Df_YandexMarket_YmlController::indexAction()
 * @used-by Df_YandexMarket_AddressController::indexAction()
 * @used-by Df_YandexMarket_CategoryController::suggestAction()
 * @used-by Df_YandexMoney_CustomerReturnController::indexAction()
 * @used-by Lamoda_Parser_Frontend_ImportController::categoriesAction()
 * @used-by Lamoda_Parser_Frontend_ImportController::shoesAction()
 * @used-by Utkonos_Parser_Frontend_IndexController::indexAction()
 * @param Mage_Core_Controller_Varien_Action $controller
 * @param string|null $suffix [optional]
 * @return void
 */
function df_action(Mage_Core_Controller_Varien_Action $controller, $suffix = null) {
	/** @var bool $full */
	$full = $suffix && df_class_my($suffix);
	// «Df_Alfabank_CustomerReturnController» => «CustomerReturn»
	$suffix = $suffix ?: df_trim_text_right(df_class_last($controller), 'Controller');
	/** @var string $class */
	$class = $full ? $suffix
		// 2016-11-01
		// Нельзя использовать здесь df_con($controller, ['Action', $suffix]),
		// потому что тогда df_con будет использовать разделитель $controller,
		// а у $controller разделитель «_», а не «/».
		: df_cc('\\', df_module_name($controller, '\\'), 'Action', $suffix);
	Df_Core_Model_Action::pc($class, $controller);
}