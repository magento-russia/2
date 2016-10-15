<?php
class Df_Downloadable_Model_Url extends Df_Core_Model {
	/** @return string */
	private function getResult() {
		/** @var string $result */
		switch ($this->getType()) {
			case Mage_Downloadable_Helper_Download::LINK_TYPE_URL:
				$result = $this->getUrl();
				df_result_string_not_empty($result);
				break;
			case Mage_Downloadable_Helper_Download::LINK_TYPE_FILE:
				$result = $this->getUrlByPath();
				break;
			default:
				df_error('Неизвестный тип цифрового товара: «%s».', $this->getType());
		}
		return $result;
	}

	/** @return string|null */
	private function getPath() {return $this->cfg(self::$P__PATH);}

	/** @return string */
	private function getType() {return $this->cfg(self::$P__TYPE);}

	/** @return string|null */
	private function getUrl() {return $this->cfg(self::$P__URL);}

	/** @return string */
	private function getUrlByPath() {
		df_assert_string_not_empty($this->getPath());
		return strtr('{base-media}downloadable/files/{folder}/{relative}', array(
			'{base-media}' => Mage::getBaseUrl('media')
			, '{folder}' => $this->isSample() ? 'link_samples' : 'links'
			, '{relative}' => df_url_from_path(df_trim_left($this->getPath(), '/'))
		));
	}

	/** @return bool */
	private function isSample() {return $this->cfg(self::$P__IS_SAMPLE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__IS_SAMPLE, DF_V_BOOL)
			->_prop(self::$P__PATH, DF_V_STRING, false)
			->_prop(self::$P__TYPE, DF_V_STRING_NE)
			->_prop(self::$P__URL, DF_V_STRING, false)
		;
	}
	/** @var string */
	private static $P__IS_SAMPLE = 'is_sample';
	/** @var string */
	private static $P__PATH = 'path';
	/** @var string */
	private static $P__TYPE = 'type';
	/** @var string */
	private static $P__URL = 'url';
	/**
	 * @param string $type
	 * @param bool $isSample
	 * @param string|null $path
	 * @param string|null $url
	 * @return string
	 */
	public static function p($type, $isSample, $path, $url) {
		/** @var Df_Downloadable_Model_Url $processor */
		$processor = new self(array(
			self::$P__TYPE => $type
			, self::$P__IS_SAMPLE => $isSample
			, self::$P__PATH => $path
			, self::$P__URL => $url
		));
		return $processor->getResult();
	}
}