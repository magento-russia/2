<?php
class Df_PageCache_Model_Validator
{
	/**#@+
	 * XML paths for lists of change nad delete dependencies
	 */
	const XML_PATH_DEPENDENCIES_CHANGE = 'adminhtml/cache/dependency/change';
	const XML_PATH_DEPENDENCIES_DELETE = 'adminhtml/cache/dependency/delete';
	/**#@-*/

	/**
	 * Mark full page cache as invalidated
	 *
	 */
	protected function _invalidateCache()
	{
		Mage::app()->getCacheInstance()->invalidateType('full_page');
	}

	/**
	 * Get list of all classes related with object instance
	 *
	 * @param $object
	 * @return array
	 */
	protected function _getObjectClasses($object)
	{
		$classes = array();
		if (is_object($object)) {
			$classes[] = get_class($object);
			$parent = $object;
			while ($parentClass = get_parent_class($parent)) {
				$classes[] = $parentClass;
				$parent = $parentClass;
			}
		}
		return $classes;
	}

	/**
	 * Check if during data change was used some model related with page cache and invalidate cache
	 *
	 * @param mixed $object
	 * @return Df_PageCache_Model_Validator
	 */
	public function checkDataChange($object)
	{
		$classes = $this->_getObjectClasses($object);
		$intersect = array_intersect($this->_getDataChangeDependencies(), $classes);
		if (!empty($intersect)) {
			$this->_invalidateCache();
		}

		return $this;
	}

	/**
	 * Check if during data delete was used some model related with page cache and invalidate cache
	 *
	 * @param mixed $object
	 * @return Df_PageCache_Model_Validator
	 */
	public function checkDataDelete($object)
	{
		$classes = $this->_getObjectClasses($object);
		$intersect = array_intersect($this->_getDataDeleteDependencies(), $classes);
		if (!empty($intersect)) {
			$this->_invalidateCache();
		}
		return $this;
	}

	/**
	 * Clean cache by entity tags
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Df_PageCache_Model_Validator
	 */
	public function cleanEntityCache(Mage_Core_Model_Abstract $object)
	{
		$tags = $object->getCacheIdTags();
		if (!empty($tags)) {
			$this->_getCacheInstance()->clean($tags);
		}
		return $this;
	}

	/**
	 * Retrieves cache instance
	 *
	 * @return Mage_Core_Model_Cache
	 */
	protected function _getCacheInstance()
	{
		return Df_PageCache_Model_Cache::getCacheInstance();
	}

	/**
	 * Returns array of data change dependencies from config
	 *
	 * @return array
	 */
	protected function _getDataChangeDependencies()
	{
		return $this->_getDataDependencies(self::XML_PATH_DEPENDENCIES_CHANGE);
	}

	/**
	 * Returns array of data delete dependencies from config
	 *
	 * @return array
	 */
	protected function _getDataDeleteDependencies()
	{
		return $this->_getDataDependencies(self::XML_PATH_DEPENDENCIES_DELETE);
	}

	/**
	 * Get data dependencies by xpath
	 *
	 * @param string $xpath
	 * @return array
	 */
	protected function _getDataDependencies($xpath)
	{
		$node = Mage::getConfig()->getNode($xpath);
		return (!$node)? array() : array_values($node->asArray());
	}
}
