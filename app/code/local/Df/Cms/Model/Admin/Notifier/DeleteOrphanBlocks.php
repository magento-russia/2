<?php
class Df_Cms_Model_Admin_Notifier_DeleteOrphanBlocks extends Df_Admin_Model_Notifier {
	/**
	 * @override
	 * @return bool
	 */
	public function needToShow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = parent::needToShow() && $this->hasOrphanBlocks();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageTemplate() {
		return Df_Cms_Block_Admin_Notifier_DeleteOrphanBlocks::render($this->getOrphanBlocks());
	}

	/** @return Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Cache::i(
				$type = Mage_Core_Block_Abstract::CACHE_GROUP
				, $lifetime = Df_Core_Model_Cache::LIFETIME_INFINITE
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Cms_Model_Resource_Block_Collection*/
	private function getOrphanBlocks() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Cms_Model_Resource_Block::s()->findOrphanBlocks();
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function hasOrphanBlocks() {
		if (!isset($this->{__METHOD__})) {
			/** @var int|bool $result */
			$result = $this->getCache()->loadData(__METHOD__);
			if (false !== $result) {
				$result = !!$result;
			}
			else {
				$result = !!$this->getOrphanBlocks()->count();
				$this->getCache()->saveData(__METHOD__, (int)$result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}