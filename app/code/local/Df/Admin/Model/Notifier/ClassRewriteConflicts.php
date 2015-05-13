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
				/**
				 * Раз набор модулей в системе изменился,
				 * то, возможно, появятся некоторые новые предупреждения,
				 * о которых администратор ещё не знает,
				 * поэтому аннулируем скрытие блока предупреждений.
				 */
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
	 * @return string
	 */
	protected function getMessageTemplate() {
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
	 * @param string $codePool
	 * @return string[]
	 */
	private function getModulesFromCodePool($codePool) {
		return
			array_diff(
				scandir(Mage::app()->getConfig()->getOptions()->getCodeDir().DS.$codePool)
				, array('..', '.')
			)
		;
	}

	/**
	 * @param string[] $modules
	 * @return string
	 */
	private function getModulesHash(array $modules) {return md5(implode($modules));}

	/** @return string */
	private function getModulesHashCurrent() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getModulesHash(
					array_merge(
						$this->getModulesFromCodePool('local')
						, $this->getModulesFromCodePool('community')
					)
				)
			;
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
	private function hasConflicts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = (0 < count($this->getConflicts()));
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function hasModulesSetBeenChanged() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getModulesHashCurrent() !== $this->getModulesHashPrev();
		}
		return $this->{__METHOD__};
	}
}