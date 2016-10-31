<?php
namespace Df\YandexMarket;
class Categories extends \Df_Core_Model {
	/** @return string */
	public function getNodesAsText() {return df_cc_n($this->getNodesAsTextArray());}

	/** @return string[] */
	public function getNodesAsTextArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(__METHOD__);
			/** @var string[] $result */
			$result = $this->getCache()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				$result = $this->getTree()->getNodesAsTextArray();
				$this->getCache()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function isPathValid($path) {return in_array($path, $this->getNodesAsTextArray(), $path);}

	/** @return \Df\YandexMarket\Category\Tree */
	public function getTree() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\YandexMarket\Category\Tree $result */
			$result = new \Df\YandexMarket\Category\Tree;
			foreach (\Df\YandexMarket\Category\Excel\Document::s()->getRows() as $row) {
				\Df\YandexMarket\Category\Excel\Processor\Row::i($result, $row)->process();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return \Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df_Core_Model_Cache::i(null, 30 * 86400);
		}
		return $this->{__METHOD__};
	}

	
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return self
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}