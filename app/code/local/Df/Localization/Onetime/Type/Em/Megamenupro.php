<?php
class Df_Localization_Onetime_Type_Em_Megamenupro extends Df_Localization_Onetime_Type {
	/**
	 * @override
	 * @return EM_Megamenupro_Model_Mysql4_Megamenupro_Collection
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new EM_Megamenupro_Model_Mysql4_Megamenupro_Collection();
			$this->collectionAfterLoad();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	public function saveModifiedEntities() {
		$this->collectionBeforeSave();
		parent::saveModifiedEntities();
	}

	/** @return void */
	private function collectionBeforeSave() {
		foreach ($this->getAllEntities() as $menu) {
			/** @var EM_Megamenupro_Model_Megamenupro $menu */
			$this->entityBeforeSave($menu);
		}
	}

	/**
	 * @param EM_Megamenupro_Model_Megamenupro $menu
	 * @return void
	 */
	private function entityBeforeSave(EM_Megamenupro_Model_Megamenupro $menu) {
		/** @var array(array(string => mixed)) $content */
		$content = $menu->getData('content');
		if (is_array($content)) {
			foreach ($content as &$item) {
				/** @var array(string => mixed) $item */
				$item['text'] = base64_encode(df_a($item, 'text'));
			}
			$menu->setData('content', serialize($content));
		}
	}

	/** @return void */
	private function collectionAfterLoad() {
		foreach ($this->{__CLASS__ . '::getAllEntities'} as $menu) {
			/** @var EM_Megamenupro_Model_Megamenupro $menu */
			$this->entityAfterLoad($menu);
		}
	}

	/**
	 * @param EM_Megamenupro_Model_Megamenupro $menu
	 * @return void
	 */
	private function entityAfterLoad(EM_Megamenupro_Model_Megamenupro $menu) {
		/** @var string|null $contentSerialized */
		$contentSerialized = $menu->getData('content');
		if ($contentSerialized) {
			/** @var array(array(string => mixed)) $content */
			$content = df_nta(@unserialize($contentSerialized), true);
			if (is_array($content)) {
				foreach ($content as &$item) {
					/** @var array(string => mixed) $item */
					/** @var string $titleEncoded */
					$titleEncoded = df_a($item, 'text');
					if ($titleEncoded) {
						$item['text'] = base64_decode($titleEncoded);
					}
				}
				$menu->setData('content', $content);
			}
		}
		$menu->setDataChanges(false);
	}
}