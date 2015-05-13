<?php
class Df_Customer_Model_Attribute_ApplicabilityAdjuster extends Df_Core_Model_Abstract {
	/** @return Df_Customer_Model_Attribute_ApplicabilityAdjuster */
	public function adjust() {
		if (!is_null($this->getApplicability())) {
			$this->getAttribute()->addData(
				array(
					'is_required' => rm_01($this->isRequired())
					,'scope_is_required' => rm_01($this->isRequired())
					,'is_visible' => rm_01($this->isVisible())
					,'scope_is_visible' => rm_01($this->isVisible())
				)
			);
		}
		return $this;
	}

	/** @return bool */
	private function isRequired() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					/**
					 * Для многих стран (например, Белоруссии)
					 * в системе отсутствует справочник регионов.
					 * В таком случае пользователь
					 * не выбирает регион из выпадающего списка (выпадающий список отсутствует),
					 * а вводит его в текстовое поле, и тогда поле «region_id» будет незаполнено.
					 * По этой причине именно в данном классе
					 * снимаем требование обязательности наличия значения у поля «region_id».
					 * Обратите внимание, что данный класс используется
					 * только в контексте валидации формы
					 * @see Mage_Customer_Model_Form
					 * В контексте валидации адреса
					 * @see Df_Customer_Model_Address
					 * мы все равно проверяем заполненность региона,
					 * поэтому если заполненность региона требуется —
					 * то у нас достаточно гарантий соблюдния этого требования.
					 * @link http://magento-forum.ru/topic/3279/
					 */
					('region_id' !== $this->getAttribute()->getAttributeCode())
				&&
					(
							Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__REQUIRED
						===
							$this->getApplicability()
					)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isVisible() {
		return Df_Checkout_Model_Config_Source_Field_Applicability::VALUE__NO !== $this->getApplicability();
	}

	/** @return Mage_Customer_Model_Address_Abstract */
	private function getAddress() {return $this->cfg(self::P__ADDRESS);}

	/** @return string|null */
	private function getAddressType() {
		/** @var string|null $result */
		$result = $this->getAddress()->getDataUsingMethod('address_type');
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return string|null */
	private function getApplicability() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getApplicabilityManager() || !$this->getApplicabilityManager()->isEnabled()
				? null
				: $this->getApplicabilityManager()->getValue($this->getAttribute()->getAttributeCode())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Checkout_Model_Settings_Field_Applicability|null */
	private function getApplicabilityManager() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getAddressType()
				? null
				: df_cfg()->checkout()->field()->getApplicabilityByAddressType($this->getAddressType())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Mage_Customer_Model_Attribute */
	private function getAttribute() {return $this->cfg(self::P__ATTRIBUTE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ADDRESS, Df_Customer_Const::ADDRESS_ABSTRACT_CLASS)
			->_prop(self::P__ATTRIBUTE, Df_Customer_Const::ATTRIBUTE_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__ADDRESS = 'address';
	const P__ATTRIBUTE = 'attribute';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Customer_Model_Attribute_ApplicabilityAdjuster
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}