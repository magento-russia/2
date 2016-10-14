<?php
class Df_1C_Config_Api_Product_Prices extends Df_1C_Config_Api_Cml2 {
	/** @return string */
	public function getMain() {return $this->getString('main');}

	/**
	 * @used-by Df_1C_Cml2_Import_Data_Entity_PriceType::getCustomerGroup()
	 * @param string $названиеТиповогоСоглашения
	 * @return Df_Customer_Model_Group|null
	 */
	public function getCustomerGroup($названиеТиповогоСоглашения) {
		return dfa($this->_map(), $названиеТиповогоСоглашения);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/product__prices/';}

	/** @return array(string => Df_Customer_Model_Group) */
	private function _map() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->map(
				'map'
				, Df_1C_Config_MapItem_PriceType::_C
				/** @uses Df_1C_Config_MapItem_PriceType::getCustomerGroup() */
				, 'getCustomerGroup'
				/** @uses Df_1C_Config_MapItem_PriceType::getНазваниеТиповогоСоглашения() */
				, 'getНазваниеТиповогоСоглашения'
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Config_Api_Product_Prices */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}