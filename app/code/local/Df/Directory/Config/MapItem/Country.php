<?php
class Df_Directory_Config_MapItem_Country extends Df_Admin_Config_MapItem {
	/** @return Df_Directory_Model_Country|null */
	public function getCountry() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(rm_country($this->getIso2(), $throwIfAbsent = false));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	public function getIso2() {return $this->cfg(self::P__ISO2);}

	/**
	 * 2015-02-07
	 * Код ISO отсутствует у псевдоопции «-- выберите страну --».
	 * @see Df_Directory_Block_Field_CountriesOrdered::_construct()
	 * @see Df_Admin_Config_MapItem::isValid()
	 * @override
	 * @return bool
	 */
	public function isValid() {return $this->getIso2() && $this->getCountry();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ISO2, DF_V_ISO2, false);
	}
	/** @used-by Df_Directory_Block_Field_CountriesOrdered::_construct() */
	const P__ISO2 = 'iso2';

	/**
	 * 2015-04-18
	 * Описывает поля структуры данных.
	 * Используется для распаковки значений по умолчанию.
	 * @used-by Df_Admin_Config_Backend_Table::unserialize()
	 * @return string[]
	 */
	public static function fields() {return array(self::P__ISO2);}
}