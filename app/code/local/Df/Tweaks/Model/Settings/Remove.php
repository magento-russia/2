<?php
class Df_Tweaks_Model_Settings_Remove extends Df_Core_Model {
	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromAccount() {
		return
			$this->getConfigValue(
				'remove_from_account'
				,Df_Admin_Model_Config_Source_RemoveIfEmpty::VALUE__NO_REMOVE
			)
		;
	}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromAll() {
		return
			$this->getConfigValue(
				'remove_from_all'
				,Df_Admin_Model_Config_Source_RemoveIfEmpty::VALUE__NO_REMOVE
			)
		;
	}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromCatalogSearchResult() {
		return
			$this->getConfigValue(
				'remove_from_catalog_search_result'
				,Df_Admin_Model_Config_Source_RemoveIfEmpty::VALUE__NO_REMOVE
			)
		;
	}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromCatalogProductList() {
		return
			$this->getConfigValue(
				'remove_from_catalog_product_list'
				,Df_Admin_Model_Config_Source_RemoveIfEmpty::VALUE__NO_REMOVE
			)
		;
	}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromCatalogProductView() {
		return
			$this->getConfigValue(
				'remove_from_catalog_product_view'
				,Df_Admin_Model_Config_Source_RemoveIfEmpty::VALUE__NO_REMOVE
			)
		;
	}

	/**
	 * Метод возвращает значение типа string, а не boolean,
	 * потому что допустимых значений три:
	 * «удалить», «не удалять», «удалить, если пуст»
	 * @return string
	 */
	public function removeFromFrontpage() {
		return
			$this->getConfigValue(
				'remove_from_frontpage'
				,Df_Admin_Model_Config_Source_RemoveIfEmpty::VALUE__NO_REMOVE
			)
		;
	}

	/**
	 * @param string $shortKey
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	private function getConfigValue($shortKey, $defaultValue = '') {
		df_param_string($defaultValue, 1);
		/** @var string $result */
		$result = Mage::getStoreConfig($this->translateConfigKeyFromShortToFull($shortKey));
		if (is_null($result)) {
			$result = $defaultValue;
		}
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getSection() {
		return $this->cfg(self::P__SECTION);
	}

	/**
	 * @param string $shortKey
	 * @return string
	 */
	private function translateConfigKeyFromShortToFull($shortKey) {
		return rm_config_key('df_tweaks', $this->getSection(), $shortKey);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__SECTION, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__SECTION = 'section';
	/**
	 * @static
	 * @param string $section
	 * @return Df_Tweaks_Model_Settings_Remove
	 */
	public static function i($section) {return new self(array(self::P__SECTION => $section));}
}