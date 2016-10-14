<?php
class Df_Tweaks_Model_Settings_Remove extends Df_Core_Model {
	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromAccount() {return $this->value('remove_from_account');}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromAll() {return $this->value('remove_from_all');}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromCatalogSearchResult() {return $this->value('remove_from_catalog_search_result');}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromCatalogProductList() {return $this->value('remove_from_catalog_product_list');}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromCatalogProductView() {return $this->value('remove_from_catalog_product_view');}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromFrontpage() {return $this->value('remove_from_frontpage');}

	/** @return string */
	private function getSection() {return $this->cfg(self::P__SECTION);}

	/**
	 * @param string $shortKey
	 * @return string
	 */
	private function translateConfigKeyFromShortToFull($shortKey) {
		return df_cc_path('df_tweaks', $this->getSection(), $shortKey);
	}

	/**
	 * @param string $shortKey
	 * @return string
	 */
	private function value($shortKey) {
		/** @var string|null $result */
		$result = Mage::getStoreConfig($this->translateConfigKeyFromShortToFull($shortKey));
		return !is_null($result) ? $result : Df_Admin_Config_Source_RemoveIfEmpty::NO_REMOVE;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__SECTION, RM_V_STRING_NE);
	}
	const _C = __CLASS__;
	const P__SECTION = 'section';
	/**
	 * @static
	 * @param string $section
	 * @return Df_Tweaks_Model_Settings_Remove
	 */
	public static function i($section) {return new self(array(self::P__SECTION => $section));}
}