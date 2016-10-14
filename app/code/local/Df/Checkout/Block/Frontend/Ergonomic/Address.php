<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address extends Df_Core_Block_Abstract_NoCache {
	/**
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getValue()
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Country::getValue()
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Street::getValueForStreetLine()
	 * @return Df_Sales_Model_Quote_Address
	 */
	public function getAddress() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Sales_Model_Quote_Address $result */
			$result = null;
			if (!rm_customer_logged_in()
				&& df_cfg()->checkout()->other()->canGetAddressFromYandexMarket()
			) {
				$result = $this->addressFromYandexMarket();
			}
			if (!$result) {
				$result =
					rm_customer_logged_in()
					? ($this->isBilling() ? rm_quote_address_billing() : rm_quote_address_shipping())
					: Df_Sales_Model_Quote_Address::i()
				;
			}
			df_assert($result instanceof Df_Sales_Model_Quote_Address);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getDomId()
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getDomName()
	 * @return string
	 */
	public function getType() {return $this[self::$P__TYPE];}

	/** @return bool */
	public function isBilling() {return self::TYPE__BILLING === $this->getType();}

	/** @return bool */
	public function isShipping() {return self::TYPE__SHIPPING === $this->getType();}

	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		try {
			$result = df_cc_n($this->rows()->walk('toHtml'));
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $result;
	}

	/** @return Df_Sales_Model_Quote_Address|null */
	private function addressFromYandexMarket() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string))|null $address */
			$address = Df_YandexMarket_AddressSession::get($this->getType());
			$this->{__METHOD__} = rm_n_set(!$address ? null : Df_Sales_Model_Quote_Address::i($address));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Field */
	private function fields() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Checkout_Model_Collection_Ergonomic_Address_Field $result */
			$result = Df_Checkout_Model_Collection_Ergonomic_Address_Field::i();
			/** @var int $orderingInConfig */
			$orderingInConfig = 1;
			/** @var mixed[] $nodeAsArray */
			$nodeAsArray = $this->fieldsConfig()->getNode()->asCanonicalArray();
			/**
			 * @uses Varien_Simplexml_Element::asCanonicalArray() может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_assert_array($nodeAsArray);
			foreach ($nodeAsArray as $fieldType => $fieldConfig) {
				/** @var string $fieldType */
				df_assert_string($fieldType);
				/** @var array $fieldConfig */
				df_assert_array($fieldConfig);
				$result->addItem(Df_Checkout_Block_Frontend_Ergonomic_Address_Field::ic(
					dfa($fieldConfig, 'block'), $this, $fieldType, $orderingInConfig++, $fieldConfig
				));
			}
			$result->removeHidden();
			$result->orderByWeight();
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Checkout_Model_Config_Query_Ergonomic_Address_Fields */
	private function fieldsConfig() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Checkout_Model_Config_Query_Ergonomic_Address_Fields::i($this->getType())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Row */
	private function rows() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Checkout_Model_Collection_Ergonomic_Address_Row $result */
			$result = Df_Checkout_Model_Collection_Ergonomic_Address_Row::i();
			/** @var int|null $previousWeight */
			$previousWeight = null;
			/** @var Df_Checkout_Block_Frontend_Ergonomic_Address_Row $currentRow */
			$currentRow = null;
			foreach ($this->fields() as $field) {
				/** @var Df_Checkout_Block_Frontend_Ergonomic_Address_Field $field */
				if (
						is_null($previousWeight)
					||
						 /**
						  * Мы пользуемся тем обстоятельством, что поля уже упорядочены по весу.
						  * Поэтому, если вес предыдущего и текущего полей различен — это значит,
						  * что для нового поля нужно добавить новую строку.
						  */
						($previousWeight != $field->getOrderingWeight())
				) {
					/** @var Df_Checkout_Block_Frontend_Ergonomic_Address_Row $row */
					$row = new Df_Checkout_Block_Frontend_Ergonomic_Address_Row;
					$row->getFields()->addItem($field);
					$result->addItem($row);
					$currentRow = $row;
				}
				else {
					df_assert($currentRow instanceof Df_Checkout_Block_Frontend_Ergonomic_Address_Row);
					$currentRow->getFields()->addItem($field);
				}
				$previousWeight = $field->getOrderingWeight();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__TYPE, Df_Zf_Validate_String_NotEmpty::s());
	}
	/**
	 * Ядро Magento использует поле «type» блоков для своих внутренних целей.
	 * @see Mage_Core_Model_Layout::createBlock():
	 * $block->setType($type);
	 * Поэтому называем наше поле «rm__type».
	 * @var string
	 */
	private static $P__TYPE = 'rm__type';
	/** @used-by Df_YandexMarket_Model_Action_ImportAddress::getAddressType() */
	const TYPE__BILLING = 'billing';
	/** @used-by Df_YandexMarket_Model_Action_ImportAddress::getAddressType() */
	const TYPE__SHIPPING = 'shipping';

	/**
	 * @used-by df/checkout/ergonomic/address/billing.phtml
	 * @return string
	 */
	public static function renderBilling() {return self::r(self::TYPE__BILLING);}

	/**
	 * @used-by df/checkout/ergonomic/address/shipping.phtml
	 * @return string
	 */
	public static function renderShipping() {return self::r(self::TYPE__SHIPPING);}

	/**
	 * @used-by renderBilling()
	 * @used-by renderShipping()
	 * @param string $type
	 * @return string
	 */
	private static function r($type) {return df_render(new self(array(self::$P__TYPE => $type)));}
}