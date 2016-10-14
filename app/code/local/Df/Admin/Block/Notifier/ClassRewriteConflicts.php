<?php
class Df_Admin_Block_Notifier_ClassRewriteConflicts extends Df_Core_Block_Admin {
	/** @return Df_Admin_Model_ClassRewrite_Collection */
	protected function getConflicts() {return $this->cfg(self::$P__CONFLICTS);}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/admin/notifier/class_rewrite_conflicts.phtml';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__CONFLICTS, Df_Admin_Model_ClassRewrite_Collection::_C);
	}
	/** @var string */
	private static $P__CONFLICTS = 'conflicts';
	/**
	 * @param Df_Admin_Model_ClassRewrite_Collection $conflicts
	 * @return string
	 */
	public static function render(Df_Admin_Model_ClassRewrite_Collection $conflicts) {
		return df_render(__CLASS__, array(self::$P__CONFLICTS => $conflicts));
	}
}