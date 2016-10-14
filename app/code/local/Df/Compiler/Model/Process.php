<?php
class Df_Compiler_Model_Process extends Mage_Compiler_Model_Process {
	/**
	 * @override
	 * @return array
	 */
	public function getCompileClassList() {
		Df_Core_Boot::run();
		$arrFiles = array();
		foreach ($this->getScopes() as $code) {
			/** @var array|null $classes */
			$arrFiles[$code] = array_keys(df_nta(dfa($this->getConfigMap(), $code), true));
			$statClasses = array();
			/** @var string|null $statFileForTheCurrentScope */
			$statFileForTheCurrentScope = dfa($this->getStatFiles(), $code);
			if (!is_null($statFileForTheCurrentScope)) {
				$statClassesAll = df_explode_n(file_get_contents($statFileForTheCurrentScope));
				/** @var int $statClassesCount */
				$statClassesCount = count($statClassesAll);
				/** @var int $statClassesLimit */
				$statClassesLimit = rm_round(1.0 * $statClassesCount);
				$popularStatClasses = array();
				foreach ($statClassesAll as $classInfo) {
					$classInfo = explode(':', $classInfo);
					$popularStatClasses[$classInfo[1]][]= $classInfo[0];
				}
				krsort($popularStatClasses);
				$statClassesUsageCount = 0;
				foreach ($popularStatClasses as $popularStatClassesCurrrent) {
					/** @var array $popularStatClassesCurrrent */
					if ($statClassesUsageCount > $statClassesLimit) {
						break;
					}
					$statClasses = array_merge($statClasses, $popularStatClassesCurrrent);
					$statClassesUsageCount += count($popularStatClassesCurrrent);
				}
			}
			$arrFiles[$code] = array_merge($arrFiles[$code], $statClasses);
			$arrFiles[$code] = dfa_unique_fast($arrFiles[$code]);
			sort($arrFiles[$code]);
		}
		foreach ($arrFiles as $scope => $classes) {
			if ($scope != 'default') {
				foreach ($classes as $index => $class) {
					if (in_array($class, $arrFiles['default'])) {
						unset($arrFiles[$scope][$index]);
					}
				}
			}
		}
		return $arrFiles;
	}

	/**
	 * @override
	 * @param $classes
	 * @param $scope
	 * @return string
	 */
	protected function _getClassesSourceCode($classes, $scope) {
		Df_Core_Boot::run();
		return
			// Видимо, улучшенную компиляцию нельзя отрубать даже по истечению лицензии,
			// иначе при неправильной компиляции сайт может перестать работать
			df_cfg()->admin()->system()->tools()->compilation()->getFix()
			? $this->_getClassesSourceCodeDf($classes, $scope)
			: parent::_getClassesSourceCode($classes, $scope)
		;
	}

	/**
	 * @param $classes
	 * @param $scope
	 * @return string
	 */
	private function _getClassesSourceCodeDf($classes, $scope) {
		$sortedClasses = array();
		foreach ($classes as $className) {
			/** @var string $className */
			if (!@class_exists($className)) {
				continue;
			}
			$implements = array_reverse(class_implements($className));
			foreach ($implements as $class) {
				if (
						!in_array($class, $sortedClasses)
					&&
						!in_array($class, $this->_processedClasses)
					&&
						strstr($class, '_')
				) {
					$sortedClasses[]= $class;
					if ('default' === $scope) {
						$this->_processedClasses[]= $class;
					}
				}
			}
			$extends = array_reverse(class_parents($className));
			foreach ($extends as $class) {
				if (
						!in_array($class, $sortedClasses)
					&&
						!in_array($class, $this->_processedClasses)
					&&
						strstr($class, '_')
				) {
					$sortedClasses[]= $class;
					if ('default' === $scope) {
						$this->_processedClasses[]= $class;
					}
				}
			}
			if (
					!in_array($className, $sortedClasses)
				&&
					!in_array($className, $this->_processedClasses)
			) {
				$sortedClasses[]= $className;
					if ('default' === $scope) {
						$this->_processedClasses[]= $className;
					}
			}
		}
		$classesSource = "<?php\n";
		foreach ($sortedClasses as $className) {
			$file = $this->_includeDir.DS.$className.'.php';
			if (!file_exists($file)) {
				continue;
			}
			$content = file_get_contents($file);
			// Не компилируем закодированные посредством ionCube файлы
			if (
					rm_contains($content, 'ionCube Loader')
				&&
					rm_contains($content, "ioncube\_loader\_")
			) {
				continue;
			}
			// НАЧАЛО ЗАПЛАТКИ
			/** @var string $contentBeforeRemovingBom */
			$contentBeforeRemovingBom = $content;
			df_assert_string($contentBeforeRemovingBom);
			/** @var string $content */
			$content = df_t()->bomRemove($content);
			df_assert_string($content);
			if ($content !== $contentBeforeRemovingBom) {
				Mage::log(rm_sprintf(
					'Российская сборка Magento предотвратила сбой компиляции,'
					. ' удалив недопустимый символ BOM из файла %s.'
					, $file
				));
			}
			$content = ltrim($content, '<?php');
			$content = rtrim($content, "\n\r\t?>");
			$classesSource.=
				rm_sprintf(
					"\n\nif (!class_exists('%s', false) && !(interface_exists('%s', false))) {\n%s\n}"
					,$className
					,$className
					,$content
				)
			;
			// КОНЕЦ ЗАПЛАТКИ
		}
		return $classesSource;
	}

	/** @return array(string => array(string => string)) */
	private function getConfigMap() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getCompileConfig()->getNode('includes')->asArray();
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getScopes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_merge(array_keys($this->getConfigMap()), array_keys($this->getStatFiles()))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getStatFiles() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			if (is_dir($this->_statDir)) {
				$dir = dir($this->_statDir);
				while (false !== ($file = $dir->read())) {
					if ('.' === ($file[0])) {
						continue;
					}
					$result[str_replace('.csv', '', $file)] = $this->_statDir.DS.$file;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}