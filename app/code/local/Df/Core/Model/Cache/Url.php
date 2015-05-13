<?php
class Df_Core_Model_Cache_Url extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param Df_Core_Model_Url $url
	 * @param string|null $routePath
	 * @param array|null $routeParams
	 * @return string
	 */
	public function getUrl(Df_Core_Model_Url $url, $routePath = null, $routeParams = null) {
		/** @var string $result */
		if (!$this->isCacheEnabled()) {
			$result = $url->getUrlParent($routePath, $routeParams);
		}
		else {
			/** @var string $cacheKey */
			$cacheKey = implode('::', $this->getCacheKeyParams2($url, $routePath, $routeParams));
			if (isset($this->_urlCache[$cacheKey])) {
				$result = $this->_urlCache[$cacheKey];
			}
			else {
				$result = $url->getUrlParent($routePath, $routeParams);
				$this->_urlCache[$cacheKey] = $result;
				$this->markCachedPropertyAsModified('_urlCache');
			}
		}
		return $result;
	}
	/** @var array(string => string) */
	protected $_urlCache;

	/**
	 * @override
	 * @return string[]
	 */
	protected function getCacheTagsRm() {return array('rm_url');}

	/**
	 * @override
	 * @return string
	 */
	protected function getCacheTypeRm() {return 'rm_url';}

	/** @return string[] */
	protected function getPropertiesToCachePerStore() {return array('_urlCache');}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return array('_urlCache');}

	/**
	 * Обратите внимание, что метод @see Df_Core_Model_Abstract::getCacheKeyParams()
	 * присутствует в классе-предке @see Df_Core_Model_Abstract,
	 * поэтому называем метод getCacheKeyParams2().
	 *
	 * Обратите также внимание, что если всё-таки назвать данный метод getCacheKeyParams(),
	 * то PHP 5.5 почему-то не выдаёт никаких предупреждений
	 * (может, потому что области видимости разные, и современные версии PHP это учитывают?),
	 * а вот PHP 5.3.19-1~dotdeb.0 вполне ожидаемо выдаёт предупреждение:
	 * «Strict Notice: Declaration of Df_Core_Model_Cache_Url::getCacheKeyParams()
	 * should be compatible with that of Df_Core_Model_Abstract::getCacheKeyParams()»
	 *
	 * @param Df_Core_Model_Url $url
	 * @param string|null $routePath
	 * @param array|null $routeParams
	 * @return string[]
	 */
	private function getCacheKeyParams2(Df_Core_Model_Url $url, $routePath = null, $routeParams = null) {
		/** @var string[] $result */
		$result = array();
		$result[]= is_null($routePath) ? 'empty' : $routePath;
		/**
		 * Звёздочка может интерпретироваться для каждой страницы индивидуально,
		 * и поэтому в идентификатор кэша надо включить какой-нибудь идентификатор страницы,
		 * например, её веб-адрес.
		 */
		if (rm_contains($routePath, '*')) {
			$result []= Mage::app()->getRequest()->getRequestUri();
		}
		if (!is_null($routeParams)) {
			/**
			 * @see json_encode() работает быстрее, чем @see serialize()
			 * @link http://stackoverflow.com/a/7723730/254475
			 * @link http://stackoverflow.com/a/804053/254475
			 *
			 * Zend_Json::encode использует json_encode при наличии расширения PHP JSON
			 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
			 * @see Zend_Json::encode
			 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
			 * Обратите внимание,
			 * что расширение PHP JSON не входит в системные требования Magento.
			 * @link http://www.magentocommerce.com/system-requirements
			 * Поэтому использование Zend_Json::encode выглядит более правильным, чем json_encode.
			 *
			 * $cacheKey .= Zend_Json::encode($routeParams);
			 *
			 * P.S. Оно, конечно, правильнее, но @see json_encode() работает заметно быстрее,
			 * чем обёртка @see Zend_Json::encode()
			 */
			$result[]= json_encode($routeParams);
		}
		if ($url->getStore()) {
			$result[]= $url->getStore()->getCode();
		}
		$result[]= intval($url->getUseSession());
		if ($url->getQueryParams()) {
			$result[]= json_encode($url->getQueryParams());
		}
		return $result;
	}

	/** @return Df_Core_Model_Cache_Url */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}