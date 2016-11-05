<?php
class Df_PageCache_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Character sets
	 */
	const CHARS_LOWERS                          = 'abcdefghijklmnopqrstuvwxyz';
	const CHARS_UPPERS                          = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const CHARS_DIGITS                          = '0123456789';

	/**
	 * Get random generated string
	 *
	 * @param int $len
	 * @param string|null $chars
	 * @return string
	 */
	public static function getRandomString($len, $chars = null)
	{
		if (is_null($chars)) {
			$chars = self::CHARS_LOWERS . self::CHARS_UPPERS . self::CHARS_DIGITS;
		}
		for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
			$str .= $chars[mt_rand(0, $lc)];
		}
		return $str;
	}

	/**
	 * Wrap string with placeholder wrapper
	 *
	 * @param string $string
	 * @return string
	 */
	public static function wrapPlaceholderString($string)
	{
		return '{{' . chr(1) . chr(2) . chr(3) . $string . chr(3) . chr(2) . chr(1) . '}}';
	}

	/**
	 * Prepare content for saving
	 *
	 * @param string $content
	 */
	public static function prepareContentPlaceholders(&$content)
	{
		/**
		 * Replace all occurrences of session_id with unique marker
		 */
		Df_PageCache_Helper_Url::replaceSid($content);
		/**
		 * Replace all occurrences of form_key with unique marker
		 */
		Df_PageCache_Helper_Form_Key::replaceFormKey($content);
	}

	/**
	 * Check if the request is secure or not
	 *
	 * @return bool
	 */
	public static function isSSL()
	{
		$isSSL           = false;
		$standardRule    = !empty($_SERVER['HTTPS']) && ('off' != $_SERVER['HTTPS']);
		$offloaderHeader = Df_PageCache_Model_Cache::getCacheInstance()
			->load(Df_PageCache_Model_Processor::SSL_OFFLOADER_HEADER_KEY);
		$offloaderHeader = trim(@unserialize($offloaderHeader));

		if ((!empty($offloaderHeader) && !empty($_SERVER[$offloaderHeader])) || $standardRule) {
			$isSSL = true;
		}
		return $isSSL;
	}

	/** @return void */
	public function clean() {Mage::app()->cleanCache(Df_PageCache_Model_Processor::CACHE_TAG);}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
