<?php
class Df_Core_Model_Setup_FirstRun extends Df_Core_Model_Abstract {
	/** @return Df_Core_Model_Setup_FirstRun */
	public function process() {
		$this->disableAndCleanCache();
		df_h()->index()->reindexEverything();
		return $this;
	}

	/** @return Df_Core_Model_Setup_FirstRun */
	private function disableAndCleanCache() {
		/** @var Mage_Core_Model_Mysql4_Cache $resource */
		$resource = Mage::getResourceSingleton('core/cache');
		/** @var array(string => int) $options */
		$options = $resource->getAllOptions();
		Mage::app()->getCacheInstance()
			->saveOptions(
				array_combine(
					array_keys($options)
					,array_fill(0, count($options), 0)
				)
			)
		;
		rm_cache_clean();
		return $this;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Setup_FirstRun
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}