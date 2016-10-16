<?php
use Mage_Core_Model_Store as Store;
/**
 * @param array(string => mixed) $params [optional]
 * @return array(string => mixed)
 */
function df_adjust_route_params(array $params = []) {return ['_nosid' => true] + $params;}

/**
 * 2016-07-12
 * @param string $url
 * @param string|Exception $message [optional]
 * @return void
 * @throws Exception
 */
function df_assert_https($url, $message = null) {
	if (df_enable_assertions() && !df_check_https($url)) {
		df_error($message ? $message : df_sprintf(
			'The URL «%s» is invalid, because the system expects an URL which starts with «https://».'
			, $url
		));
	}
}

/**
 * 2016-05-30
 * http://framework.zend.com/manual/1.12/en/zend.uri.chapter.html#zend.uri.instance-methods.getscheme
 * @uses \Zend_Uri::getScheme() always returns a lowercased value:
 * @see \Zend_Uri::factory()
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Uri.php#L100
 * $scheme = strtolower($uri[0]);
 * @param string $url
 * @return bool
 */
function df_check_https($url) {return 'https' === df_zuri($url)->getScheme();}

/**
 * @used-by Df_Dataflow_Model_Importer_Product_Images::getImages()
 * http://stackoverflow.com/a/15011528
 * http://www.php.net/manual/en/function.filter-var.php
 * Обратите внимание, что
 * filter_var('/C/A/CA559AWLE574_1.jpg', FILTER_VALIDATE_URL) вернёт false
 * @param $s $string
 * @return bool
 */
function df_check_url($s) {return false !== filter_var($s, FILTER_VALIDATE_URL);}

/**
 * @param int|string|null|bool|Store $store [optional]
 * @return string
 */
function df_current_domain($store = null) {return dfcf(function($store = null) {
	/** @var Zend_View_Helper_ServerUrl $helper */
	$helper = new Zend_View_Helper_ServerUrl();
	/** @var string|null $result */
	$result = $helper->getHost();
	if (!$result) {
		// Magento запущена с командной строки (например, планировщиком задач)
		/** @var string|null $baseUrl */
		$baseUrl = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);
		/**
		 * Тут уже нам некуда деваться:
		 * пусть уж администратор указывает базовый адрес в настройках.
		 */
		/** @var string $errorMessage */
		$errorMessage = 'Укажите полный корневой адрес магазина в административных настройках';
		df_assert($baseUrl, $errorMessage);
		try {
			/** @var Zend_Uri_Http $uri */
			$uri = Zend_Uri::factory($baseUrl);
			$result = $uri->getHost();
			df_assert_string_not_empty($result);
		}
		catch (Exception $e) {
			df_error($errorMessage);
		}
	}
	df_result_string_not_empty($result);
	return $result;
}, func_get_args());}

/** @return string */
function df_current_url() {return df_url_h()->getCurrentUrl();}

/**
 * @used-by Df_Core_Model_Design_PackageM::getSkinUrl()
 * @used-by Df_Page_Model_Html_Head::addVersionStamp()
 * @param string $url
 * @param string|null $version [optional]
 * @return string
 */
function df_url_add_version_stamp($url, $version = null) {return
	$url . '?v=' . ($version ? $version : df_version())
;}

/** @return Mage_Core_Helper_Url */
function df_url_h() {return Mage::helper('core/url');}

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
 * 2015-11-28
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url($path = null, array $params = []) {return
	df_url_o()->getUrl($path, df_adjust_route_params($params))
;}

/**
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
 * @used-by df_admin_button_location()
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url_backend($path = null, array $params = []) {return
	df_url_backend_o()->getUrl($path, df_adjust_route_params($params))
;}

/**
 * 2016-08-24
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url_backend_ns($path = null, array $params = []) {return
	df_url_backend($path, ['_nosecret' => true] + $params)
;}

/**
 * 2016-10-15
 * Ниже — старый комментарий. Не знаю, правилен ли он:
 *
 * «Обратите внимание, что для формирования веб-адресов
 * мы не можем с целью ускорения использовать объект-одиночку класса @see Mage_Adminhtml_Model_Url,
 * потому что оба передаваемых для формирования веб-адреса параметра
 * влияют на объект @see Mage_Adminhtml_Model_Url:
 *
 * Второй параметр, $routeParams,
 * влияет непосредственно в методе @see Mage_Adminhtml_Model_Url::getUrl().
 * Первый параметр, $routePath, влияет через вызов из @see Mage_Adminhtml_Model_Url::getUrl():
 * $url = $this->getRouteUrl($routePath, $routeParams);»
 *
 * @see Mage_Core_Model_Url::getRouteUrl
 * @return Mage_Adminhtml_Model_Url
 */
function df_url_backend_o() {return Mage::getModel('adminhtml/url');}

/**
 * @used-by Df_Downloadable_Model_Url::getUrlByPath()
 * @param string $path
 * @return string
 */
function df_url_from_path($path) {return
	df_cc_path(array_map('rawurlencode', explode('/', df_path_relative($path))))
;}

/**
 * 2015-11-28
 * @param string|null $path [optional]
 * @param array(string => mixed) $params [optional]
 * @return string
 */
function df_url_frontend($path = null, array $params = []) {return
	df_url_frontend_o()->getUrl($path, df_adjust_route_params($params))
;}

/** @return Df_Core_Model_Url */
function df_url_frontend_o() {return new Df_Core_Model_Url;}

/** @return Df_Core_Model_Url|Mage_Adminhtml_Model_Url */
function df_url_o() {return df_is_admin() ? df_url_backend_o() : df_url_frontend_o();}

/**
 * 2016-05-31
 * @param string $url
 * @return string
 */
function df_url_strip_path($url) {
	/** @var \Zend_Uri_Http $z */
	$z = df_zuri($url);
	/** @var string $port */
	$port = $z->getPort();
	if ('80' === $port) {
		$port = '';
	}
	if ($port) {
		$port = ':' . $port;
	}
	return $z->getScheme() . '://' . $z->getHost() . $port;
}

/**
 * 2016-05-30
 * @param string $uri
 * @param bool $throw [optional]
 * @return \Zend_Uri|\Zend_Uri_Http
 * @throws \Zend_Uri_Exception
 */
function df_zuri($uri, $throw = true) {
	try {
		/** @var \Zend_Uri_Http $result */
		$result = \Zend_Uri::factory($uri);
	}
	catch (\Zend_Uri_Exception $e) {
		if ($throw) {
			throw $e;
		}
		$result = null;
	}
	return $result;
}