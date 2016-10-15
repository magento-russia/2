<?php
/**
 * 2015-02-06
 * Этот класс содержит функциональность, общую для классов
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Type_Billing
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Type_Shipping
 * Мы не можем вынести данную функциональность вверх по иерархии наследования,
 * потому что указанные классы имеют разных родителей:
 * @see Mage_Checkout_Block_Onepage_Billing
 * @see Mage_Checkout_Block_Onepage_Shipping
 * По сути, мы переопределяем функциональность, которуэ классы
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Type_Billing
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Type_Shipping
 * заимствуют от своего общего предка-дедушки @see Mage_Checkout_Block_Onepage_Abstract.
 */
class Df_Checkout_Block_Frontend_Ergonomic_Address_HtmlSelect extends Df_Core_Block_Abstract_NoCache {
	/** @return bool */
	public function hasAddresses() {return !!$this->getTypeAddresses();}

	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {return df_render($this->getSelect());}

	/**
	 * @override
	 * @see Df_Core_Block_Abstract::needToShow()
	 * @used-by Df_Core_Block_Abstract::_loadCache()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return bool
	 */
	protected function needToShow() {return rm_customer_logged_in();}

	/**
	 * 2015-02-06
	 * Раньше реализация была такой:
			mb_strtolower(df_last(rm_explode_class(get_class($this->getOwner()))))
	 * В принципе, она корректна, однако зависит от «магии» — имени класса, использующего наш класс.
	 * Мало ли, какой рефакторинг потом произойдёт и как имена классов изменятся.
	 * @return string
	 */
	private function getAddressType() {return $this->cfg(self::$P__ADDRESS_TYPE);}

	/** @return Df_Customer_Model_Customer */
	private function getCustomer() {return $this->getOwner()->getCustomer();}

	/** @return array(array(string => string)) */
	private function getOptions() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => string)) $result */
			$result = array();
			/**
			 * 2015-01-31
			 * В оригинальном методе
			 * @see Mage_Checkout_Block_Onepage_Abstract::getAddressesHtmlSelect()
			 * цикл выглядит так:
			 * foreach ($this->getCustomer()->getAddresses() as $address) {
			 */
			foreach ($this->getTypeAddresses() as $address) {
				/** @var Df_Customer_Model_Address $address */
				$result[]= rm_option($address->getId(), $address->format('oneline'));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Checkout_Block_Onepage_Abstract|Mage_Checkout_Block_Onepage_Billing|Mage_Checkout_Block_Onepage_Shipping */
	private function getOwner() {return $this->cfg(self::$P__OWNER);}

	/** @return Df_Customer_Model_Address|null */
	private function getPrimaryAddress() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Методы
			 * @uses Mage_Customer_Model_Address::getPrimaryBillingAddress()
			 * @uses Mage_Customer_Model_Address::getPrimaryShippingAddress()
			 * используют метод @uses Mage_Customer_Model_Address::getPrimaryAddress(),
			 * который может вернуть false.
			 * По этой причине преобразуем результат посредством @uses df_ftn()
			 */
			$this->{__METHOD__} = df_n_set(df_ftn(
				'billing' === $this->getAddressType()
				? $this->getCustomer()->getPrimaryBillingAddress()
				: $this->getCustomer()->getPrimaryShippingAddress()
			));
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return Mage_Sales_Model_Quote_Address */
	private function getQuoteAddress() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2015-02-06
			 * @var Mage_Sales_Model_Quote_Address $address
			 * Метод getAddress почему-то отсутствует в классе
			 * @see Mage_Checkout_Block_Onepage_Abstract,
			 * однако присутствует и публичен у обоих его потомков:
			 * @uses Mage_Checkout_Block_Onepage_Billing::getAddress()
			 * @uses Mage_Checkout_Block_Onepage_Shipping::getAddress()
			 */
			$this->{__METHOD__} = $this->getOwner()->getAddress();
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Block_Html_Select */
	private function getSelect() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Block_Html_Select $result */
			$result = new Mage_Core_Block_Html_Select(array(
				'name' => $this->getAddressType() . '_address_id'
				,'value' => $this->getSelectedAddressId()
				,'extra_params' => strtr('onchange="{instance}.newAddress(!this.value);"', array(
					'{instance}' => $this->getAddressType()
				))
			));
			$result->setId($this->getAddressType() . '-address-select');
			$result->setClass('address-select');
			$result->setOptions($this->getOptions());
			$result->addOption('', df_mage()->checkoutHelper()->__('New Address'));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int|null */
	private function getSelectedAddressId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = $this->getQuoteAddress()->getCustomerAddressId();
			if (!$result && $this->getPrimaryAddress() && $this->hasAddresses()) {
				/** @var Df_Customer_Model_Address $address */
				$address = dfa(
					$this->getTypeAddresses()
					, $this->getPrimaryAddress()->getId()
					, df_first($this->getTypeAddresses())
				);
				$result = $address->getId();
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * Возвращает адреса типа, соответсвующего типу,
	 * заданному в конструкторе данного класса@see _construct().
	 * @return array(int => Df_Customer_Model_Address)
	 */
	private function getTypeAddresses() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => Df_Customer_Model_Address) $result */
			$result = $this->getCustomer()->getAddresses();
			if (rm_checkout_ergonomic()) {
				/** @var Df_Checkout_Model_Filter_Ergonomic_Address $filter */
				$filter = Df_Checkout_Model_Filter_Ergonomic_Address::i($this->getAddressType());
				$result = $filter->filter($result);
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
		$this
			->_prop(self::$P__ADDRESS_TYPE, DF_V_STRING_NE)
			->_prop(self::$P__OWNER, 'Mage_Checkout_Block_Onepage_Abstract')
		;
	}
	/** @var string */
	private static $P__ADDRESS_TYPE = 'address_type';
	/** @var string */
	private static $P__OWNER = 'owner';

	/**
	 * @param Mage_Checkout_Block_Onepage_Abstract $owner
	 * @param string $addressType
	 * @return Df_Checkout_Block_Frontend_Ergonomic_Address_HtmlSelect
	 */
	public static function i(Mage_Checkout_Block_Onepage_Abstract $owner, $addressType) {
		df_param_string_not_empty($addressType, 1);
		return new self(array(self::$P__OWNER => $owner, self::$P__ADDRESS_TYPE => $addressType));
	}
}