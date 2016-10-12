<?php
class Df_Page_Model_Html_Head extends Df_Core_Model {
	/**
	 * @param array $staticItems
	 * @return array
	 */
	public function addVersionStamp(array $staticItems) {
		foreach ($staticItems as &$rows) {
			foreach ($rows as &$name) {
				if (0 === strpos($name, 'df/')) {
					$name = df()->url()->addVersionStamp($name);
				}
			}
		}
		return $staticItems;
	}

	/**
	 * @param string $format
	 * @param mixed[] $staticItems
	 * @return string
	 */
	public function prependAdditionalTags($format, array &$staticItems) {
		return Df_Page_Model_Html_Head_JQuery::s()->process($format, $staticItems);
	}

	/** @return Df_Page_Model_Html_Head */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}