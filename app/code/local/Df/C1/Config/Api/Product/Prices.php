<?php
class Df_C1_Config_Api_Product_Prices extends Df_C1_Config_Api_Cml2 {
	/** @return string */
	public function getMain() {return $this->v('main');}

	/**
	 * @used-by Df_C1_Cml2_Import_Data_Entity_PriceType::getCustomerGroup()
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
				, Df_C1_Config_MapItem_PriceType::class
				/** @uses Df_C1_Config_MapItem_PriceType::getCustomerGroup() */
				, 'getCustomerGroup'
				/** @uses Df_C1_Config_MapItem_PriceType::getНазваниеТиповогоСоглашения() */
				, 'getНазваниеТиповогоСоглашения'
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_C1_Config_Api_Product_Prices */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}