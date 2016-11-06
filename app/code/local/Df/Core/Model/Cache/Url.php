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
			$cacheKey = dfa_hash([$url, $routePath, $routeParams]);
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

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}