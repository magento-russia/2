<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address extends Df_Core_Block_Abstract_NoCache {
	/**
	 * Этот метод используется только полями
	 * (объектами класса Df_Checkout_Block_Frontend_Ergonomic_Address_Field),
	 * которые создаёт данный класс
	 * @return Mage_Sales_Model_Quote_Address
	 */
	public function getAddress() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Sales_Model_Quote_Address $result */
			$result = null;
			if (
					!df_mage()->customer()->isLoggedIn()
				&&
					df_cfg()->checkout()->other()->canGetAddressFromYandexMarket()
			) {
				$result = $this->getAddressFromYandexMarket();
			}
			if (is_null($result)) {
				$result =
					df_mage()->customer()->isLoggedIn()
					? (
						(self::TYPE__BILLING === $this->getType())
						? $this->getSession()->getQuote()->getBillingAddress()
						: $this->getSession()->getQuote()->getShippingAddress()
					)
					: Df_Sales_Model_Quote_Address::i()
				;
			}
			df_assert($result instanceof Mage_Sales_Model_Quote_Address);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод публичен, потому что его использует
	 * класс Df_Checkout_Block_Frontend_Ergonomic_Address_Field
	 * @return string
	 */
	public function getType() {return $this->cfg(self::P__TYPE);}

	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		$result = '';
		try {
			$result = implode("\n", $this->getRows()->walk('toHtml'));
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $result;
	}

	/** @return Df_Sales_Model_Quote_Address|null */
	private function getAddressFromYandexMarket() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => array(string => string))|null $addressesDataFromYandexMarket */
			$addressesDataFromYandexMarket =
				$this->getSession()->getData(
					Df_YandexMarket_Model_Action_ImportAddress::SESSION__ADDRESSES_FROM_YANDEX_MARKET
				)
			;
			if (!is_array($addressesDataFromYandexMarket)) {
				$addressesDataFromYandexMarket = array();
			}
			/** @var array(string => string))|null $address */
			$address = df_a($addressesDataFromYandexMarket, $this->getType());
			$this->{__METHOD__} = rm_n_set(
				!$address ? null : Df_Sales_Model_Quote_Address::i($address)
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Field */
	private function getFields() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Checkout_Model_Collection_Ergonomic_Address_Field $result */
			$result = Df_Checkout_Model_Collection_Ergonomic_Address_Field::i();
			/** @var int $orderingInConfig */
			$orderingInConfig = 1;
			/** @var mixed[] $nodeAsArray */
			$nodeAsArray = $this->getFieldsConfig()->getNode()->asCanonicalArray();
			/**
			 * Varien_Simplexml_Element::asCanonicalArray может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_assert_array($nodeAsArray);
			foreach ($nodeAsArray as $fieldType => $fieldConfig) {
				/** @var string $fieldType */
				df_assert_string($fieldType);
				/** @var array $fieldConfig */
				df_assert_array($fieldConfig);
				/** @var Df_Checkout_Block_Frontend_Ergonomic_Address_Field $block */
				$block = null;
				try {
					$block =
						df_block(
							df_a($fieldConfig, 'block')
							,null
							,array_merge(
								$fieldConfig
								,array(
									Df_Checkout_Block_Frontend_Ergonomic_Address_Field
										::P__TYPE => $fieldType
									,Df_Checkout_Block_Frontend_Ergonomic_Address_Field
										::P__ADDRESS => $this
									,Df_Checkout_Block_Frontend_Ergonomic_Address_Field
										::P__ORDERING_IN_CONFIG => $orderingInConfig++
								)
							)

						)
					;
				}
				catch(Exception $e) {
					df_error('Не найден класс блока: %s' ,df_a($fieldConfig, 'block'));
				}
				$result->addItem($block);
			}
			$this->{__METHOD__} = $this->getFilter()->filter($result);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Checkout_Model_Config_Query_Ergonomic_Address_Fields */
	private function getFieldsConfig() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Checkout_Model_Config_Query_Ergonomic_Address_Fields::i($this->getType())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Filter */
	private function getFilter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Filter();
			$this->{__METHOD__}
				->addFilter(Df_Checkout_Model_Filter_Ergonomic_Address_Field_Collection_ByVisibility::i())
				->addFilter(Df_Checkout_Model_Filter_Ergonomic_Address_Field_Collection_Order_ByWeight::i())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Row */
	private function getRows() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Checkout_Model_Collection_Ergonomic_Address_Row $result */
			$result = Df_Checkout_Model_Collection_Ergonomic_Address_Row::i();
			/** @var int|null $previousWeight */
			$previousWeight = null;
			/** @var Df_Checkout_Block_Frontend_Ergonomic_Address_Row $currentRow */
			$currentRow = null;
			foreach ($this->getFields() as $field) {
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
					$row = Df_Checkout_Block_Frontend_Ergonomic_Address_Row::i();
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

	/** @return Mage_Checkout_Model_Session */
	private function getSession() {return rm_session_checkout();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__TYPE, Df_Zf_Validate_String_NotEmpty::s());
	}
	const _CLASS = __CLASS__;
	/**
	 * Ядро Magento использует поле «type» блоков для своих внутренних целей.
	 * @see Mage_Core_Model_Layout::createBlock():
	 * $block->setType($type);
	 */
	const P__TYPE = 'rm__type';
	const TYPE__BILLING = 'billing';
	const TYPE__SHIPPING = 'shipping';
	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address */
	public static function billing() {
		return self::i(self::TYPE__BILLING);
	}
	/**
	 * @param string $type
	 * @return Df_Checkout_Block_Frontend_Ergonomic_Address
	 */
	public static function i($type) {
		df_param_string_not_empty($type, 0);
		return df_block(new self(array(self::P__TYPE => $type)));
	}
	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address */
	public static function shipping() {
		return self::i(self::TYPE__SHIPPING);
	}
}