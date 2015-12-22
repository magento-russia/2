<?php
class Df_PageCache_Model_Observer {
	/**
	 * Save page body to cache storage
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function cacheResponse(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$frontController = $observer->getEvent()->getFront();
		$request = $frontController->getRequest();
		$response = $frontController->getResponse();
		$this->_saveDesignException();
		$this->_checkAndSaveSslOffloaderHeaderToCache();
		$this->_processor->processRequestResponse($request, $response);
		return $this;
	}

	/**
	 * Check category state on post dispatch to allow category page be cached
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function checkCategoryState(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$category = Mage::registry('current_category');
		/**
		 * Categories with category event can't be cached
		 */
		if ($category && $category->getEvent()) {
			$request = $observer->getEvent()->getControllerAction()->getRequest();
			$request->setParam('no_cache', true);
		}
		return $this;
	}

	/**
	 * Check cross-domain session messages
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function checkMessages(Varien_Event_Observer $observer)
	{
		$transport = $observer->getEvent()->getTransport();
		if (!$transport || !$transport->getUrl()) {
			return $this;
		}
		$url = $transport->getUrl();
		$httpHost = Mage::app()->getFrontController()->getRequest()->getHttpHost();
		$urlHost = parse_url($url, PHP_URL_HOST);
		if ($httpHost != $urlHost && Mage::getSingleton('core/session')->getMessages()->count() > 0) {
			$transport->setUrl(Mage::helper('core/url')->addRequestParam(
				$url,
				array(Df_PageCache_Model_Cache::REQUEST_MESSAGE_GET_PARAM => null)
			));
		}
		return $this;
	}

	/**
	 * Check product state on post dispatch to allow product page be cached
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function checkProductState(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$product = Mage::registry('current_product');
		/**
		 * Categories with category event can't be cached
		 */
		if ($product && $product->getEvent()) {
			$request = $observer->getEvent()->getControllerAction()->getRequest();
			$request->setParam('no_cache', true);
		}
		return $this;
	}

	/**
	 * Clean full page cache
	 *
	 * @return Df_PageCache_Model_Observer
	 */
	public function cleanCache()
	{
		$this->_cacheInstance->clean(Df_PageCache_Model_Processor::CACHE_TAG);
		return $this;
	}

	/**
	 * Cleans cache by tags
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Mage_Core_Model_Observer
	 */
	public function cleanCacheByTags(Varien_Event_Observer $observer)
	{
		/** @var $tags array */
		$tags = $observer->getEvent()->getTags();
		if (empty($tags)) {
			$this->_cacheInstance->clean();
			return $this;
		}

		$this->_cacheInstance->clean($tags);
		return $this;
	}

	/**
	 * Clean cached tags for product if tags for product are saved
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function cleanCachedProductTagsForTags(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return;
		}

		/** @var $tagModel Mage_Tag_Model_Tag */
		$tagModel = $observer->getEvent()->getDataObject();
		$productCollection = $tagModel->getEntityCollection()
			->addTagFilter($tagModel->getId());

		/** @var $product Mage_Catalog_Model_Product */
		foreach ($productCollection as $product) {
			$this->_cacheInstance->clean($product->getCacheTags());
		}
	}

	/**
	 * Clean expired entities in full page cache
	 * @return Df_PageCache_Model_Observer
	 */
	public function cleanExpiredCache()
	{
		$this->_cacheInstance->getFrontend()->clean(Zend_Cache::CLEANING_MODE_OLD);
		return $this;
	}

	/**
	 * Clear request path cache by tag
	 * (used for redirects invalidation)
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */
	public function clearRequestCacheByTag(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$redirect = $observer->getEvent()->getRedirect();
		$this->_cacheInstance->clean(
			array(
				Df_PageCache_Helper_Url::prepareRequestPathTag($redirect->getData('identifier')),
				Df_PageCache_Helper_Url::prepareRequestPathTag($redirect->getData('target_path')),
				Df_PageCache_Helper_Url::prepareRequestPathTag($redirect->getOrigData('identifier')),
				Df_PageCache_Helper_Url::prepareRequestPathTag($redirect->getOrigData('target_path'))
			)
		);
		return $this;
	}

	/**
	 * Update customer rates cookie after address update
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function customerAddressUpdate(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$cookie = $this->_getCookie();
		$cookie->updateCustomerCookies();
		$cookie->updateCustomerRatesCookie();
		return $this;
	}

	/**
	 * Set cookie for logged in customer
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function customerLogin(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$cookie = $this->_getCookie();
		$cookie->updateCustomerCookies();
		$cookie->updateCustomerRatesCookie();
		$this->updateCustomerProductIndex();
		return $this;
	}

	/**
	 * Remove customer cookie
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function customerLogout(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$this->_getCookie()->updateCustomerCookies();

		if (!$this->_getCookie()->get(Df_PageCache_Model_Cookie::COOKIE_CUSTOMER)) {
			$this->_getCookie()->delete(Df_PageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED);
			$this->_getCookie()->delete(Df_PageCache_Model_Cookie::COOKIE_COMPARE_LIST);
			Df_PageCache_Model_Cookie::registerViewedProducts(array(), 0, false);
		}

		return $this;
	}

	/**
	 * Flush full page cache
	 *
	 * @return Df_PageCache_Model_Observer
	 */
	public function flushCache()
	{
		$this->_cacheInstance->flush();
		return $this;
	}

	/**
	 * Invalidate full page cache
	 * @return Df_PageCache_Model_Observer
	 */
	public function invalidateCache()
	{
		Mage::app()->getCacheInstance()->invalidateType('full_page');
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isCacheEnabled() {
		/** @var bool $result */
		static $result;
		if (!isset($result)) {
			$result =
					Mage::app()->useCache('full_page')
				&&
					df_enabled(Df_Core_Feature::FULL_PAGE_CACHING)
			;
		}
		return $result;
	}

	/**
	 * Process entity action
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function processEntityAction(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$object = $observer->getEvent()->getObject();
		Mage::getModel('df_pagecache/validator')->cleanEntityCache($object);
		return $this;
	}

	/**
	 * Remove new message cookie on clearing session messages.
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function processMessageClearing(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$this->_getCookie()->delete(Df_PageCache_Model_Cookie::COOKIE_MESSAGE);
		return $this;
	}

	/**
	 * Set new message cookie on adding messsage to session.
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function processNewMessage(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$this->_getCookie()->set(Df_PageCache_Model_Cookie::COOKIE_MESSAGE, '1');
		return $this;
	}

	/**
	 * Check when cache should be disabled
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function processPreDispatch(Varien_Event_Observer $observer)
	{
		if (Df_PageCache_Model_Processor::isDisabledByCurrentUri() || !$this->isCacheEnabled()) {
			return $this;
		}
		$action = $observer->getEvent()->getControllerAction();
		/* @var $request Mage_Core_Controller_Request_Http */
		$request = $action->getRequest();

		$noCache = $this->_getCookie()->get(Df_PageCache_Model_Processor::NO_CACHE_COOKIE);
		if ($noCache) {
			Mage::getSingleton('catalog/session')->setParamsMemorizeDisabled(false);
			$this->_getCookie()->renew(Df_PageCache_Model_Processor::NO_CACHE_COOKIE);
		} elseif ($action) {
			Mage::getSingleton('catalog/session')->setParamsMemorizeDisabled(true);
		}
		/**
		 * Check if request will be cached
		 */
		if ($this->_processor->canProcessRequest($request)) {
			Mage::app()->getCacheInstance()->banUse(Mage_Core_Block_Abstract::CACHE_GROUP);
		}
		$this->_getCookie()->updateCustomerCookies();
		return $this;
	}

	/**
	 * Register add wishlist item from cart in admin
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerAdminWishlistChange(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}

		$this->_cacheInstance->clean(
			$observer->getEvent()->getWishlist()->getCacheIdTags()
		);
	}

	/**
	 * Retrieve block tags and add it to processor
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerBlockTags(Varien_Event_Observer $observer)
	{
		$transport = $observer->getEvent()->getTransport();

		if (!$this->isCacheEnabled()) {
			return $this;
		}

		/** @var $block Mage_Core_Block_Abstract*/
		$block = $observer->getEvent()->getBlock();

		if (in_array($block->getType(), array_keys($this->_config->getDeclaredPlaceholders()))) {
			return $this;
		}

		/**
		 * 2015-05-07
		 * $transport — это объект-одиночка: @see Mage_Core_Block_Abstract::toHtml()
		 * Он перетрётся, если сейчас создать какой-нибудь другой блок.
		 * Вот @see Df_Cms_Block_Page::getCacheKeyInfo() создаёт новый блок...
		 * Чтобы $transport не перетёрся — сохраняем его значение.
		 */
		/** @var string $html */
		$html = $transport['html'];

		/** @var Df_Cms_Block_Page $block */
		$tags = $block->getCacheTags();

		$transport['html'] = $html;

		if (empty($tags)) {
			return $this;
		}

		$key = array_search(Mage_Core_Block_Abstract::CACHE_GROUP, $tags);
		if (false !== $key) {
			unset($tags[$key]);
		}
		if (empty($tags)) {
			return $this;
		}

		$this->_processor->addRequestTag($tags);

		return $this;
	}

	/**
	 * Register form key in session from cookie value
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function registerCachedFormKey(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return;
		}

		/** @var $session Mage_Core_Model_Session  */
		$session = Mage::getSingleton('core/session');
		$cachedFrontFormKey = Df_PageCache_Model_Cookie::getFormKeyCookieValue();
		if ($cachedFrontFormKey) {
			$session->setData('_form_key', $cachedFrontFormKey);
		}
	}

	/**
	 * Drop top navigation block from cache if category becomes visible/invisible
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function registerCategorySave(Varien_Event_Observer $observer)
	{
		/** @var $category Mage_Catalog_Model_Category */
		$category = $observer->getEvent()->getDataObject();

		if ($category->isObjectNew() ||
			($category->dataHasChangedFor('is_active') || $category->dataHasChangedFor('include_in_menu'))
		) {
			$this->_cacheInstance->clean(Mage_Catalog_Model_Category::CACHE_TAG);
		}
	}

	/**
	 * Set compare list in cookie on list change. Also modify recently compared cookie.
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerCompareListChange(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}

		$listItems = Mage::helper('catalog/product_compare')->getItemCollection();
		$previouseList = $this->_getCookie()->get(Df_PageCache_Model_Cookie::COOKIE_COMPARE_LIST);
		$previouseList = (empty($previouseList)) ? array() : explode(',', $previouseList);

		$ids = array();
		foreach ($listItems as $item) {
			$ids[] = $item->getId();
		}
		sort($ids);
		$this->_getCookie()->set(Df_PageCache_Model_Cookie::COOKIE_COMPARE_LIST, implode(',', $ids));

		//Recenlty compared products processing
		$recentlyComparedProducts = $this->_getCookie()
			->get(Df_PageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED);
		$recentlyComparedProducts = (empty($recentlyComparedProducts)) ? array()
			: explode(',', $recentlyComparedProducts);

		//Adding products deleted from compare list to "recently compared products"
		$deletedProducts = array_diff($previouseList, $ids);
		$recentlyComparedProducts = array_merge($recentlyComparedProducts, $deletedProducts);

		//Removing products from recently product list if it's present in compare list
		$addedProducts = array_diff($ids, $previouseList);
		$recentlyComparedProducts = array_diff($recentlyComparedProducts, $addedProducts);

		$recentlyComparedProducts = array_unique($recentlyComparedProducts);
		sort($recentlyComparedProducts);

		$this->_getCookie()->set(Df_PageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED,
			implode(',', $recentlyComparedProducts));

	   return $this;
	}

	/**
	 * Resave exception rules to cache storage
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerDesignExceptionsChange(Varien_Event_Observer $observer)
	{
		$this->_cacheInstance
			->remove(Df_PageCache_Model_Processor::DESIGN_EXCEPTION_KEY);
		return $this;
	}

	/**
	 * model_load_after event processor. Collect tags of all loaded entities
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerModelTag(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		/** @var $object Mage_Core_Model_Abstract */
		$object = $observer->getEvent()->getObject();
		if ($object && $object->getId()) {
			$tags = $object->getCacheIdTags();
			if ($tags) {
				$this->_processor->addRequestTag($tags);
			}
		}
		return $this;
	}

	/**
	 * Clean order sidebar cache
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerNewOrder(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}

		/** @var $blockContainer Df_PageCache_Model_Container_Orders */
		$blockContainer = Mage::getModel('df_pagecache/container_orders');
		$this->_cacheInstance->remove($blockContainer->getCacheId());
		return $this;
	}

	/**
	 * Set poll hash in cookie on poll vote
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerPollChange(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}

		$cookieValue = $observer->getEvent()->getPoll()->getId();
		$this->_getCookie()->set(Df_PageCache_Model_Cookie::COOKIE_POLL, $cookieValue);

		return $this;
	}

	/**
	 * Set cart hash in cookie on quote change
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerQuoteChange(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		/** @var Mage_Sales_Model_Quote */
		$quote = ($observer->getEvent()->getQuote()) ? $observer->getEvent()->getQuote() :
			$observer->getEvent()->getQuoteItem()->getQuote();
		$this->_getCookie()->setObscure(Df_PageCache_Model_Cookie::COOKIE_CART, 'quote_' . $quote->getId());

		$cacheId = Df_PageCache_Model_Container_Advanced_Quote::getCacheId();
		$this->_cacheInstance->remove($cacheId);

		return $this;
	}

	/**
	 * Clean cached tags for product on deleting review
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function registerReviewDelete(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return;
		}

		$review = $observer->getEvent()->getDataObject();
		$product = Mage::getModel('catalog/product')->load($review->getOrigData('entity_pk_value'));
		if ($product->getId() && $review->getOrigData('status_id') == Mage_Review_Model_Review::STATUS_APPROVED) {
			$this->_cacheInstance->clean($product->getCacheTags());
		}
	}

	/**
	 * Clean cached tags for product on saving review
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function registerReviewSave(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return;
		}

		$review = $observer->getEvent()->getDataObject();
		$product = Mage::getModel('catalog/product')->load($review->getEntityPkValue());
		if ($product->getId() && $this->_isChangedReviewVisibility($review)) {
			$this->_cacheInstance->clean($product->getCacheTags());
		}
	}

	/**
	 * Re-save exception rules to cache storage
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerSslOffloaderChange(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}

		$object = $observer->getEvent()->getDataObject();
		if ($object) {
			$this->_saveSslOffloaderHeaderToCache($object->getValue());
		}
		return $this;
	}

	/**
	 * Set wishlist hash in cookie on wishlist change
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerWishlistChange(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}

		$cookieValue = '';
		foreach (Mage::helper('wishlist')->getWishlistItemCollection() as $item) {
			$cookieValue .= ($cookieValue ? '_' : '') . $item->getId();
		}

		// Wishlist sidebar hash
		$this->_getCookie()->setObscure(Df_PageCache_Model_Cookie::COOKIE_WISHLIST, $cookieValue);

		// Wishlist items count hash for top link
		$this->_getCookie()->setObscure(Df_PageCache_Model_Cookie::COOKIE_WISHLIST_ITEMS,
			'wishlist_item_count_' . Mage::helper('wishlist')->getItemCount());

		$this->_cacheInstance->clean(
			Mage::helper('wishlist')->getWishlist()->getCacheIdTags()
		);

		return $this;
	}

	/**
	 * Clear wishlist list
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function registerWishlistListChange(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}

		$blockContainer = Mage::getModel('df_pagecache/container_wishlists');
		$this->_cacheInstance->remove($blockContainer->getCacheId());

		return $this;
	}

	/**
	 * Render placeholder tags around the block if needed
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function renderBlockPlaceholder(Varien_Event_Observer $observer)
	{
		if (!$this->_isEnabled) {
			return $this;
		}
		$block = $observer->getEvent()->getBlock();
		$transport = $observer->getEvent()->getTransport();

		$placeholder = $this->_config->getBlockPlaceholder($block);

		if ($transport && $placeholder && !$block->getSkipRenderTag()) {
			$blockHtml = $transport->getHtml();

			$request = Mage::app()->getFrontController()->getRequest();
			/** @var $processor Df_PageCache_Model_Processor_Default */
			$processor = $this->_processor->getRequestProcessor($request);
			if ($processor && $processor->allowCache($request)) {
				$container = $placeholder->getContainerClass();
				if ($container && !Mage::getIsDeveloperMode()) {
					$container = new $container($placeholder);
					$container->setProcessor(Mage::getSingleton('df_pagecache/processor'));
					$container->setPlaceholderBlock($block);
					$container->saveCache($blockHtml);
				}
			}

			$blockHtml = $placeholder->getStartTag() . $blockHtml . $placeholder->getEndTag();
			$transport->setHtml($blockHtml);
		}
		return $this;
	}

	/**
	 * Update customer viewed products index and renew customer viewed product ids cookie
	 *
	 * @return Df_PageCache_Model_Observer
	 */
	public function updateCustomerProductIndex()
	{
		try {
			$productIds = $this->_getCookie()->get(Df_PageCache_Model_Container_Viewedproducts::COOKIE_NAME);
			if ($productIds) {
				$productIds = explode(',', $productIds);
				Mage::getModel('reports/product_index_viewed')->registerIds($productIds);
			}
		} catch (Exception $e) {
			Mage::logException($e);
		}

		// renew customer viewed product ids cookie
		$countLimit = Mage::getStoreConfig(Mage_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT);
		$collection = Mage::getResourceModel('reports/product_index_viewed_collection')
			->addIndexFilter()
			->setAddedAtOrder()
			->setPageSize($countLimit)
			->setCurPage(1);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($collection);
		$productIds = $collection->load()->getLoadedIds();
		$productIds = implode(',', $productIds);
		$this->_getCookie()->registerViewedProducts($productIds, $countLimit, false);
		return $this;
	}

	/**
	 * Update info about product on product page
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function updateProductInfo(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}

		$paramsObject = $observer->getEvent()->getParams();
		if ($paramsObject instanceof Varien_Object) {
			if (array_key_exists(Df_PageCache_Model_Cookie::COOKIE_CATEGORY_ID, $_COOKIE)) {
				$paramsObject->setCategoryId($_COOKIE[Df_PageCache_Model_Cookie::COOKIE_CATEGORY_ID]);
			}
		}
		return $this;
	}

	/**
	 * Check if data changes duering object save affect cached pages
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function validateDataChanges(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$object = $observer->getEvent()->getObject();
		Mage::getModel('df_pagecache/validator')->checkDataChange($object);
		return $this;
	}

	/**
	 * Check if data delete affect cached pages
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Df_PageCache_Model_Observer
	 */
	public function validateDataDelete(Varien_Event_Observer $observer)
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$object = $observer->getEvent()->getObject();
		Mage::getModel('df_pagecache/validator')->checkDataDelete($object);
		return $this;
	}

	/**
	 * Saves 'web/secure/offloader_header' config to cache, only when value was updated
	 *
	 * @return Df_PageCache_Model_Observer
	 */
	protected function _checkAndSaveSslOffloaderHeaderToCache()
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}
		$sslOffloaderHeader = trim((string) Mage::getConfig()->getNode(
			/**
			 * 2015-12-22
			 * В Magento CE 1.6.0.0
			 * константа @see Mage_Core_Model_Store::XML_PATH_OFFLOADER_HEADER отсутствует.
			 */
			'web/secure/offloader_header', 'default'
		));

		$cachedSslOffloaderHeader = $this->_cacheInstance
			->load(Df_PageCache_Model_Processor::SSL_OFFLOADER_HEADER_KEY);
		$cachedSslOffloaderHeader = trim(@unserialize($cachedSslOffloaderHeader));

		if ($cachedSslOffloaderHeader != $sslOffloaderHeader) {
			$this->_saveSslOffloaderHeaderToCache($sslOffloaderHeader);
		}
		return $this;
	}

	/**
	 * Retrieve cookie instance
	 *
	 * @return Df_PageCache_Model_Cookie
	 */
	protected function _getCookie()
	{
		return Mage::getSingleton('df_pagecache/cookie');
	}

	/**
	 * Check is review visibility was changed
	 *
	 * @param Mage_Review_Model_Review $review
	 * @return bool
	 */
	protected function _isChangedReviewVisibility($review)
	{
		return $review->getData('status_id') == Mage_Review_Model_Review::STATUS_APPROVED
			|| ($review->getData('status_id') != Mage_Review_Model_Review::STATUS_APPROVED
			&& $review->getOrigData('status_id') == Mage_Review_Model_Review::STATUS_APPROVED);
	}

	/**
	 * @return array
	 */
	protected function _loadDesignExceptions()
	{
		$exceptions = $this->_cacheInstance
			->load(Df_PageCache_Model_Processor::DESIGN_EXCEPTION_KEY)
		;
		$exceptions = @unserialize($exceptions);
		return is_array($exceptions) ? $exceptions : array();
	}

	/**
	 * Checks whether exists design exception value in cache.
	 * If not, gets it from config and puts into cache
	 *
	 * @return Df_PageCache_Model_Observer
	 */
	protected function _saveDesignException()
	{
		if (!$this->isCacheEnabled()) {
			return $this;
		}

		if (isset($_COOKIE[Mage_Core_Model_Store::COOKIE_NAME])) {
			$storeIdentifier = $_COOKIE[Mage_Core_Model_Store::COOKIE_NAME];
		} else {
			$storeIdentifier = Mage::app()->getRequest()->getHttpHost() . Mage::app()->getRequest()->getBaseUrl();
		}
		$exceptions = $this->_loadDesignExceptions();
		if (!isset($exceptions[$storeIdentifier])) {
			$exceptions[$storeIdentifier][self::XML_PATH_DESIGN_EXCEPTION] = Mage::getStoreConfig(
				self::XML_PATH_DESIGN_EXCEPTION
			);
			foreach ($this->_themeExceptionTypes as $type) {
				$configPath = sprintf('design/theme/%s_ua_regexp', $type);
				$exceptions[$storeIdentifier][$configPath] = Mage::getStoreConfig($configPath);
			}
			$this->_saveDesignExceptions($exceptions);
			$this->_processor->refreshRequestIds();
		}
		return $this;
	}

	/**
	 * @param array $exceptions
	 * @return Df_PageCache_Model_Observer
	 */
	protected function _saveDesignExceptions(array $exceptions)
	{
		$this->_cacheInstance->save(
			serialize($exceptions),
			Df_PageCache_Model_Processor::DESIGN_EXCEPTION_KEY,
			array(Df_PageCache_Model_Processor::CACHE_TAG)
		);
		return $this;
	}

	/**
	 * Save 'web/secure/offloader_header' config to cache
	 *
	 * @param $value
	 */
	protected function _saveSslOffloaderHeaderToCache($value)
	{
		$this->_cacheInstance->save(
			serialize($value),
			Df_PageCache_Model_Processor::SSL_OFFLOADER_HEADER_KEY,
			array(Df_PageCache_Model_Processor::CACHE_TAG)
		);
	}
	/**
	 * Cache instance
	 *
	 * @var Mage_Core_Model_Cache
	 */
	protected $_cacheInstance;
	/**
	 * Page Cache Config
	 *
	 * @var Df_PageCache_Model_Config
	 */
	protected $_config;
	/**
	 * Is Enabled Full Page Cache
	 *
	 * @var bool
	 */
	protected $_isEnabled;
	/**
	 * Page Cache Processor
	 *
	 * @var Df_PageCache_Model_Processor
	 */
	protected $_processor;
	protected $_themeExceptionTypes = array(
		'template',
		'skin',
		'layout',
		'default'
	);
	const XML_PATH_DESIGN_EXCEPTION = 'design/package/ua_regexp';

	/**
	 * Class constructor
	 */
	public function __construct(array $args = array())
	{
		$this->_processor = isset($args['processor'])
			? $args['processor']
			: Mage::getSingleton('df_pagecache/processor');
		$this->_config = isset($args['config']) ? $args['config'] : Mage::getSingleton('df_pagecache/config');
		$this->_isEnabled = isset($args['enabled']) ? $args['enabled'] : Mage::app()->useCache('full_page');
		$this->_cacheInstance = isset($args['cacheInstance'])
			? $args['cacheInstance']
			: Df_PageCache_Model_Cache::getCacheInstance();
	}
}
