<?php
namespace Df\C1\Cml2\Export\Data\Entity;
use Df\C1\Cml2\Export\DocumentMixin as Mixin;
use Df_Customer_Model_Customer as C;
use Df_Sales_Model_Order as O;
class Customer extends \Df_Core_Model {
	/** @return string */
	public function getDateOfBirthAsString() {return
		$this->getMagentoCustomer()
		? $this->getMagentoCustomer()->getDateOfBirthAsString(Mixin::DATE_FORMAT)
		: ''
	;}

	/** @return string */
	public function getEmail() {return
		$this->getMagentoCustomer()
		? $this->getMagentoCustomer()->getEmail()
		: $this->getMergedAddressWithShippingPriority()->getEmail()
	;}

	/** @return string */
	public function getGenderAsString() {return
		!$this->getMagentoCustomer() ? '' :
			df_nts(dfa(
				[C::GENDER__FEMALE => 'F', C::GENDER__MALE => 'M']
				,$this->getMagentoCustomer()->getGenderAsString()
			))
	;}

	/**
	 * @override
	 * @return string
	 */
	public function getId() {return
		!is_null($this->getMagentoCustomer())
		? $this->getMagentoCustomer()->getId()
		: $this->getNameFull()
	;}

	/** @return string */
	public function getInn() {return
		$this->getMagentoCustomer()
		? $this->getMagentoCustomer()->getInn()
		: ''
	;}

	/** @return \Df_Sales_Model_Order_Address */
	public function getMergedAddressWithShippingPriority() {return dfc($this, function() {return
		\Df_Sales_Model_Order_Address::i(df_merge_not_empty(
			$this->getOrder()->getBillingAddress()->getData()
			,$this->getOrder()->getShippingAddress()->getData()
		))
	;});}

	/** @return string */
	public function getNameFirst() {return
		$this->getMagentoCustomer()
		? df_nts($this->getMergedAddressWithShippingPriority()->getFirstname())
		: ''
	;}

	/** @return string */
	public function getNameFull() {return
		$this->getMagentoCustomer()
		? $this->getMagentoCustomer()->getName()
		: $this->getMergedAddressWithShippingPriority()->getName()
	;}

	/** @return string */
	public function getNameLast() {return
		df_nts(
			$this->getMagentoCustomer()
			? $this->getMagentoCustomer()->getLastname()
			: $this->getMergedAddressWithShippingPriority()->getLastname()
		)
	;}

	/** @return string */
	public function getNameMiddle() {return
		df_nts(
			$this->getMagentoCustomer()
			? $this->getMagentoCustomer()->getMiddlename()
			: $this->getMergedAddressWithShippingPriority()->getMiddlename()
		)
	;}

	/** @return string */
	public function getNameShort() {return $this->getNameFull();}

	/** @return C|null */
	private function getMagentoCustomer() {return dfc($this, function() {return
		!$this->getOrder()->getCustomerId()
		? null
		: C::ld($this->getOrder()->getCustomerId())
	;});}

	/** @return O */
	private function getOrder() {return $this->cfg(self::$P__ORDER);}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ORDER, O::class);
	}
	/** @var string */
	private static $P__ORDER = 'order';
	/**
	 * @static
	 * @param O $order
	 * @return \Df\C1\Cml2\Export\Data\Entity\Customer
	 */
	public static function i(O $order) {return new self(array(self::$P__ORDER => $order));}
}