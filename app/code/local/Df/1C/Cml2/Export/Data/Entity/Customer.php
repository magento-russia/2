<?php
class Df_1C_Cml2_Export_Data_Entity_Customer extends Df_Core_Model {
	/** @return string */
	public function getDateOfBirthAsString() {
		return
			$this->getMagentoCustomer()
			? $this->getMagentoCustomer()->getDateOfBirthAsString(
				Df_1C_Cml2_Export_DocumentMixin::DATE_FORMAT
			  )
			: ''
		;
	}

	/** @return string */
	public function getEmail() {
		return
			$this->getMagentoCustomer()
			? $this->getMagentoCustomer()->getEmail()
			: $this->getMergedAddressWithShippingPriority()->getEmail()
		;
	}

	/** @return string */
	public function getGenderAsString() {
		return
			!$this->getMagentoCustomer()
			? ''
			: df_nts(
				dfa(
					array(
						Df_Customer_Model_Customer::GENDER__FEMALE => 'F'
						,Df_Customer_Model_Customer::GENDER__MALE => 'M'
					)
					,$this->getMagentoCustomer()->getGenderAsString()
				)
			)
		;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getId() {
		return
			!is_null($this->getMagentoCustomer())
			? $this->getMagentoCustomer()->getId()
			: $this->getNameFull()
		;
	}

	/** @return string */
	public function getInn() {
		return
			$this->getMagentoCustomer()
			? $this->getMagentoCustomer()->getInn()
			: ''
		;
	}

	/** @return Df_Sales_Model_Order_Address */
	public function getMergedAddressWithShippingPriority() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Sales_Model_Order_Address::i(df_merge_not_empty(
				$this->getOrder()->getBillingAddress()->getData()
				,$this->getOrder()->getShippingAddress()->getData()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getNameFirst() {
		return
			$this->getMagentoCustomer()
			? df_nts($this->getMergedAddressWithShippingPriority()->getFirstname())
			: ''
		;
	}

	/** @return string */
	public function getNameFull() {
		return
			$this->getMagentoCustomer()
			? $this->getMagentoCustomer()->getName()
			: $this->getMergedAddressWithShippingPriority()->getName()
		;
	}

	/** @return string */
	public function getNameLast() {
		return df_nts(
			$this->getMagentoCustomer()
			? $this->getMagentoCustomer()->getLastname()
			: $this->getMergedAddressWithShippingPriority()->getLastname()
		);
	}

	/** @return string */
	public function getNameMiddle() {
		return df_nts(
			$this->getMagentoCustomer()
			? $this->getMagentoCustomer()->getMiddlename()
			: $this->getMergedAddressWithShippingPriority()->getMiddlename()
		);
	}

	/** @return string */
	public function getNameShort() {return $this->getNameFull();}

	/** @return Df_Customer_Model_Customer|null */
	private function getMagentoCustomer() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				!$this->getOrder()->getCustomerId()
				? null
				: Df_Customer_Model_Customer::ld($this->getOrder()->getCustomerId())
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return Df_Sales_Model_Order */
	private function getOrder() {return $this->cfg(self::$P__ORDER);}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ORDER, Df_Sales_Model_Order::class);
	}
	/** @var string */
	private static $P__ORDER = 'order';
	/**
	 * @static
	 * @param Df_Sales_Model_Order $order
	 * @return Df_1C_Cml2_Export_Data_Entity_Customer
	 */
	public static function i(Df_Sales_Model_Order $order) {return new self(array(self::$P__ORDER => $order));}
}