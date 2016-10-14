<?php
class Df_Admin_Block_Notifier_DeleteDemoStore extends Df_Core_Block_Admin {
	/** @return string */
	protected function getLink() {
		return Df_Admin_Model_Action_DeleteDemoStore::getLink($this->store());
	}

	/** @return string */
	protected function getTitle() {return rm_e($this->store()->getName());}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/admin/notifier/delete_demo_store.phtml';}

	/** @return Df_Core_Model_StoreM */
	private function store() {return $this->cfg(self::$P__STORE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__STORE, Df_Core_Model_StoreM::_C);
	}
	/** @var string */
	private static $P__STORE = 'store';
	/**
	 * @param Df_Core_Model_StoreM[] $stores
	 * @return string[]
	 */
	public static function renderA(array $stores) {return array_map(array(__CLASS__, 'render'), $stores);}
	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return string
	 */
	private static function render(Df_Core_Model_StoreM $store) {
		return rm_render(__CLASS__, array(self::$P__STORE => $store));
	}
}