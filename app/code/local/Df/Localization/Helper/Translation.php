<?php
class Df_Localization_Helper_Translation extends Mage_Core_Helper_Abstract {
	/** @return Df_Localization_Translation_FileStorage */
	public function getRussianFileStorage() {
		return $this->getFileStorageByCode(Df_Core_Model_Translate::LOCALE__RU_DF);
	}

	/** @return Df_Localization_Translation_FileStorage */
	public function getDefaultFileStorage() {
		return $this->getFileStorageByCode(Mage_Core_Model_Locale::DEFAULT_LOCALE);
	}

	/**
	 * @param string $code
	 * @return Df_Localization_Translation_FileStorage
	 */
	public function getFileStorageByCode($code) {
		df_param_string($code, 0);
		if (!isset($this->{__METHOD__}[$code])) {
			$this->{__METHOD__}[$code] = Df_Localization_Translation_FileStorage::i($code);
		}
		return $this->{__METHOD__}[$code];
	}

	/**
	 * @param string|string[] $text
	 * @param string $module
	 * @return string
	 */
	public function translateByModule($text, $module) {
		$text = df_array($text);
		/**
		 * Раньше тут стояло:
		 * $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $module);
		 * array_unshift($args, $expr);
		 */
		$text[0] = new Mage_Core_Model_Translate_Expr(dfa($text, 0), $module);
		/** @var Mage_Core_Model_Translate $translator */
		static $translator; if (!$translator) {$translator = Mage::app()->getTranslator();}
		return $translator->translate($text);
	}

	/**
	 * @param array $args
	 * @param string[] $modules
	 * @return string
	 */
	public function translateByModules(array $args, array $modules) {
		/** @var string $prevModule */
		$prevModule = null;
		reset($args);
		/** @var string $originalText */
		$originalText = current($args);
		/** @var string $result */
		$result = $originalText;
		if (is_null($result)) {
			/**
			 * Вот почему-то
			 * Mage_Adminhtml_Block_System_Email_Template_Grid_Filter_Type::_getOptions
			 * в Magento CE 1.6.2.0 передаёт первым параметром null:
			 *
				protected function _getOptions()
				{
					$result = array();
					foreach (self::$_types as $code=>$label) {
						$result[]= array('value'=>$code, 'label'=>df_mage()->adminhtmlHelper()->__($label));
					}
		return $result;
				}
			 *
			 * В этом случае мы получаем в текущий метод array(null).
			 */
			$result = '';
		}
		else {
			df_assert_string($result);
			/**
			 * Раньше цикл выполнялся только при условии
			 * if (Mage_Core_Model_Locale::DEFAULT_LOCALE !== rm_locale())
			 * Условие было убрано для устранения дефекта:
			 * http://magento-forum.ru/topic/2066/
			 */
			foreach ($modules as $module) {
				/** @var string $module */
				df_assert_string($module);
				if ($result !== $originalText) {
					break;
				}
				if ($prevModule === $module) {
					break;
				}
				$result = rm_translate($args, $module);
			}
		}
		df_result_string($result);
		return $result;
	}

	/**
	 * @param array $args
	 * @param $object
	 * @return string
	 */
	public function translateByParent(array $args, $object) {
		df_assert(is_object($object));
		/** @var string $parentClass */
		$parentClass = get_parent_class($object);
		/** @var string $currentClass */
		$currentClass = get_class($object);
		if ($parentClass === $currentClass) {
			df_error(
				'[%s] Класс объекта совпадает с его родительским классом: «%s».
				Видимо, программист ошибся.'
				,__METHOD__
				,$currentClass
			);
		}
		return rm_translate($args, rm_module_name($parentClass));
	}

	/** @return Df_Localization_Helper_Translation */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}