<?php
class Df_YandexMarket_Model_Categories extends Df_Core_Model {
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

	/** @return Df_YandexMarket_Model_Category_Tree */
	public function getTree() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_YandexMarket_Model_Category_Tree $result */
			$result = new Df_YandexMarket_Model_Category_Tree();
			foreach (Df_YandexMarket_Model_Category_Excel_Document::s()->getRows() as $row) {
				Df_YandexMarket_Model_Category_Excel_Processor_Row::i($result, $row)->process();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Cache::i(null, 30 * 86400);
		}
		return $this->{__METHOD__};
	}

	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_YandexMarket_Model_Categories
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return Df_YandexMarket_Model_Categories */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}