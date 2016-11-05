<?php
class Df_Core_Model_Cache_Url extends Df_Core_Model {
	/**
	 * @param Df_Core_Model_Url $url
	 * @param string|null $routePath [optional]
	 * @param array(string => mixed)|null $routeParams [optional]
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
	 * @used-by Df_Core_Model::cacheSaveProperty()
	 * @override
	 * @return string[]
	 */
	protected function cacheTags() {return array('rm_url');}

	/**
	 * @override
	 * @return string
	 */
	protected function cacheType() {return 'rm_url';}

	/**
	 * @override
	 * @see Df_Core_Model::cached()
	 * @return string[]
	 */
	protected function cached() {return array('_urlCache');}

	/**
	 * Обратите внимание, что метод @see Df_Core_Model::getCacheKeyParams()
	 * присутствует в классе-предке @see Df_Core_Model,
	 * поэтому называем метод @see getCacheKeyParams2().
	 *
	 * Обратите также внимание, что если всё-таки назвать данный метод @see getCacheKeyParams(),
	 * то PHP 5.5 почему-то не выдаёт никаких предупреждений
	 * (может, потому что области видимости разные, и современные версии PHP это учитывают?),
	 * а вот PHP 5.3.19-1~dotdeb.0 вполне ожидаемо выдаёт предупреждение:
	 * «Strict Notice: Declaration of Df_Core_Model_Cache_Url::getCacheKeyParams()
	 * should be compatible with that of Df_Core_Model::getCacheKeyParams()»
	 *
	 * @param Df_Core_Model_Url $url
	 * @param string|null $routePath [optional]
	 * @param array(string => mixed)|null $routeParams [optional]
	 * @return string[]
	 */
	private function getCacheKeyParams2(Df_Core_Model_Url $url, $routePath = null, $routeParams = null) {
		/** @var string[] $result */
		$result = [];
		$result[]= is_null($routePath) ? 'empty' : $routePath;
		/**
		 * Звёздочка может интерпретироваться для каждой страницы индивидуально,
		 * и поэтому в идентификатор кэша надо включить какой-нибудь идентификатор страницы,
		 * например, её веб-адрес.
		 */
		if (df_contains($routePath, '*')) {
			$result[]= df_ruri();
		}
		if (!is_null($routeParams)) {
			$result[]= json_encode($routeParams);
		}
		if ($url->getStore()) {
			$result[]= $url->getStore()->getCode();
		}
		$result[]= (int)$url->getUseSession();
		if ($url->getQueryParams()) {
			$result[]= json_encode($url->getQueryParams());
		}
		return $result;
	}

	/** @return Df_Core_Model_Cache_Url */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}