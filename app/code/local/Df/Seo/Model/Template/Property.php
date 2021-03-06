<?php
abstract class Df_Seo_Model_Template_Property extends Df_Core_Model_Abstract {
	/** @return string */
	abstract public function getValue();

	/** @return Df_Seo_Model_Template_Adapter */
	public function getAdapter() {
		return $this->cfg(self::P__ADAPTER);
	}

	/** @return string */
	public function getName() {
		return $this->cfg(self::P__NAME);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ADAPTER, Df_Seo_Model_Template_Adapter::_CLASS)
			->_prop(self::P__NAME, self::V_STRING_NE)
		;
	}

	const P__ADAPTER = 'adapter';
	const P__NAME = 'name';
	const _CLASS = __CLASS__;

}