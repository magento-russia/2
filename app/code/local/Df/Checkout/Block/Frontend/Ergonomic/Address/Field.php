<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field extends Df_Core_Block_Template {
	/** @return string */
	public function getApplicability() {return $this->getConfigValue('applicability');}

	/** @return string */
	public function getCssClassesAsText() {
		return df_output()->getCssClassesAsString($this->getCssClasses());
	}

	/** @return string */
	public function getDomId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				implode(':', array($this->getAddress()->getType(), $this->getType()))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getDomName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = sprintf('%s[%s]', $this->getAddress()->getType(), $this->getType());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLabel() {return df_e(df_h()->checkout()->__($this['label']));}

	/** @return string */
	public function getLabelHtml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_tag(
					'label'
					,array_filter(array(
						'for' => $this->getDomId()
						,'class' => ($this->isRequired() ? 'required' : null)
					))
					,df_ccc(''
						,($this->isRequired() ? '<em>*</em>' : null)
						,$this->getLabel()
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getOrderingInConfig() {return $this[self::$P__ORDERING];}

	/** @return int */
	public function getOrderingWeight() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_nat0($this->getConfigValue('ordering'));
		}
	}

	/**
	 * @override
	 * @return string|null
	 */
	public function getTemplate() {
		return
			!$this->needToShow()
			? null
			: (
				// Разработчик может указать шаблон поля в настроечном файле XML.
				// Например:
				// <customer_password>
				// 		<template>df/checkout/ergonomic/address/field/password.phtml</template>
				// </customer_password>
				$this->hasData(self::$P__TEMPLATE)
				? $this->_getData(self::$P__TEMPLATE)
				: $this->defaultTemplate()
			)
		;
	}

	/**
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Row::_toHtml()
	 * @used-by Varien_Data_Collection::walk()
	 * @return string
	 */
	public function getType() {return $this[self::$P__TYPE];}

	/** @return string|null */
	public function getValue() {
		return $this->getAddress()->getAddress()->getDataUsingMethod($this->getType());
	}

	/** @return bool */
	public function isRequired() {
		return
				Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
			===
				$this->getApplicability()
		;
	}

	/**
	 * @override
	 * @return bool
	 */
	public function needToShow() {
		return
				parent::needToShow()
			&&
					Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__NO
				 !==
					$this->getApplicability()
			&&
				$this->checkAuthenticationStatus()
		;
	}

	/** @return bool */
	protected function checkAuthenticationStatus() {
		/** @var bool $result */
		$result =
				(self::$ANY === $this->getAuthenticated())
			||
				(
						df_customer_logged_in()
					&&
						(self::$YES === $this->getAuthenticated())
				)
			||
				(
						!df_customer_logged_in()
					&&
						(self::$NO === $this->getAuthenticated())
				)
		;
		return $result;
	}

	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address */
	protected function getAddress() {return $this[self::$P__ADDRESS];}

	/**
	 * Кто может видеть данное поле: авторизованные, неавторизованные или все
	 * @return string
	 */
	protected function getAuthenticated() {return $this->cfg('authenticated', self::$ANY);}

	/**
	 * @override
	 * @see Df_Core_Block_Template::cacheKeySuffix()
	 * @used-by Df_Core_Block_Template::getCacheKeyInfo()
	 * @return string|string[]
	 */
	protected function cacheKeySuffix() {return $this->getDomId();}

	/**
	 * Этот метод перекрывается в классе
	 * @see Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Region_Dropdown
	 * @used-by getConfigValue()
	 * @return string
	 */
	protected function getConfigShortKey() {return $this->getType();}

	/**
	 * @used-by getCssClassesAsText()
	 * @return string[]
	 */
	protected function getCssClasses() {
		/** @var string[] $result */
		$result = df_csv_parse($this[self::$P__CSS_CLASSES]);
		if ($this->isRequired()) {
			$result[]= $this->getValidatorCssClass();
		}
		/**
		 * 2015-02-15
		 * Добавил класс «rm-checkout-input», чтобы отличать основные поля,
		 * которые содержат данные, передаваемые на сервер,
		 * от динамически создаваемых посредством JavaScript вспомогательных полей.
		 * @used-by rm.customer.Address::getFields()
		 */
		$result[]= 'rm-checkout-input';
		return $result;
	}

	/** @return string */
	protected function getValidatorCssClass() {return 'required-entry';}

	/**
	 * @used-by getApplicability()
	 * @used-by getOrderingWeight()
	 * @param string $paramName
	 * @return string
	 */
	private function getConfigValue($paramName) {
		df_param_string_not_empty($paramName, 0);
		if (!isset($this->{__METHOD__}[$paramName])) {
			/** @var string $fieldType */
			$fieldType = $this->getConfigShortKey();
			df_assert_string_not_empty($fieldType);
			/** @var string $key */
			$key = "df_checkout/{$this->getAddress()->getType()}_field_{$paramName}/{$fieldType}";
			/** @var string $result */
			$result = Mage::getStoreConfig($key);
			if (!is_string($result)) {
				df_error('Не могу прочитать значение настройки «%s»', $key);
			}
			$this->{__METHOD__}[$paramName] = $result;
		}
		return $this->{__METHOD__}[$paramName];
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__CSS_CLASSES, DF_V_STRING)
			->_prop(self::$P__ORDERING, DF_V_NAT0)
			->_prop(self::$P__TYPE, DF_V_STRING_NE)
		;
	}
	/** @var string */
	private static $ANY = 'any';
	/** @var string */
	private static $NO = 'no';
	/** @var string */
	private static $P__ADDRESS = 'address';
	/** @var string */
	private static $P__CSS_CLASSES = 'css-classes';
	/** @var string */
	private static $P__ORDERING = 'ordering_in_config';
	/** @var string */
	private static $P__TEMPLATE = 'template';
	/**
	 * Ядро Magento использует поле «type» блоков для своих внутренних целей.
	 * @see Mage_Core_Model_Layout::createBlock():
	 * $block->setType($type);
	 * Поэтому называем наше поле «df__type».
	 * @var string
	 */
	private static $P__TYPE = 'df__type';
	/** @var string */
	private static $YES = 'yes';

	/**
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field::fields()
	 * @param string $class
	 * @param Df_Checkout_Block_Frontend_Ergonomic_Address $address
	 * @param string $type
	 * @param int $ordering
	 * @param array(string => string) $additional [optional]
	 * @return Df_Checkout_Block_Frontend_Ergonomic_Address_Field
	 */
	public static function ic(
		$class
		, Df_Checkout_Block_Frontend_Ergonomic_Address $address
		, $type
		, $ordering
		, array $additional = array()
	) {
		return df_ic($class, __CLASS__, array(
			self::$P__ADDRESS => $address, self::$P__TYPE => $type, self::$P__ORDERING => $ordering
		) + $additional);
	}
}