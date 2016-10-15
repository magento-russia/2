<?php
/** @method Df_PageCache_Model_Resource_Crawler getResource() */
class Df_PageCache_Model_Crawler extends Df_Core_Model {
	/**
	 * Crawl all system urls
	 * @return Df_PageCache_Model_Crawler
	 */
	public function crawl() {
		try {
			/** @var array $storesInfo */
			$storesInfo = $this->getStoresInfo();
			$dfUrlCount = 0;
			$dfMaxUrlCount = 20;
			foreach ($storesInfo as $info) {
				$options	= array(CURLOPT_USERAGENT => self::USER_AGENT);
				$storeId	= $info['store_id'];
				if (!df_store($storeId)->getConfig(self::XML_PATH_CRAWLER_ENABLED)) {
					continue;
				}
				$threads = (int)df_store($storeId)->getConfig(self::XML_PATH_CRAWLER_THREADS);
				if (!$threads) {
					$threads = 1;
				}
				$stmt = $this->getResource()->getUrlStmt($storeId);
				$baseUrl = $info['base_url'];
				if (!empty($info['cookie'])) {
					$options[CURLOPT_COOKIE] = $info['cookie'];
				}
				$urls = array();
				$urlsCount = 0;
				$totalCount = 0;
				$this->request(array($baseUrl), $options);
				while (true) {
					$row = $stmt->fetch();
					if (!$row) {
						break;
					}
					$urls[]= df_cc($baseUrl, $this->encodeUrlPath($row['request_path']));
					$urlsCount++;
					$totalCount++;
					if ($urlsCount==$threads) {
						$this->request($urls, $options);
						$dfUrlCount += count($urls);
						if ($dfUrlCount > $dfMaxUrlCount) {
							//break;
						}

						$urlsCount = 0;
						$urls = array();
					}
				}
				if (!empty($urls)) {
					$this->request($urls, $options);
					$dfUrlCount += count($urls);
					if ($dfUrlCount > $dfMaxUrlCount) {
						//break;
					}
				}
			}
		}

		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_PageCache_Model_Resource_Crawler
	 */
	protected function _getResource() {return Df_PageCache_Model_Resource_Crawler::s();}

	/**
	 * Если в адресе присутствуют символы кириллицы, то кодируем их.
	 * Дело в том, что браузер будет кодировать символы кириллицы в любом случае.
	 * И, если бы мы из сейчас не кодировали,
	 * при загрузке той же самой страницы из браузера
	 * ключ кэша был бы иным, и от нашего формируемого сейчас кэша не было бы толку.
	 * @param string $path
	 * @return string
	 */
	private function encodeUrlPath($path) {
		df_param_string($path, 0);
		return df_cc_path(array_map('rawurlencode', df_explode_url($path)));
	}

	/** @return Varien_Http_Adapter_Curl */
	private function getCurl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Varien_Http_Adapter_Curl();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Get configuration for stores base urls.
	 * array(
	 *  $index => array(
	 *	  'store_id'  => $storeId,
	 *	  'base_url'  => $url,
	 *	  'cookie'	=> $cookie
	 *  )
	 * )
	 * @return array
	 */
	private function getStoresInfo() {
		$baseUrls = array();
		foreach (Mage::app()->getStores() as $store) {
			/** @var Df_Core_Model_StoreM $store */
			/** @var Mage_Core_Model_Website $website */
			$website = df_website($store->getWebsiteId());
			$defaultWebsiteStore = $website->getDefaultStore();
			$defaultWebsiteBaseUrl = $defaultWebsiteStore->getBaseUrl();
			$baseUrl = $store->getBaseUrl();
			$defaultCurrency = $store->getDefaultCurrencyCode();
			$cookie = '';
			if (($baseUrl == $defaultWebsiteBaseUrl) && ($defaultWebsiteStore->getId() != $store->getId())) {
				$cookie = 'store='.$store->getCode().';';
			}
			$baseUrls[]= array(
				'store_id' => $store->getId(),'base_url' => $baseUrl,'cookie'   => $cookie,);
			if ($store->getConfig(self::XML_PATH_CRAWL_MULTICURRENCY)
				&& $store->getConfig(Df_PageCache_Model_Processor::XML_PATH_CACHE_MULTICURRENCY)) {
				$currencies = $store->getAvailableCurrencyCodes(true);
				foreach ($currencies as $currencyCode) {
					if ($currencyCode != $defaultCurrency) {
						$baseUrls[]= array(
							'store_id' => $store->getId()
							,'base_url' => $baseUrl
							,'cookie' => $cookie.'currency='.$currencyCode.';'
						);
					}
				}
			}
		}
		return $baseUrls;
	}

	/**
	 * @param array $urls
	 * @param array $options
	 * @return Df_PageCache_Model_Crawler
	 */
	private function request(array $urls, array $options) {
		$this->getCurl()->multiRequest($urls, $options);
		return $this;
	}


	const XML_PATH_CRAWL_MULTICURRENCY = 'df_speed/page_cache/auto_crawling__multicurrency';
	const XML_PATH_CRAWLER_ENABLED	 = 'df_speed/page_cache/auto_crawling__enabled';
	const XML_PATH_CRAWLER_THREADS	 = 'df_speed/page_cache/auto_crawling__num_threads';
	const USER_AGENT = 'MagentoCrawler';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_PageCache_Model_Crawler
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_PageCache_Model_Crawler
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
}