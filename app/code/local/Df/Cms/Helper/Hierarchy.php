<?php
class Df_Cms_Helper_Hierarchy extends Mage_Core_Helper_Abstract {
	const XML_PATH_HIERARCHY_ENABLED	= 'df_cms/hierarchy/enabled';
	const METADATA_VISIBILITY_PARENT	= '0';
	const METADATA_VISIBILITY_YES	   = '1';
	const METADATA_VISIBILITY_NO		= '2';

	/**
	 * Check is Enabled Hierarchy Functionality
	 * @return bool
	 */
	public function isEnabled() {
		return rm_bool(Mage::getStoreConfigFlag(self::XML_PATH_HIERARCHY_ENABLED));
	}

	/**
	 * Copy meta data from source array to target
	 *
	 * @param array $source
	 * @param array $target
	 * @return array
	 */
	public function copyMetaData($source, $target)
	{
		if (is_array($source)) {
			if (isset($source['pager_visibility'])) {
				$default = $this->_getDefaultMetadataValues('pager_visibility', $source['pager_visibility']);
				if (is_array($default)) {
					$source = array_merge($source, $default);
				}
			}

			$target = $this->_forcedCopyMetaData($source, $target);
		}
		return $target;
	}

	/**
	 * Copy metadata fields that don't depend on isMetadataEnabled
	 *
	 * @param array $source
	 * @param array $target
	 * @return array
	 */
	protected function _forcedCopyMetaData($source, $target)
	{
		if (!is_array($source)) {
			return $target;
		}

		foreach (Df_Cms_Model_Hierarchy_Node::getMetadataKeys() as $element) {
			if (array_key_exists($element, $source)) {
				$target[$element] = $source[$element];
			}
		}
		return $target;
	}

	/**
	 * Return default values for metadata fields based on other field values
	 * Ex: if 'pager_visibility' == '0' then set to zeros pagination params
	 *
	 * @param string $field Field name to search for
	 * @param string $value Field value
	 * @return array|null
	 */
	protected function _getDefaultMetadataValues($field, $value)
	{
		$paginationDefault = array(
			'pager_frame' => '0','pager_jump' => '0',);
		$menuDefault = array(
			'menu_levels_down' => '0','menu_brief' => '0','menu_ordered' => '0','menu_list_type' => '',);
		$default =
			array(
				'pager_visibility' =>
					array(
						self::METADATA_VISIBILITY_PARENT => $paginationDefault
						,self::METADATA_VISIBILITY_NO => $paginationDefault
				)
			)
		;
		return isset($default[$field][$value]) ? $default[$field][$value] : null;
	}

	/** @return Df_Cms_Helper_Hierarchy */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}