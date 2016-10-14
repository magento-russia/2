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
 * @param string|null $class [optional]
 * @return void
 */
function rm_action(Mage_Core_Controller_Varien_Action $controller, $class = null) {
	/** @var string $m */
	$m = rm_module_name($controller);
	/** @var bool $full */
	$full = $class && rm_starts_with($class, $m);
	if (!$class) {
		// «Df_Alfabank_CustomerReturnController» => «CustomerReturn»
		$class = df_trim_text_right(rm_last(rm_explode_class($controller)), 'Controller');
	}
	Df_Core_Model_Action::pc($full ? $class : rm_concat_class($m, 'Model_Action', $class), $controller);
}

/**
 * @used-by Df_Adminhtml_Block_Widget_Form_Container::getBlockClassSuffix()
 * @used-by Df_Payment_Model_Response::getIdInPaymentInfo()
 * @param string $className
 * @return string
 */
function rm_class_mf($className) {
	return Df_Core_Model_Reflection::s()->getModelNameInMagentoFormat($className);
}

/**
 * @param string|string[] $parts
 * @return string
 */
function rm_concat_class($parts) {
	/** @uses func_get_args() не может быть параметром другой функции */
	$parts = is_array($parts) ? $parts : func_get_args();
	return implode('_', $parts);
}

/**
 * Возвращает или Df_<имя конечного модуля>_<окончание класса>,
 * если данный класс присутствует, или $defaultResult, если отсутствует.
 * @param Varien_Object|object $caller
 * @param string $classSuffix
 * @param string|null $defaultResult [optional]
 * @param bool $throwOnError [optional]
 * @return string|null
 */
function rm_convention(Varien_Object $caller, $classSuffix, $defaultResult = null, $throwOnError = true) {
	return Df_Core_Model_Convention::s()->getClass($caller, $classSuffix, $defaultResult, $throwOnError);
}

/**
 * @used-by rm_explode_class()
 * @used-by rm_module_name()
 * @param string|object $class
 * @return string
 */
function rm_cts($class) {return is_object($class) ? get_class($class) : $class;}

/**
 * @param string|object $class
 * @return string[]
 */
function rm_explode_class($class) {return explode('_', rm_cts($class));}

/**
 * «Df_YandexMarket_Model_Yml_Document» => «yandex.market»
 * «Df_1C_Cml2_Export_Document_Catalog» => «1c»
 * @param Mage_Core_Model_Abstract $model
 * @param string $separator
 * @param int $offsetLeft [optional]
 * @return string
 */
function rm_model_id(Mage_Core_Model_Abstract $model, $separator, $offsetLeft = 0) {
	return Df_Core_Model_Reflection::s()->getModelId($model, $separator, $offsetLeft);
}

/**
 * «Df_YandexMarket_Model_Yml_Document» => «yandex.market»
 * «Df_1C_Cml2_Export_Document_Catalog» => «1c»
 * @param Varien_Object $object
 * @param string $separator
 * @return string
 */
function rm_module_id(Varien_Object $object, $separator) {
	/** @var string $className */
	$className = get_class($object);
	/** @var string $key */
	$key = $className . $separator;
	/** @var array(string => string) */
	static $cache;
	if (!isset($cache[$key])) {
		// «yandex.market»
		$cache[$key] = mb_strtolower(
			// «Yandex.Market»
			implode($separator, df_explode_camel(
				// «YandexMarket»
				df_a(rm_explode_class($className), 1)
			)
		));
	}
	return $cache[$key];
}

/**
 * «Df_SalesRule_Model_Event_Validator_Process» => «Df_SalesRule»
 * @param Varien_Object|string $object
 * @return string
 */
function rm_module_name($object) {return Df_Core_Model_Reflection::s()->getModuleName(rm_cts($object));}

