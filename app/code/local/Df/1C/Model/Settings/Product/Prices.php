<?php
class Df_1C_Model_Settings_Product_Prices extends Df_1C_Model_Settings_Cml2 {
	/** @return string */
	public function getMain() {return $this->getString('df_1c/product__prices/main');}

	/** @return array(int => string) */
	public function getMapFromCustomerGroupIdToPriceTypeName() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => string) $result  */
			$result = array();
			/** @var string|null $mapSerialized */
			$mapSerialized = $this->getStringNullable('df_1c/product__prices/map');
			if ($mapSerialized) {
				df_assert_string_not_empty($mapSerialized);
				/** @var array[] $map */
				$map = @unserialize($mapSerialized);
				if (is_array($map)) {
					foreach ($map as $mapItem) {
						/** @var string[] $mapItem */
						df_assert_array($mapItem);
						/** @var int $customerGroup */
						$customerGroup =
							rm_nat0(
								df_a(
									$mapItem
									,Df_1C_Block_System_Config_Form_Field_MapFromCustomerGroupToPriceType
										::COLUMN__CUSTOMER_GROUP
								)
							)
						;
						/** @var string $priceType */
						$priceType =
							df_nts(
								df_a(
									$mapItem
									,Df_1C_Block_System_Config_Form_Field_MapFromCustomerGroupToPriceType
										::COLUMN__PRICE_TYPE
								)
							)
						;
						if ((0 !== $customerGroup) && $priceType) {
							$result[$customerGroup] = $priceType;
						}
					}
				}
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => Mage_Customer_Model_Group|null) */
	public function getMapFromPriceTypeNameToCustomerGroup() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Customer_Model_Resource_Group_Collection $customerGroups */
			$customerGroups = Mage::getResourceModel('customer/group_collection');
			/** @var array(string => Mage_Customer_Model_Group|null) $result  */
			$result = array();
			foreach ($this->getMapFromPriceTypeNameToCustomerGroupId()
					 as $priceTypeName => $customerGroupId) {
				/** @var string $priceTypeName */
				/** @var int $customerGroupId */
				$result[$priceTypeName] = $customerGroups->getItemById($customerGroupId);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => int) */
	public function getMapFromPriceTypeNameToCustomerGroupId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_flip($this->getMapFromCustomerGroupIdToPriceTypeName());
			/** При сбое @see array_flip может вернуть null */
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Settings_Product_Prices */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}