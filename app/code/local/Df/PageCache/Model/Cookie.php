<?php
class Df_PageCache_Model_Cookie extends Mage_Core_Model_Cookie
{
	/**
	 * Cookie names
	 */
	const COOKIE_CUSTOMER           = 'CUSTOMER';
	const COOKIE_CUSTOMER_GROUP     = 'CUSTOMER_INFO';
	const COOKIE_CUSTOMER_RATES     = 'CUSTOMER_RATES';

	const COOKIE_MESSAGE            = 'NEWMESSAGE';
	const COOKIE_CART               = 'CART';
	const COOKIE_COMPARE_LIST       = 'COMPARE';
	const COOKIE_POLL               = 'POLL';
	const COOKIE_RECENTLY_COMPARED  = 'RECENTLYCOMPARED';
	const COOKIE_WISHLIST           = 'WISHLIST';
	const COOKIE_WISHLIST_ITEMS     = 'WISHLIST_CNT';

	const COOKIE_CUSTOMER_LOGGED_IN = 'CUSTOMER_AUTH';
	const PERSISTENT_COOKIE_NAME    = 'persistent_shopping_cart';

	const COOKIE_FORM_KEY           = 'CACHED_FRONT_FORM_KEY';

	/**
	 * Subprocessors cookie names
	 */
	const COOKIE_CATEGORY_PROCESSOR = 'CATEGORY_INFO';

	/**
	 * Cookie to store last visited category id
	 */
	const COOKIE_CATEGORY_ID = 'LAST_CATEGORY';

	/**
	 * Customer segment ids cookie name
	 */
	const CUSTOMER_SEGMENT_IDS = 'CUSTOMER_SEGMENT_IDS';

	/**
	 * Cookie name for users who allowed cookie save
	 */
	const IS_USER_ALLOWED_SAVE_COOKIE  = 'user_allowed_save_cookie';

	/**
	 * Encryption salt value
	 *
	 * @var string
	 */
	protected $_salt = null;

	/**
	 * Retrieve encryption salt
	 *
	 * @return null|string
	 */
	protected function _getSalt()
	{
		if ($this->_salt === null) {
			$saltCacheId = 'full_page_cache_key';
			$this->_salt = Df_PageCache_Model_Cache::getCacheInstance()->load($saltCacheId);
			if (!$this->_salt) {
				$this->_salt = md5(microtime() . rand());
				Df_PageCache_Model_Cache::getCacheInstance()->save($this->_salt, $saltCacheId,
					array(Df_PageCache_Model_Processor::CACHE_TAG));
			}
		}
		return $this->_salt;
	}

	/**
	 * Set cookie with obscure value
	 *
	 * @param string $name The cookie name
	 * @param string $value The cookie value
	 * @param int $period Lifetime period
	 * @param string $path
	 * @param string $domain
	 * @param int|bool $secure
	 * @param bool $httponly
	 * @return Mage_Core_Model_Cookie
	 */
	public function setObscure(
		$name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null
	) {
		$value = md5($this->_getSalt() . $value);
		return $this->set($name, $value, $period, $path, $domain, $secure, $httponly);
	}

	/**
	 * Keep customer cookies synchronized with customer session
	 *
	 * @return Df_PageCache_Model_Cookie
	 */
	public function updateCustomerCookies()
	{
		/** @var Mage_Customer_Model_Session $session */
		$session = df_session_customer();
		$customerId = $session->getCustomerId();
		$customerGroupId = $session->getCustomerGroupId();
		if (!$customerId || is_null($customerGroupId)) {
			$customerCookies = new Varien_Object();
			Mage::dispatchEvent('update_customer_cookies', array('customer_cookies' => $customerCookies));
			if (!$customerId) {
				$customerId = $customerCookies->getCustomerId();
			}
			if (is_null($customerGroupId)) {
				$customerGroupId = $customerCookies->getCustomerGroupId();
			}
		}
		if ($customerId && !is_null($customerGroupId)) {
			$this->setObscure(self::COOKIE_CUSTOMER, 'customer_' . $customerId);
			$this->setObscure(self::COOKIE_CUSTOMER_GROUP, 'customer_group_' . $customerGroupId);
			if ($session->isLoggedIn()) {
				$this->setObscure(self::COOKIE_CUSTOMER_LOGGED_IN, 'customer_logged_in_' . $session->isLoggedIn());
			} else {
				$this->delete(self::COOKIE_CUSTOMER_LOGGED_IN);
				$this->delete(self::COOKIE_CUSTOMER_RATES);
			}
		} else {
			$this->delete(self::COOKIE_CUSTOMER);
			$this->delete(self::COOKIE_CUSTOMER_GROUP);
			$this->delete(self::COOKIE_CUSTOMER_LOGGED_IN);
			$this->delete(self::COOKIE_CUSTOMER_RATES);
		}
		return $this;
	}

	/**
	 * Update customer rates cookie
	 */
	public function updateCustomerRatesCookie()
	{
		if (df_tax_c()->getPriceDisplayType() > 1) {
			/** @var $taxCalculationModel Mage_Tax_Model_Calculation */
			$taxCalculationModel = Mage::getSingleton('tax/calculation');
			$request = $taxCalculationModel->getRateRequest();
			$rates = $taxCalculationModel->getApplicableRateIds($request);
			sort($rates);
			$this->setObscure(self::COOKIE_CUSTOMER_RATES, 'customer_rates_' . implode(',', $rates));
		}
	}

	/**
	 * Register viewed product ids in cookie
	 *
	 * @param int|string|array $productIds
	 * @param int $countLimit
	 * @param bool $append
	 */
	public static function registerViewedProducts($productIds, $countLimit, $append = true)
	{
		if (!is_array($productIds)) {
			$productIds = array($productIds);
		}
		if ($append) {
			if (!empty($_COOKIE[Df_PageCache_Model_Container_Viewedproducts::COOKIE_NAME])) {
				$cookieIds = $_COOKIE[Df_PageCache_Model_Container_Viewedproducts::COOKIE_NAME];
				$cookieIds = explode(',', $cookieIds);
			} else {
				$cookieIds = array();
			}
			array_splice($cookieIds, 0, 0, $productIds);  // append to the beginning
		} else {
			$cookieIds = $productIds;
		}
		$cookieIds = array_unique($cookieIds);
		$cookieIds = array_slice($cookieIds, 0, $countLimit);
		$cookieIds = implode(',', $cookieIds);
		setcookie(Df_PageCache_Model_Container_Viewedproducts::COOKIE_NAME, $cookieIds, 0, '/');
	}

	/**
	 * Set catalog cookie
	 *
	 * @param string $value
	 */
	public static function setCategoryCookieValue($value)
	{
		setcookie(self::COOKIE_CATEGORY_PROCESSOR, $value, 0, '/');
	}

	/**
	 * Get catalog cookie
	 *
	 * @static
	 * @return bool
	 */
	public static function getCategoryCookieValue()
	{
		return (isset($_COOKIE[self::COOKIE_CATEGORY_PROCESSOR])) ? $_COOKIE[self::COOKIE_CATEGORY_PROCESSOR] : false;
	}

	/**
	 * Set cookie with visited category id
	 *
	 * @param int $id
	 */
	public static function setCategoryViewedCookieValue($id)
	{
		setcookie(self::COOKIE_CATEGORY_ID, $id, 0, '/');
	}

	/**
	 * Set cookie with form key for cached front
	 *
	 * @param string $formKey
	 */
	public static function setFormKeyCookieValue($formKey)
	{
		setcookie(self::COOKIE_FORM_KEY, $formKey, 0, '/');
	}

	/**
	 * Get form key cookie value
	 *
	 * @return string|bool
	 */
	public static function getFormKeyCookieValue()
	{
		return (isset($_COOKIE[self::COOKIE_FORM_KEY])) ? $_COOKIE[self::COOKIE_FORM_KEY] : false;
	}
}
