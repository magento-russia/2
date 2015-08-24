<?php
class Df_Localization_Model_Realtime_Translator extends Df_Core_Model_Abstract {
	/** @return bool */
	public function isEnabled() {
		if (!isset($this->{__METHOD__})) {
			// Обратите внимание, что данная технология перевода применяется только для витрины,
			// но не применяется для административной части.
			$this->{__METHOD__} = !df_is_admin();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function log($message) {
		if (self::$needLog) {
			Mage::log($message, $level = null, $file = 'rm.translation.log', $forceLog = true);
		}
	}
	/** @var bool $needLog */
	public static $needLog = false;
	/** @var string */
	public static $watched = 'New';
	/** @var bool */
	public static $needle = false;

	/**
	 * @param string $text
	 * @param string $code
	 * @return string|null
	 */
	public function translate($text, $code) {
		/** @var string|null $result */
		$result = null;
		self::$needLog = self::$watched && (self::$watched === $text);
		$this->log('Переводим: ' . $code);
		if ($this->getThemeDictionary()) {
			$this->log('Словарь оформительcкой темы...');
			$result = $this->getThemeDictionary()->translate($text, $code);
			$this->log('результат: ' . $result);
		}
		if (is_null($result)) {
			$this->log('Общий словарь...');
			$result = $this->getCommonDictionary()->translate($text, $code);
			$this->log('результат: ' . $result);
		}
		return $result;
	}

	/**
	 * @param string $localPath
	 * @return Df_Localization_Model_Realtime_Dictionary
	 */
	private function createDictionary($localPath) {
		return Df_Localization_Model_Realtime_Dictionary::i($localPath);
	}

	/** @return Df_Localization_Model_Realtime_Dictionary */
	private function getCommonDictionary() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->createDictionary(self::COMMOM_DICTIONARY_FILE_NAME);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Model_Realtime_Dictionary|null */
	private function getThemeDictionary() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Localization_Model_Realtime_Dictionary|null $result */
			$result = null;
			/** @var Mage_Core_Model_Config_Element $configNode */
			$configNode =
				Mage::app()->getConfig()->getNode(
					rm_config_key(
						'rm'
						,'translation'
						,rm_design_package()->getPackageName()
						,df_mage()->core()->design()->getThemeFrontend()
					)
				)
			;
			if ($configNode instanceof Varien_Simplexml_Element) {
				/** @var string $fileName */
				$fileName = (string)$configNode;
				df_assert_string_not_empty($fileName);
				/** @var string $result */
				$result = $this->createDictionary($fileName);
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	const _CLASS = __CLASS__;
	const COMMOM_DICTIONARY_FILE_NAME = 'common.xml';

	/** @return Df_Localization_Model_Realtime_Translator */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}