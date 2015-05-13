<?php
class Df_Admin_Block_Notifier_DeleteDemoStore extends Df_Core_Block_Admin {
	/** @return string */
	protected function getLink() {
		return Df_Admin_Model_Action_DeleteDemoStore::s()->getLink($this->getStore());
	}

	/** @return string */
	protected function getTitle() {return df_escape($this->getStore()->getName());}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/admin/notifier/delete_demo_store.phtml';}

	/** @return Mage_Core_Model_Store */
	private function getStore() {return $this->cfg(self::$P__STORE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__STORE, 'Mage_Core_Model_Store');
	}
	/** @var string */
	private static $P__STORE = 'store';
	/**
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public static function render(Mage_Core_Model_Store $store) {
		return df_block_render(__CLASS__, '', array(self::$P__STORE => $store));
	}
}