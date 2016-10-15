<?php
abstract class Df_Admin_Config_Backend_Validator_Strategy extends Df_Core_Model {
	/**
	 * @abstract
	 * @return bool
	 */
	abstract public function validate();

	/** @return Df_Admin_Config_Backend_Validator */
	protected function getBackend() {return $this->cfg(self::$P__BACKEND);}

	/**
	 * @used-by Df_Shipping_Config_Backend_Validator_Strategy_Origin::getShippingOriginParam()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return $this->cfg(self::$P__STORE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__BACKEND, Df_Admin_Config_Backend::class)
			->_prop(self::$P__STORE, Df_Core_Model_StoreM::class)
		;
	}
	/** @var string */
	private static $P__BACKEND = 'backend';
	/** @var string */
	private static $P__STORE = 'store';

	/**
	 * @used-by Df_Admin_Config_Backend_Validator::validateForStore()
	 * @param string $class
	 * @param Df_Admin_Config_Backend $backend
	 * @param Df_Core_Model_StoreM $store
	 * @return Df_Admin_Config_Backend_Validator_Strategy
	 */
	public static function ic($class, Df_Admin_Config_Backend $backend, Df_Core_Model_StoreM $store) {
		return df_ic($class, __CLASS__, array(self::$P__BACKEND => $backend, self::$P__STORE => $store));
	}
}