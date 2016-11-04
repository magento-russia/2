<?php
class Df_Core_Model_Mixin extends Df_Core_Model {
	/**
	 * @param string $method
	 * @return mixed
	 */
	protected function parent($method) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		return $this->getParent()->callByMixin($arguments);
	}

	/** @return Df_Core_Model */
	private function getParent() {return $this->cfg(self::$P__PARENT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PARENT, 'Df_Core_Model');
	}
	/** @used-by Df_Core_Model::createMixin() */

	/** @var string */
	private static $P__PARENT = 'parent';

	/**
	 * @used-by \Df\C1\Cml2\Export\DocumentMixin::i()
	 * @used-by \Df\C1\Cml2\Export\DocumentMixin\Catalog::i()
	 * @param string $class
	 * @param Df_Core_Model $parent
	 * @return Df_Core_Model_Mixin
	 */
	public static function ic($class, Df_Core_Model $parent) {
		return df_ic($class, __CLASS__, array(self::$P__PARENT => $parent));
	}
}