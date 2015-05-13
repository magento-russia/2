<?php
class Df_1C_Model_Cml2_Import_Data_Collection_OfferPart_OptionValues
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/**
	 * В версиях ранее 4-й модуля 1С-Битрикс
	 * товарное предложение не перечисляет незаполненные характеристики.
	 * Например, если для товарного предложения
	 * в 1С не заполнено значение характеристики «Тип кожи»,
	 * то в версии 4 структура будет выглядеть так:
		<ХарактеристикиТовара>
			<ХарактеристикаТовара>
				<Наименование>Размер</Наименование>
				<Значение>38</Значение>
			</ХарактеристикаТовара>
			<ХарактеристикаТовара>
				<Наименование>Цвет</Наименование>
				<Значение>Бежевый</Значение>
			</ХарактеристикаТовара>
			<ХарактеристикаТовара>
				<Наименование>Тип кожи</Наименование>
				<Значение/>
			</ХарактеристикаТовара>
		</ХарактеристикиТовара>
	 *
	 * А в версии ранее 4-й структура будет выглядеть так:
		<ХарактеристикиТовара>
			<ХарактеристикаТовара>
				<Наименование>Размер</Наименование>
				<Значение>38</Значение>
			</ХарактеристикаТовара>
			<ХарактеристикаТовара>
				<Наименование>Цвет</Наименование>
				<Значение>Бежевый</Значение>
			</ХарактеристикаТовара>
		</ХарактеристикиТовара>
	 *
	 * Как можно заметить, в  версии ранее 4-й в структуре отсутствует блок
		<ХарактеристикаТовара>
			<Наименование>Тип кожи</Наименование>
			<Значение/>
		</ХарактеристикаТовара>
	 *
	 * Так вот, это приводит к тому,
	 * что у простого варианта настраиваемого товара в Magento не будет инициализировано
	 * значение товарного свойства, являющего опцией настраиваемого товара.
	 * В таком случае Magento откажется считать данное товарное предложение
	 * частью настраиваемого товара.
	 *
	 * Для устранения этой проблемы нам надо инициализировать все товарные свойства,
	 * которые являются опциями настраиваемого товара.
	 * Для тех свойств, значения которых в 1С отсутствуют,
	 * мы в Magento используем значение [неизвестно].
	 * @see Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue::$VALUE__UNKNOWN
	 *
	 * Смотрите также комментарий к методу
	 * @see Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue::getValue()
	 * Тот метод содержит решение этой же проблемы для версии 4 модуля 1С-Битрикс.
	 *
	 * НЕЛЬЗЯ автоматически вызывать данный метод из метода @see getItems(),
	 * потмоу что иначе мы попадём рекурсию.
	 *
	 * @return void
	 */
	public function addAbsentItems() {
		foreach ($this->getAbsentConfigurableMagentoAttributes() as $attribute) {
			/** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
			$this->addItem(Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue_Empty::i2(
				$this->getOffer(), $attribute
			));
		}
	}

	/**
	 * @param string $attributeId
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue
	 */
	public function findByAttributeId($attributeId) {
		df_param_string_not_empty($attributeId, 0);
		if (!isset($this->{__METHOD__}[$attributeId])) {
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue|null $result */
			foreach ($this as $optionValue) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue $optionValue */
				if ($attributeId === $optionValue->getAttributeMagento()->getId()) {
					$result = $optionValue;
					break;
				}
			}
			df_assert($result instanceof Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue);
			$this->{__METHOD__}[$attributeId] = $result;
		}
		return $this->{__METHOD__}[$attributeId];
	}

	/**
	 * @override
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue[]
	 */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			// Важно здесь сразу инициализировать переменную $this->{__METHOD__},
			// чтобы не попадать в рекурсию в коде ниже.
			$this->{__METHOD__} = parent::getItems();
			if ((0 === count($this->{__METHOD__})) && $this->getOffer()->isTypeConfigurableChild()) {
				$this->addItem(
					Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue_Anonymous::i(
						$this->getOffer()
					)
				);
			}
			/**
			 * НЕЛЬЗЯ автоматически вызывать здесь @see addAbsentItems(),
			 * потому что иначе мы попадём рекурсию.
			 */
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue::_CLASS;
	}

	/**
	 * @override
	 * Позволяет добавлять к создаваемым элементам
	 * дополнительные, единые для всех элементов, параметры
	 * @return array(string => mixed)
	 */
	protected function getItemParamsAdditional() {
		return array_merge(parent::getItemParamsAdditional(), array(self::P__OFFER => $this->getOffer()));
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {
		return array('ХарактеристикиТовара', 'ХарактеристикаТовара');
	}

	/** @return Mage_Catalog_Model_Resource_Eav_Attribute[] */
	private function getAbsentConfigurableMagentoAttributes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				// очень красивое решение!
				array_diff_key(
					$this->getOffer()->getConfigurableParent()->getConfigurableAttributes()
					, $this->getOffer()->getConfigurableAttributes()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Offer */
	private function getOffer() {return $this->cfg(self::P__OFFER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__OFFER, Df_1C_Model_Cml2_Import_Data_Entity_Offer::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__OFFER = 'offer';
	/**
	 * @static
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer
	 * @param Df_Varien_Simplexml_Element $element
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_OfferPart_OptionValues
	 */
	public static function i(
		Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer, Df_Varien_Simplexml_Element $element
	) {
		return new self(array(self::P__OFFER => $offer, self::P__SIMPLE_XML => $element));
	}
}