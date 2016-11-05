<?php
class Df_Localization_Realtime_Translator extends Df_Core_Model {
	/** @return bool */
	public function isEnabled() {
		if (!isset($this->{__METHOD__})) {
			// Обратите внимание, что данная технология перевода применяется только для витрины,
			// но не применяется для административной части.
			// 2015-09-04
			// А вообще странно: почему я так решил раньше?
			// Для Magento 2 я решил применять эту технологию
			// и для русификации административного интерфейса,
			// и это выглядит естественным и правильным.
			$this->{__METHOD__} =
				!df_is_admin()
				/**
				 * 2015-08-15
				 * Странно, что этого условия не было здесь раньше.
				 * @see Df_Localization_Realtime_Dictionary_ModulePart_Term::original()
				 * @see Df_Localization_Realtime_Dictionary_ModulePart_Term::translated()
				 */
				&& Mage_Core_Model_Locale::DEFAULT_LOCALE !== df_locale()
			;
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
	public static $watched = '';
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
		/**
		 * 2015-08-23
		 * Словаря оформительской темы может не быть.
		 * Используем значение false как признак неинициализированности словаря
		 * и значение null как признак отсутствия словаря.
		 * @var Df_Localization_Realtime_Dictionary|null|false $theme
		 */
		static $theme = false;
		if (false === $theme) {
			$theme =
				!$this->themeDictionaryPath()
				? null
				: $this->dictionary($this->themeDictionaryPath())
			;
		}
		if ($theme) {
			$this->log('Словарь оформительcкой темы...');
			$result = $theme->translate($text, $code);
			$this->log('результат: ' . $result);
		}
		if (is_null($result)) {
			$this->log('Общий словарь...');
			/** @var Df_Localization_Realtime_Dictionary $common */
			static $common;
			if (!$common) {
				$common = $this->dictionary('common.xml');
			}
			$result = $common->translate($text, $code);
			$this->log('результат: ' . $result);
		}
		return $result;
	}

	/**
	 * @used-by translate()
	 * @param string $localPath
	 * @return Df_Localization_Realtime_Dictionary|null
	 */
	private function dictionary($localPath) {
		return !$localPath ? null : Df_Localization_Realtime_Dictionary::s($localPath);
	}

	/**
	 * @used-by translate()
	 * @return string|null
	 */
	private function themeDictionaryPath() {
		/** @var Mage_Core_Model_Config_Element $configNode */
		$configNode = df_config_node(
			'rm/translation'
			,df_design_package()->getPackageName()
			,df_mage()->core()->design()->getThemeFrontend()
		);
		return !$configNode ? null : df_leaf_sne($configNode);
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}