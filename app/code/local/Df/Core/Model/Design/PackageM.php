<?php
class Df_Core_Model_Design_PackageM extends Mage_Core_Model_Design_Package {
	/**
	 * Magento CE / EE правильно обрабатывает конструкции типа
	 * img:hover{filter: url(data:
	 * но некорректно обрабатывает те же конструкции, но когда data в кавычках:
	 * img:hover{filter: url("data:
	 * @override
	 * @param string $file
	 * @param string $contents
	 * @return string
	 * http://magento-forum.ru/topic/4502/
	 * http://magento.stackexchange.com/questions/14973/problem-with-data-uris-and-css-file-merge
	 */
	public function beforeMergeCss($file, $contents) {
		$this->_setCallbackFileDir($file);
		/** @var string $cssImport */
		$cssImport = '/@import\\s+([\'"])(.*?)[\'"]/';
		/** @var string $contents */
		$contents = preg_replace_callback($cssImport, array($this, '_cssMergerImportCallback'), $contents);
		/** @var string $cssUrl */
		$cssUrl = '/url\\(\\s*(?![\"\']?data:)([^\\)\\s]+)\\s*\\)?/';
		/** @var string $contents */
		$contents = preg_replace_callback($cssUrl, array($this, '_cssMergerUrlCallback'), $contents);
		return $contents;
	}

	/**
	 * Этот метод должен быть именно публичным,
	 * потому что используется как callback сторонним классом.
	 * @used-by getMergedJsUrl()
	 * @used-by _mergeFiles()
	 * @param string $file
	 * @param string $contents
	 * @return string
	 */
	public function beforeMergeJsRm($file, $contents) {return $contents . ';';}

	/**
	 * Перекрываем родительский метод @see Mage_Core_Model_Design_Package::getFilename() ради кэширования.
	 * @override
	 * @param string $file
	 * @param array $params
	 * @return string
	 */
	public function getFilename($file, array $params) {
		$this->updateParamDefaults($params);
		/** @var string $cacheKey */
		$cacheKey = $file . http_build_query($params);
		/**
		 * Обратите внимание, что объект @see Mage_Core_Model_Design_Package
		 * вовсе не всегда является одиночкой,
		 * ядро иногда создаёт новые экзмемпляры этого класса.
		 * Смотрите, например:
		 * @see Mage_Core_Model_Design_Source_Design::getAllOptions()
		 * @see Mage_Adminhtml_CacheController::cleanMediaAction()
		 */
		/** @var string|null $result */
		$result = Df_Core_Model_Cache_Design_Package::s()->cacheGet($cacheKey);
		if (!$result) {
			//Mage::log('cache miss!');
			$result = $this->_fallback($file, $params, array(
				array(),
				array('_theme' => $this->getFallbackTheme()),
				array('_theme' => self::DEFAULT_THEME),
			));
			Df_Core_Model_Cache_Design_Package::s()->cacheSet($cacheKey, $result);
		}
		return $result;
	}

	/**
	 * @override
	 * @param string[] $files
	 * @return string
	 */
	public function getMergedJsUrl($files) {
		/** @var string $targetFilename */
		$targetFilename = md5(df_csv($files)) . '.js';
		/** @var string $targetDir */
		$targetDir = $this->_initMergerDir('js');
		/** @var string $result */
		$result = '';
		if (
				$targetDir
			&&
				$this->_mergeFiles(
					$files
					, $targetDir . DS . $targetFilename
					, false
					/** @uses beforeMergeJsRm() */
					, $beforeMergeCallback = array($this, 'beforeMergeJsRm')
					, 'js'
				)
		) {
			$result = Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure()) . 'js/' . $targetFilename;
		}
		return $result;
	}

	/**
	 * @override
	 * @param string|null $file [optional]
	 * @param array(string => mixed) $params [optional]
	 * @return string
	 */
	public function getSkinUrl($file = null, array $params = array()) {
		/** @var string $result */
		$result = parent::getSkinUrl($file, $params);
		if (rm_contains($result, '/rm/')) {
			/**
			 * Обратите внимание, что для ресурсов из папки js мы добавляем параметр v по-другому:
			 * в методе Df_Page_Block_Html_Head::_prepareStaticAndSkinElements
			 */
			$result = df_url()->addVersionStamp($result);
		}
		else {
			/** @var bool */
			static $isRunningCustomSolution;
			if (is_null($isRunningCustomSolution)) {
				$isRunningCustomSolution =
					Df_Core_Model_Design_Package::s()->isCustom()
					&& Df_Core_Model_Design_Package::s()->hasConfiguration()
				;
			}
			if ($isRunningCustomSolution) {
				/** @var string $packageUrlPart */
				static $packageUrlPart;
				if (!isset($packageUrlPart)) {
					$packageUrlPart = sprintf('/%s/', Df_Core_Model_Design_Package::s()->getName());
				}
				if (rm_contains($result, $packageUrlPart)) {
					$result = df_url()->addVersionStamp(
						$result, Df_Core_Model_Design_Package::s()->getVersion()
					);
				}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @param string $file
	 * @param array &$params
	 * @param array $fallbackScheme
	 * @return string
	 */
	protected function _fallback($file, array &$params, array $fallbackScheme = array(array())) {
		/**
		 * Раньше здесь стояло:
		 *
			array_splice(
				$fallbackScheme
				,0
				,0
				,array(
					array(
						'_package' => 'rm'
						,'_theme' => 'priority'
					)
					,array(
						'_package' => df_a($params, '_package')
						,'_theme' => df_a($params, '_theme')
					)
				)
			)
			;
		 *
		 * array_unshift, видимо, работает быстрее
		 */
		array_unshift(
			$fallbackScheme
			,array(
				'_package' => 'rm'
				,'_theme' => 'priority'
			)
			,array(
				/**
				 * Сюда мы можем попасть при установке оформительской темы.
				 * В частности, сюда попадаем при установке темы EM Taobaus.
				 * Российская сборка Magento во время работы установочного скрипта
				 * еще не инициализирована, и работа установочного скрипта завершалась сбоем:
				 * «Call to undefined function df_a()»
				 * http://magento-forum.ru/topic/3779/
				 */
				'_package' => isset($params['_package']) ? $params['_package'] : null
				,'_theme' => isset($params['_theme']) ? $params['_theme'] : null
			)
		);
		$fallbackScheme[]=
			array(
				'_package' => 'rm'
				,'_theme' => self::DEFAULT_THEME
			)
		;
		return parent::_fallback($file, $params, $fallbackScheme);
	}
}