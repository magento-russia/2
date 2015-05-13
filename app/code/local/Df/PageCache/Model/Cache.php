<?php
class Df_PageCache_Model_Cache
{
	const REQUEST_MESSAGE_GET_PARAM = 'frontend_message';

	/**
	 * FPC cache instance
	 *
	 * @var Mage_Core_Model_Cache
	 */
	static protected $_cache = null;

	/**
	 * Cache instance static getter
	 *
	 * @return Mage_Core_Model_Cache
	 */
	static public function getCacheInstance()
	{
		if (is_null(self::$_cache)) {
			$options = Mage::app()->getConfig()->getNode('global/full_page_cache');
			if (!$options) {
				self::$_cache = Mage::app()->getCacheInstance();
				return self::$_cache;
			}

			$options = $options->asArray();

			foreach (array('backend_options', 'slow_backend_options') as $tag) {
				if (!empty($options[$tag]['cache_dir'])) {
					$options[$tag]['cache_dir'] = Mage::getBaseDir('var') . DS . $options[$tag]['cache_dir'];
					Mage::app()->getConfig()->getOptions()->createDirIfNotExists($options[$tag]['cache_dir']);
				}
			}

			self::$_cache = Mage::getModel('core/cache', $options);
		}

		return self::$_cache;
	}
}
