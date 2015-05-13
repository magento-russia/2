<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Type_Billing
	extends Mage_Checkout_Block_Onepage_Billing {
	//
	// Метод __ перекрывать не нужно
	//
	/**
	 * @override
	 * @return bool
	 */
	public function customerHasAddresses() {return !!$this->getAddresses();}

	/**
	 * @override
	 * @param string $type
	 * @return string
	 */
	public function getAddressesHtmlSelect($type) {
		if ($this->isCustomerLoggedIn()) {
			$options = array();
			foreach (
				// BEGIN PATCH
				$this->getAddresses()
				// END PATCH
				as $address) {
				$options[]= array(
					'value' => $address->getId(),'label' => $address->format('oneline')
				);
			}
			$addressId = $this->getAddress()->getCustomerAddressId();
			if (empty($addressId)) {
				if ($type=='billing') {
					$address = $this->getCustomer()->getPrimaryBillingAddress();
				} else {
					$address = $this->getCustomer()->getPrimaryShippingAddress();
				}
				if ($address) {
					$isAllowed = false;
					foreach ($this->getAddresses() as $allowedAddress) {
						if ($allowedAddress->getId() === $address->getId()) {
							$isAllowed = true;
							break;
						}
					}

					if (!$isAllowed) {
						$address = df_a($this->getAddresses(), 0);
					}

					if ($address) {
						$addressId = $address->getId();
					}

				}
			}

			$select = df_block('core/html_select')
				->setName($type.'_address_id')
				->setId($type.'-address-select')
				->setClass('address-select')
				->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
				->setValue($addressId)
				->setOptions($options);
			$select->addOption('', df_mage()->checkoutHelper()->__('New Address'));
		return $select->getHtml();
		}
		return '';
	}

	/** @return array */
	private function getAddresses() {
		if (!isset($this->{__METHOD__})) {
			/** @var array $result */
			$result = $this->getCustomer()->getAddresses();
			if (
				df_cfg()->checkout()->_interface()->needShowAllStepsAtOnce()
			) {
				$result =
					Df_Checkout_Model_Filter_Ergonomic_Address::i(
						array(
							Df_Checkout_Model_Filter_Ergonomic_Address
								::P__ADDRESS_TYPE => 'billing'
						)
					)->filter($result)
				;
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}