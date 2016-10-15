<?php
class Df_Localization_Realtime_Dictionary extends Df_Localization_Dictionary {
	/**
	 * @param string $text
	 * @param string $code
	 * @return string|null
	 * @throws Exception
	 */
	public function translate($text, $code) {
		/**
		 * Не допускаем рекурсивность данного метода,
		 * потому что она может привести к зависанию системы.
		 * Рекурсивность может возникнуть
		 * из-за вызова метода @uses Mage_Core_Block_Template::getTemplate() ниже.
		 * @var bool $isProcessing
		 */
		static $isProcessing = false;
		/** @var string|null $result */
		$result = null;
		if (!$isProcessing && $this->hasEntry($text)) {
			$this->log('Термин найден.');
			$isProcessing = true;
			try {
				if (!df_state()->hasBlocksBeenGenerated()) {
					if (!Mage::app()->getRequest()->isXmlHttpRequest()) {
						/**
						 * Вызов из макета.
						 * Пока никак не обрабатываем.
						 * Не помню, почему.
						 * Надо выяснить и изложить причину в комментарии.
						 */
						if (df_state()->hasBlocksGenerationBeenStarted()) {
							$this->log('блоки создаются');
							$result = $this->handleForLayout($text, $code);
						}
						else {
							$this->log('блоки ещё не созданы');
						}
					}
					else {
						// Вызов из контроллера, обработка асинхронного запроса
						$this->log('вызов из контроллера, обработка асинхронного запроса');
						$result = $this->handleForController($text, $code);
					}
				}
				else {
					if (!df_state()->hasLayoutRenderingBeenStarted()) {
						// Вызов из контроллера.
						$this->log('рисование не началось');
						$result = $this->handleForController($text, $code);
					}
					else {
						// Вызов из шаблона.
						$this->log('рисование шаблона');
						if (df_state()->block()) {
							$result = $this->handleForTemplate($text, $code);
						}
						if (is_null($result)) {
							$result = $this->handleForController($text, $code);
						}
					}
				}
				$isProcessing = false;
			}
			catch(Exception $e) {
				$isProcessing = false;
				throw $e;
			}
		}
		return $result;
	}

	/**
	 * 2015-08-15
	 * @override
	 * @see Df_Core_Model::cachedGlobal()
	 * @return string[]
	 */
	protected function cachedGlobal() {return self::m(__CLASS__, '_entries');}

	/**
	 * 2015-08-15
	 * @override
	 * @see Df_Core_Model::cacheKeySuffix()
	 * @return string
	 */
	protected function cacheKeySuffix() {return $this->pathLocal();}

	/**
	 * 2015-08-15
	 * @override
	 * @see Df_Core_Model::cacheTags()
	 * @return string|string[]
	 */
	protected function cacheTags() {return Mage_Core_Model_Translate::CACHE_TAG;}

	/**
	 * @override
	 * @see Df_Localization_Dictionary::type()
	 * @return string
	 */
	protected function type() {return 'realtime';}

	/**
	 * 2015-09-21
	 * Добавил поддержку выражений типа <controller action='sales_transactions_*'>
	 * @param string $pattern
	 * @param string $value
	 * @return bool
	 */
	private function _continue($pattern, $value) {
		/** @var bool $result */
		$result = false;
		$pattern = (string)$pattern;
		if (!in_array($pattern, array('', '*', $value), $strict = true)) {
			/** @var string $trimmed */
			$trimmed = df_trim_right($pattern, '*');
			/**
			 * 2015-09-21
			 * Добавил поддержку выражений типа <controller action='sales_transactions_*'>
			 * 1) $trimmed === $pattern означает, что $pattern не заканчивается на *.
			 * А учитывая, что $pattern !== $value, то _continue должна вернуть true.
			 * 2) !df_starts_with($value, $trimmed) означает, что $pattern заканчивается на *,
			 * однако текст до * не совпадает с началом $value.
			 */
			$result = $trimmed === $pattern || !df_starts_with($value, $trimmed);
		}
		return $result;
	}

	/**
	 * 2015-09-19
	 * @used-by handleForController()
	 * @used-by handleForTemplate()
	 * @param string $expectedClass
	 * @param object $object
	 * @return bool
	 */
	private function _continueC($expectedClass, $object) {
		return
			$expectedClass
			&& '*' !== $expectedClass
			&& @class_exists($expectedClass)
			&& !($object instanceof $expectedClass)
		;
	}

	/**
	 * @param string $code
	 * @return string
	 */
	private function getModuleNameFromCode($code) {
		/** @var string[] $codeParts */
		$codeParts = explode(Mage_Core_Model_Translate::SCOPE_SEPARATOR, $code);
		return 1 < count($codeParts) ? df_first($codeParts) : '';
	}

	/**
	 * @param string $text
	 * @param string $code
	 * @return string|null
	 * @throws Exception
	 */
	private function handleForController($text, $code) {
		/** @var string $result */
		$result = null;
		/** @var Mage_Core_Controller_Varien_Action|null $controller */
		$controller = df_controller();
		if ($controller) {
			/** @var string|null $currentModuleName */
			/**
			 * Приоритет должен отдаваться имени модуля, указанному в коде,
			 * потому что вызов метода __ необязательно был произведён в контексте $this
			 */
			$currentModuleName = $this->getModuleNameFromCode($code);
			if (!$currentModuleName) {
				$currentModuleName = df_module_name($controller);
			}
			df_assert_string_not_empty($currentModuleName);
			/** @var Df_Localization_Realtime_Dictionary_Module|null $currentModule */
			$currentModule = $this->modules()->findById($currentModuleName);
			if (!is_null($currentModule)) {
				/**
				 * например: «checkout_onepage_index»
				 * @var string $currentControllerAction
				 */
				$currentControllerAction = df_action_name();
				foreach ($currentModule->getControllers() as $entry) {
					/** @var Df_Localization_Realtime_Dictionary_ModulePart_Controller $entry */
					if ($this->_continue($entry->getAction(), $currentControllerAction)
						|| $this->_continueC($entry->getControllerClass(), $controller)
					) {
						continue;
					}
					/** @var Df_Localization_Realtime_Dictionary_ModulePart_Terms $terms */
					$terms = $entry->terms();
					/** @var Df_Localization_Realtime_Dictionary_ModulePart_Term|null $term */
					$term = $terms->findById($text);
					if (!is_null($term)) {
						$result = $term->translated();
						break;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * @param string $text
	 * @param string $code
	 * @return string|null
	 * @throws Exception
	 */
	private function handleForLayout($text, $code) {
		/** @var string $result */
		$result = null;
		if ($this->layout()) {
			/** @var Df_Localization_Realtime_Dictionary_ModulePart_Term|null $term */
			$term = $this->layout()->terms()->findById($text);
			if (!is_null($term)) {
				$result = $term->translated();
			}
		}
		return $result;
	}

	/**
	 * @param string $text
	 * @param string $code
	 * @return string|null
	 * @throws Exception
	 */
	private function handleForTemplate($text, $code) {
		/** @var string $result */
		$result = null;
		/** @var Mage_Core_Block_Abstract|null $currentBlock */
		$currentBlock = df_state()->block();
		/** @var string|null $currentModuleName */
		$currentModuleName =
			/**
			 * Приоритет должен отдаваться имени модуля, указанному в коде,
			 * потому что вызов метода __ необязательно был произведён в контексте $this
			 */
			$this->getModuleNameFromCode($code)
		;
		if (!$currentModuleName) {
			$currentModuleName = $currentBlock->getModuleName();
		}
		if ($currentModuleName) {
			/** @var Df_Localization_Realtime_Dictionary_Module|null $currentModule */
			$currentModule = $this->modules()->findById($currentModuleName);
			if (!is_null($currentModule)) {
				/** @var string $currentTemplate */
				$currentTemplate = null;
				if ($currentBlock instanceof Mage_Core_Block_Template) {
					/** @var Mage_Core_Block_Template $currentBlockTemplated */
					$currentBlockTemplated = $currentBlock;
					$currentTemplate = $currentBlockTemplated->getTemplate();
				}
				foreach ($currentModule->getBlocks() as $block) {
					/** @var Df_Localization_Realtime_Dictionary_ModulePart_Block $block */
					if (
						!$block->matchTemplate($currentTemplate)
						|| $this->_continueC($block->getBlockClass(), $currentBlock)
						|| $this->_continue($block->getName(), $currentBlock->getNameInLayout())
					) {
						continue;
					}
					/** @var Df_Localization_Realtime_Dictionary_ModulePart_Terms $terms */
					$terms = $block->terms();
					/** @var Df_Localization_Realtime_Dictionary_ModulePart_Term|null $term */
					$term = $terms->findById($text);
					if (!is_null($term)) {
						$result = $term->translated();
						break;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Этот метод используется только для быстрой проверки
	 * наличия в словаре перевода конкретного текста.
	 * @used-by Df_Localization_Realtime_Dictionary::translate()
	 *
	 * Метод реализован именно таким способом ради ускорения.
	 * Данная реализация работает быстрее, нежели использование @see in_array()
	 * http://stackoverflow.com/a/5036972
	 *
	 * @param string $text
	 * @return bool
	 */
	private function hasEntry($text) {
		if (!isset($this->_entries) || !is_array($this->_entries)) {
			$this->_entries = array_flip($this->e()->leafs('//' . Mage_Core_Model_Locale::DEFAULT_LOCALE));
		}
		return isset($this->_entries[$text]);
	}

	/**
	 * @used-by handleForLayout()
	 * @return Df_Localization_Realtime_Dictionary_Layout|null
	 */
	private function layout() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				!$this->isChildExist('layout')
				? null
				: Df_Localization_Realtime_Dictionary_Layout::i($this->e()->{'layout'})
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * @param string $message
	 * @return void
	 */
	private function log($message) {Df_Localization_Realtime_Translator::s()->log($message);}

	/** @return Df_Localization_Realtime_Dictionary_Modules */
	private function modules() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Realtime_Dictionary_Modules::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @var array(string => bool) */
	protected $_entries;
	/**
	 * @param string $pathLocal
	 * @return Df_Localization_Realtime_Dictionary
	 */
	public static function s($pathLocal) {return self::sc(__CLASS__, $pathLocal);}
}