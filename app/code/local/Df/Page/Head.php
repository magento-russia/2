<?php
class Df_Page_Head {
	/**
	 * @used-by Df_Adminhtml_Block_Page_Head::_prepareStaticAndSkinElements()
	 * @used-by Df_Page_Block_Html_Head::_prepareStaticAndSkinElements()
	 * @param array $staticItems
	 * @return array
	 */
	public static function addVersionStamp(array $staticItems) {
		foreach ($staticItems as &$rows) {
			foreach ($rows as &$name) {
				if (0 === strpos($name, 'df/')) {
					$name = df_url_add_version_stamp($name);
				}
			}
		}
		return $staticItems;
	}

	/**
	 * @used-by Df_Adminhtml_Block_Page_Head::_prepareStaticAndSkinElements()
	 * @used-by Df_Page_Block_Html_Head::_prepareStaticAndSkinElements()
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	public static function prependTags($format, array &$staticItems) {
		return Df_Page_JQueryInjecter::p($format, $staticItems);
	}
}