<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field extends Df_Core_Block_Template {
	/** @return string */
	public function getApplicability() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getConfigValue('applicability');
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

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
			$this->{__METHOD__} =
				rm_sprintf('%s[%s]', $this->getAddress()->getType(), $this->getType())
			;
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLabel() {
		return $this->escapeHtml(df_h()->checkout()->__($this->_getData(self::P__LABEL)));
	}

	/** @return string */
	public function getLabelHtml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_tag(
					'label'
					,df_clean(array(
						'for' => $this->getDomId()
						,'class' => ($this->isRequired() ? 'required' : null)
					))
					,rm_concat_clean(''
						,($this->isRequired() ? '<em>*</em>' : null)
						,$this->getLabel()
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getOrderingInConfig() {return $this->cfg(self::P__ORDERING_IN_CONFIG);}

	/** @return int */
	public function getOrderingWeight() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_int($this->getConfigValue('ordering'));
		}
		return $this->{__METHOD__};
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
				$this->hasData(self::P__TEMPLATE)
				? $this->_getData(self::P__TEMPLATE)
				: $this->getDefaultTemplate()
			)
		;
	}

	/**
	 * public, потому что вызывается через walk
	 * @return string
	 */
	public function getType() {return $this->cfg(self::P__TYPE);}

	/** @return mixed */
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
				(
					Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__NO
				 !==
					$this->getApplicability()
				)
			&&
				$this->checkAuthenticationStatus()
		;
	}

	/** @return bool */
	protected function checkAuthenticationStatus() {
		/** @var bool $result */
		$result =
				(self::P__AUTHENTICATED__ANY === $this->getAuthenticated())
			||
				(
						df_mage()->customer()->isLoggedIn()
					&&
						(self::P__AUTHENTICATED__YES === $this->getAuthenticated())
				)
			||
				(
						!df_mage()->customer()->isLoggedIn()
					&&
						(self::P__AUTHENTICATED__NO === $this->getAuthenticated())
				)
		;
		return $result;
	}

	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address */
	protected function getAddress() {return $this->cfg(self::P__ADDRESS);}

	/**
	 * Кто может видеть данное поле: авторизованные, неавторизованные или все
	 * @return string
	 */
	protected function getAuthenticated() {
		return $this->cfg(self::P__AUTHENTICATED, self::P__AUTHENTICATED__ANY);
	}

	/**
	 * @override
	 * @return string|string[]
	 */
	protected function getCacheKeyParamsAdditional() {return $this->getDomId();}

	/**
	 * Этот метод перекрывается в классе
	 * Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Region_Dropdown
	 * @return string
	 */
	protected function getConfigShortKey() {return $this->getType();}

	/** @return string[] */
	protected function getCssClasses() {
		/** @var string[] $result */
		$result = df_parse_csv($this->cfg(self::P__CSS_CLASSES));
		if ($this->isRequired()) {
			$result[]= 'required-entry';
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

	/**
	 * @param string $paramName
	 * @return string
	 */
	private function getConfigValue($paramName) {
		df_param_string($paramName, 0);
		/** @var string $key */
		$key =
			rm_config_key(
				'df_checkout'
				,implode('_', array($this->getAddress()->getType(), 'field', $paramName))
				,$this->getConfigShortKey()
			)
		;
		/** @var string $result */
		$result = Mage::getStoreConfig($key);
		if (!is_string($result)) {
			df_error('Не могу прочитать значение настройки «%s»', $key);
		}
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CSS_CLASSES, self::V_STRING)
			->_prop(self::P__ORDERING_IN_CONFIG, self::V_INT)
		;
	}
	const _CLASS = __CLASS__;
	const P__ADDRESS = 'address';
	const P__AUTHENTICATED = 'authenticated';
	const P__AUTHENTICATED__ANY = 'any';
	const P__AUTHENTICATED__NO = 'no';
	const P__AUTHENTICATED__YES = 'yes';
	const P__CSS_CLASSES = 'css-classes';
	const P__LABEL = 'label';
	const P__ORDERING_IN_CONFIG = 'ordering_in_config';
	const P__TEMPLATE = 'template';
	/**
	 * Ядро Magento использует поле «type» блоков для своих внутренних целей.
	 * @see Mage_Core_Model_Layout::createBlock():
	 * $block->setType($type);
	 */
	const P__TYPE = 'rm__type';
}