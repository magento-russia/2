<?php
class Df_Core_Helper_Url {
	/**
	 * @used-by Df_Core_Model_Design_PackageM::getSkinUrl()
	 * @used-by Df_Page_Model_Html_Head::addVersionStamp()
	 * @param string $url
	 * @param string|null $version [optional]
	 * @return string
	 */
	public function addVersionStamp($url, $version = null) {
		return $url . '?v=' . ($version ? $version : rm_version());
	}

	/**
	 * @used-by Df_Downloadable_Model_Url::getUrlByPath()
	 * @param string $path
	 * @return string
	 */
	public function fromPath($path) {
		return $this->encode(str_replace(DS, '/', df_path()->makeRelative($path)));
	}

	/**
	 * @used-by Df_Dataflow_Model_Importer_Product_Images::getImages()
	 * http://stackoverflow.com/a/15011528
	 * http://www.php.net/manual/en/function.filter-var.php
	 * Обратите внимание, что
	 * filter_var('/C/A/CA559AWLE574_1.jpg', FILTER_VALIDATE_URL) вернёт false
	 * @param string $string
	 * @return bool
	 */
	public function is($string) {return false !== filter_var($string, FILTER_VALIDATE_URL);}

	/**
	 * @used-by fromPath()
	 * @param string $url
	 * @return string
	 */
	private function encode($url) {return $this->processParts('rawurlencode', $url);}

	/**
	 * @used-by encode()
	 * @param string $function
	 * @param string $url
	 * @return string
	 */
	private function processParts($function, $url) {
		return df_cc_path(array_map($function, explode('/', $url)));
	}

	/**
	 * @used-by df_url()
	 * @return Df_Core_Helper_Url
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}