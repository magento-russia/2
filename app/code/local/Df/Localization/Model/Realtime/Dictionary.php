<?php
class Df_Localization_Model_Realtime_Dictionary extends Df_Localization_Model_Dictionary {
	/**
	 * @param string $text
	 * @param string $code
	 * @return string|null
	 * @throws Exception
	 */
	public function translate($text, $code) {
		/** @var bool $isProcessing */
		/**
		 * Не допускаем рекурсивность данного метода,
		 * потому что она может привести к зависанию системы.
		 *
		 * Рекурсивность может возникнуть
		 * из-за вызова метода Mage_Core_Block_Template::getTemplate ниже.
		 *
		 * @var bool $isProcessing
		 */
		static $isProcessing = false;
		/** @var string|null $result */
		$result = null;
		/** @var bool $needLog */
		$needLog =
				Df_Localization_Model_Realtime_Translator::$watched
			&&
				(Df_Localization_Model_Realtime_Translator::$watched === $text)
		;
		if ((false === $isProcessing) && $this->hasEntry($text)) {
			$this->log('Термин найден.');
			$isProcessing = true;
			try {
				if (!rm_state()->hasBlocksBeenGenerated()) {
					if (!Mage::app()->getRequest()->isXmlHttpRequest()) {
						/**
						 * Вызов из макета.
						 * Пока никак не обрабатываем.
						 * Не помню, почему.
						 * Надо выяснить и изложить причину в комментарии.
						 */
						if (rm_state()->hasBlocksGenerationBeenStarted()) {
							$this->log('блоки создаются');
							$result = $this->handleTranslateForLayout($text, $code);
						}
						else {
							$this->log('блоки ещё не созданы');
						}
					}
					else {
						// Вызов из контроллера, обработка асинхронного запроса
						$this->log('вызов из контроллера, обработка асинхронного запроса');
						$result = $this->handleTranslateForController($text, $code);
					}
				}
				else {
					if (!rm_state()->hasLayoutRenderingBeenStarted()) {
						// Вызов из контроллера.
						$this->log('рисование не началось');
						$result = $this->handleTranslateForController($text, $code);
					}
					else {
						// Вызов из шаблона.
						$this->log('рисование шаблона');
						$result = $this->handleTranslateForTemplate($text, $code);
						if (!$result) {
							$result = $this->handleTranslateForController($text, $code);
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
	 * @override
	 * @return string
	 */
	protected function getType() {return 'realtime';}

	/**
	 * @param string $a
	 * @param string $b
	 * @return bool
	 */
	private function _continue($a, $b) {return $a && !in_array($a, array('*', $b));}

	/** @return Df_Localization_Model_Realtime_Dictionary_Layout|null */
	public function getLayout() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->isChildExist('layout')
				? null
				: Df_Localization_Model_Realtime_Dictionary_Layout::i($this->e()->{'layout'})
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * @param string $code
	 * @return string
	 */
	private function getModuleNameFromCode($code) {
		/** @var string $result */
		$result = '';
		/** @var array $codeParts */
		$codeParts =
			explode(
				Mage_Core_Model_Translate::SCOPE_SEPARATOR
				,$code
			)
		;
		if (1 < count($codeParts)) {
			$result = rm_first($codeParts);
		}
		return $result;
	}

	/** @return Df_Localization_Model_Realtime_Dictionary_Modules */
	private function getModules() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Model_Realtime_Dictionary_Modules::i($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $text
	 * @param string $code
	 * @return string|null
	 * @throws Exception
	 */
	private function handleTranslateForController($text, $code) {
		/** @var string $result */
		$result = null;
		/** @var Mage_Core_Controller_Varien_Action|null $currentController */
		$currentController = rm_state()->getController();
		if (!is_null($currentController)) {
			/** @var string $currentControllerClass */
			$currentControllerClass = get_class($currentController);
			/** @var string|null $currentModuleName */
			$currentModuleName =
				/**
				 * Приоритет должен отдаваться имени модуля, указанному в коде,
				 * потому что вызов метода __ необязательно был произведён в контексте $this
				 */
				$this->getModuleNameFromCode($code)
			;
			if (!$currentModuleName) {
				$currentModuleName = df()->reflection()->getModuleName($currentControllerClass);
			}
			df_assert_string_not_empty($currentModuleName);
			/** @var Df_Localization_Model_Realtime_Dictionary_Module|null $currentModule */
			$currentModule = $this->getModules()->findById($currentModuleName);
			if (!is_null($currentModule)) {
				/**
				 * например: «checkout_onepage_index»
				 * @var string $currentControllerAction
				 */
				$currentControllerAction =
					$currentController
						->getFullActionName(
							$delimiter = '_'
						)
				;
				foreach ($currentModule->getControllers() as $controller) {
					/** @var Df_Localization_Model_Realtime_Dictionary_ModulePart_Controller $controller */
					if (
							$this->_continue($controller->getAction(), $currentControllerAction)
						||
							$this->_continue($controller->getControllerClass(), $currentControllerClass)
					) {
						continue;
					}
					/** @var Df_Localization_Model_Realtime_Dictionary_ModulePart_Terms $terms */
					$terms = $controller->getTerms();
					/** @var Df_Localization_Model_Realtime_Dictionary_ModulePart_Term|null $term */
					$term = $terms->findById($text);
					if (!is_null($term)) {
						$result = $term->getTextTranslated();
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
	private function handleTranslateForLayout($text, $code) {
		/** @var string $result */
		$result = null;
		if ($this->getLayout()) {
			/** @var Df_Localization_Model_Realtime_Dictionary_ModulePart_Term|null $term */
			$term = $this->getLayout()->getTerms()->findById($text);
			if (!is_null($term)) {
				$result = $term->getTextTranslated();
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
	private function handleTranslateForTemplate($text, $code) {
		/** @var string $result */
		$result = null;
		/** @var Mage_Core_Block_Abstract|null $currentBlock */
		$currentBlock = rm_state()->getCurrentBlock();
		/** @var string|null $currentModuleName */
		$currentModuleName =
			/**
			 * Приоритет должен отдаваться имени модуля, указанному в коде,
			 * потому что вызов метода __ необязательно был произведён в контексте $this
			 */
			$this->getModuleNameFromCode($code)
		;
		if (!$currentModuleName && !is_null($currentBlock)) {
			$currentModuleName = $currentBlock->getModuleName();
		}
		if ($currentModuleName) {
			/** @var Df_Localization_Model_Realtime_Dictionary_Module|null $currentModule */
			$currentModule = $this->getModules()->findById($currentModuleName);
			if (!is_null($currentModule)) {
				/** @var string $currentTemplate */
				$currentTemplate = null;
				if ($currentBlock && ($currentBlock instanceof Mage_Core_Block_Template)) {
					/** @var Mage_Core_Block_Template $currentBlockTemplated */
					$currentBlockTemplated = $currentBlock;
					$currentTemplate = $currentBlockTemplated->getTemplate();
				}
				foreach ($currentModule->getBlocks() as $block) {
					/** @var Df_Localization_Model_Realtime_Dictionary_ModulePart_Block $block */
					if (
							!$block->matchTemplate($currentTemplate)
						||
							!$block->matchClass($currentBlock ? get_class($currentBlock) : '')
						||
							$this->_continue(
								$block->getName(), $currentBlock ? $currentBlock->getNameInLayout() : ''
							)
					) {
						continue;
					}
					/** @var Df_Localization_Model_Realtime_Dictionary_ModulePart_Terms $terms */
					$terms = $block->getTerms();
					/** @var Df_Localization_Model_Realtime_Dictionary_ModulePart_Term|null $term */
					$term = $terms->findById($text);
					if (!is_null($term)) {
						$result = $term->getTextTranslated();
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
	 * @see Df_Localization_Model_Realtime_Dictionary::translate()
	 * @param string $text
	 * @return bool
	 */
	private function hasEntry($text) {
		/**
		 * Метод реализован именно таким способом ради ускорения.
		 * Данная реализация работает быстрее, нежели использование @see in_array()
		 * @link http://stackoverflow.com/a/5036972
		 */
		if (!isset($this->_entries)) {
			/** @var bool $canUseCache */
			$canUseCache = Mage::app()->useCache('translate');
			/** @var string $cacheId */
			/**
			 * Добавляем к идентификатору кэша файловый путь словаря,
			 * потому что в любой момент мы работаем сразу с двумя словарями:
			 * общим словарём и словарём конкретной темы.
			 */
			$cacheId = __METHOD__ . '::' . md5($this->getPathFull());
			if ($canUseCache) {
				/**
				 * @see Zend_Json::decode() использует json_decode при наличии расширения PHP JSON
				 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
				 * @see Zend_Json::decode
				 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
				 * Обратите внимание,
				 * что расширение PHP JSON не входит в системные требования Magento.
				 * @link http://www.magentocommerce.com/system-requirements
				 * Поэтому использование @see Zend_Json::decode выглядит более правильным,
				 * чем @see json_decode().
				 *
				 * Обратите внимание, что при использовании @see json_decode() напрямую
				 * параметр $assoc = true надо указывать обязательно,
				 * иначе @see json_decode() может вернуть объект даже в том случае,
				 * когда посредством @see json_encode() был кодирован массив.
				 *
				 * При использовании @see Zend_Json::decode()
				 * второй параметр $objectDecodeType имеет значение Zend_Json::TYPE_ARRAY по умолчанию,
				 * поэтому его можно не указывать.
				 */
				$this->_entries = Zend_Json::decode(Mage::app()->loadCache($cacheId));
			}
			if (!isset($this->_entries) || !is_array($this->_entries)) {
				foreach ($this->e()->xpath('//en_US') as $entry) {
					/** @var Df_Varien_Simplexml_Element $entry */
					$this->_entries[(string)$entry] = true;
				}
				if ($canUseCache) {
					Mage::app()->saveCache(
						/**
						 * @see json_encode() / @see json_decode работает быстрее,
						 * чем @see serialize() / @see unserialize()
						 * @link http://stackoverflow.com/a/7723730
						 * @link http://stackoverflow.com/a/804053
						 *
						 * Zend_Json::encode использует json_encode при наличии расширения PHP JSON
						 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
						 * @see Zend_Json::encode
						 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
						 * Обратите внимание,
						 * что расширение PHP JSON не входит в системные требования Magento.
						 * @link http://www.magentocommerce.com/system-requirements
						 * Поэтому использование Zend_Json::encode выглядит более правильным, чем json_encode.
						 */
						json_encode($this->_entries)
						, $cacheId
						, array(Mage_Core_Model_Translate::CACHE_TAG)
						, null
					);
				}
			}
		}
		return isset($this->_entries[$text]);
	}
	/** @var array(string => bool) */
	private $_entries;

	/**
	 * @param string $message
	 * @return void
	 */
	private function log($message) {Df_Localization_Model_Realtime_Translator::s()->log($message);}

	const _CLASS = __CLASS__;
	/**
	 * @param string $pathLocal
	 * @return Df_Localization_Model_Onetime_Dictionary
	 */
	public static function i($pathLocal) {return self::_i(__CLASS__, $pathLocal);}
}