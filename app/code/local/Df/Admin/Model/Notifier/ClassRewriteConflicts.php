<?php
class Df_Admin_Model_Notifier_ClassRewriteConflicts extends Df_Admin_Model_Notifier {
	/**
	 * @override
	 * @return bool
	 */
	public function needToShow() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result = false;
			if ($this->hasModulesSetBeenChanged()) {
				$result = $this->hasConflicts();
				// Раз набор модулей в системе изменился,
				// то, возможно, появятся некоторые новые предупреждения,
				// о которых администратор ещё не знает,
				// поэтому аннулируем скрытие блока предупреждений.
				$this->resetSkipStatus();
				$this->getCache()->saveData(
					$this->getCacheKeyForModules(), $this->getModulesHashCurrent()
				);
			}
			$this->{__METHOD__} = $result || (parent::needToShow() && $this->hasConflicts());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Admin_Model_Notifier::messageTemplate()
	 * @return string
	 */
	protected function messageTemplate() {
		return implode(array(
			Df_Admin_Block_Notifier_ClassRewriteConflicts::render($this->getConflicts())
			,'[[чем это опасно и как устранить проблему?]]'
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrlHelp() {return 'http://magento-forum.ru/topic/4573/';}

	/** @return Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Cache::i(
				$type = null, $lifetime = Df_Core_Model_Cache::LIFETIME_INFINITE
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getCacheKeyForModules() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getCache()->makeKey(__METHOD__);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Model_ClassRewrite_Collection */
	private function getConflicts() {
		return Df_Admin_Model_ClassRewrite_Finder::s()->getRewrites()->getConflicts();
	}

	/**
	 * 2015-02-06
	 * @used-by getModulesHashCurrent()
	 * @param string $codePool
	 * @return string[]
	 */
	private function getModulesFromCodePool($codePool) {
		/** @var string $path */
		$path = Mage::app()->getConfig()->getOptions()->getCodeDir() . DS . $codePool;
		/**
		 * Например, если в качестве $codePool передано значение «local»,
		 * то $vendors будет содержать подпапки внутри папки «app/code/local»?
		 * например: array('Df', 'Dfa', 'Dfm', 'Dft', 'Portal', 'Utkonos', 'Varien', 'Zend')
		 * @var string[] $vendors
		 */
		$vendors = df_path()->children($path);
		/** @var string[] $result */
		$result = [];
		foreach ($vendors as $vendor) {
			/** @var string $vendor */
			$modules = df_path()->children($path . DS . $vendor);
			foreach ($modules as $module) {
				/** @var string $module */
				$result[]= $vendor . '_' . $module;
			}
		}
		return $result;
	}

	/**
	 * @param string[] $modules
	 * @return string
	 */
	private function getModulesHash(array $modules) {return md5(implode($modules));}

	/** @return string */
	private function getModulesHashCurrent() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2015-02-06
			 * Обратите внимание, что @uses getModulesFromCodePool()
			 * возвращает массив с целочисленными ключами,
			 * и результат применения @uses array_merge() может содержать повторяющиеся элементы.
			 * Однако в данной ситуации мы их намеренно не удаляем,
			 * потому что повторяющиеся элементы
			 * обозначают один и то же модуль в разных областях программного кода,
			 * и наличие одного модуля сразу в двух областях программного кода
			 * должно влиять на результат нашего метода.
			 * На практике это возможно,
			 * когда модули ядра из области программного кода «core» (папка «app/code/core»)
			 * перекрыты одноимёнными модулями из области программного кода «local»
			 * (папка «app/code/local»),
			 * то есть, когда в папке «app/code/local» содержится подпапка «Mage»
			 * с некоторыми классами, перекрывающими системные классы.
			 */
			$this->{__METHOD__} = $this->getModulesHash(array_merge(
				$this->getModulesFromCodePool('local')
				, $this->getModulesFromCodePool('community')
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getModulesHashPrev() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getCache()->loadData($this->getCacheKeyForModules());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function hasConflicts() {return $this->getConflicts()->hasItems();}

	/** @return bool */
	private function hasModulesSetBeenChanged() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getModulesHashCurrent() !== $this->getModulesHashPrev();
		}
		return $this->{__METHOD__};
	}
}