<?php
/**
 * @used-by Df_IPay_Model_Action_Abstract::order()
 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
 * @return string
 */
function df_current_domain($store = null) {
	/** @var string $baseUrl */
	$baseUrl = df_store($store)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
	return Zend_Uri_Http::fromString($baseUrl)->getHost();
}

/** @return string */
function df_current_url() {return df_mage()->core()->url()->getCurrentUrl();}

/** @return Df_Core_Helper_Url */
function df_url_h() {return Df_Core_Helper_Url::s();}

/**
 * Обратите внимание, что для формирования веб-адресов
 * мы не можем с целью ускорения использовать объект-одиночку класса @see Mage_Core_Model_Url,
 * потому что оба передаваемых для формирования веб-адреса параметра
 * влияют на объект @see Mage_Core_Model_Url:
 *
 * Второй параметр, $routeParams, влияет непосредственно в методе @see Mage_Core_Model_Url::getUrl().
 * Первый параметр, $routePath, влияет через вызов из @see Mage_Core_Model_Url::getUrl():
 * $url = $this->getRouteUrl($routePath, $routeParams);
 * @see Mage_Core_Model_Url::getRouteUrl
 *
 * Обратите внимание, что если в качестве $routeParams передать параметры запроса в виде
 * array('paramName' => 'paramValue'),
 * то в веб-адресе эти параметры будут содержаться через косую черту: paramName/paramValue.
 *
 * Если же в качестве $routeParams передать конструкцию вида
 * array('_query' => array('paramName' => 'paramValue')),
 * то в веб-адресе эти параметры будут содержаться стандартным для PHP способом:
 * ?paramName=paramValue.
 * Пример: @see Df_Payment_Model_Method_WithRedirect::getCustomerReturnUrl()
 *
 * В Magento в большинстве случаев оба варианта равноценны, но надо понимать разницу между ними!
 *
 * @param string $routePath
 * @param array(string => mixed) $routeParams [optional]
 * @return string
 */
function df_url($routePath, array $routeParams = array()) {
	/** @var Df_Core_Model_Url $url */
	$url = new Df_Core_Model_Url();
	return $url->getUrl($routePath, $routeParams);
}

/**
 * Обратите внимание, что для формирования веб-адресов
 * мы не можем с целью ускорения использовать объект-одиночку класса @see Mage_Adminhtml_Model_Url,
 * потому что оба передаваемых для формирования веб-адреса параметра
 * влияют на объект @see Mage_Adminhtml_Model_Url:
 *
 * Второй параметр, $routeParams,
 * влияет непосредственно в методе @see Mage_Adminhtml_Model_Url::getUrl().
 * Первый параметр, $routePath, влияет через вызов из @see Mage_Adminhtml_Model_Url::getUrl():
 * $url = $this->getRouteUrl($routePath, $routeParams);
 * @see Mage_Core_Model_Url::getRouteUrl
 *
 * Обратите внимание, что если в качестве $routeParams передать параметры запроса в виде
 * array('paramName' => 'paramValue'),
 * то в веб-адресе эти параметры будут содержаться через косую черту: paramName/paramValue.
 *
 * Если же в качестве $routeParams передать конструкцию вида
 * array('_query' => array('paramName' => 'paramValue')),
 * то в веб-адресе эти параметры будут содержаться стандартным для PHP способом:
 * ?paramName=paramValue.
 * Пример: @see Df_Payment_Model_Method_WithRedirect::getCustomerReturnUrl()
 *
 * В Magento в большинстве случаев оба варианта равноценны, но надо понимать разницу между ними!
 * @used-by rm_admin_button_location()
 * @param string $routePath
 * @param array(string => mixed) $routeParams [optional]
 * @return string
 */
function rm_url_admin($routePath, array $routeParams = array()) {
	/** @var Mage_Adminhtml_Model_Url $url */
	$url = Mage::getModel('adminhtml/url');
	return $url->getUrl($routePath, $routeParams);
}