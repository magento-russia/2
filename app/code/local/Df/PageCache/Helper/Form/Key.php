<?php
class Df_PageCache_Helper_Form_Key extends Mage_Core_Helper_Abstract
{
	/**
	 * Retrieve unique marker value
	 *
	 * @return string
	 */
	protected static function _getFormKeyMarker()
	{
		return Df_PageCache_Helper_Data::wrapPlaceholderString('_FORM_KEY_MARKER_');
	}

	/**
	 * Replace form key with placeholder string
	 *
	 * @param string $content
	 * @return bool
	 */
	public static function replaceFormKey(&$content)
	{
		if (!$content) {
			return $content;
		}
		/** @var $session Mage_Core_Model_Session */
		$session = Mage::getSingleton('core/session');
		$replacementCount = 0;
		$content = str_replace($session->getFormKey(), self::_getFormKeyMarker(), $content, $replacementCount);
		return ($replacementCount > 0);
	}

	/**
	 * Restore user form key in form key placeholders
	 *
	 * @param string $content
	 * @param string $formKey
	 * @return bool
	 */
	public static function restoreFormKey(&$content, $formKey)
	{
		if (!$content) {
			return false;
		}
		$replacementCount = 0;
		$content = str_replace(self::_getFormKeyMarker(), $formKey, $content, $replacementCount);
		return ($replacementCount > 0);
	}
}
