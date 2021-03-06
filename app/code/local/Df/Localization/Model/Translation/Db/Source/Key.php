<?php
class Df_Localization_Model_Translation_Db_Source_Key extends Df_Core_Model_Abstract {
	/** @return string */
	public function getModule() {
		return df_a($this->getSplittedKey(), 0);
	}

	/** @return string */
	public function getString() {
		return df_a($this->getSplittedKey(), 1);
	}

	/** @return string */
	protected function getKey() {
		return $this->cfg(self::P__KEY);
	}

	/** @return string[] */
	protected function getSplittedKey() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = explode('::', $this->getKey());
			df_assert_eq(2, count($this->{__METHOD__}), 'Invalid key');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__KEY, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__KEY = 'key';
	/**
	 * @param string $key
	 * @return Df_Localization_Model_Translation_Db_Source_Key
	 */
	public static function i($key) {return new self(array(self::P__KEY => $key));}
}