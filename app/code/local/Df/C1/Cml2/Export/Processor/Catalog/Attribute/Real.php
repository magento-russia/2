<?php
namespace Df\C1\Cml2\Export\Processor\Catalog\Attribute;
class Real extends \Df\C1\Cml2\Export\Processor\Catalog\Attribute {
	/**
	 * @override
	 * @return bool
	 */
	public function isEligible() {return dfc($this, function() {return
			/**
			 * Невидимые свойства:
			 * category_ids, created_at, has_options, links_exist, old_id,
			 * price_type, required_options, sku_type, updated_at, url_path, weight_type.
			 * Думаю, нет смысла экспортировать значения этих свойств.
			 */
			$this->getAttribute()->getIsVisible()
		&&
			!in_array($this->getAttribute()->getName(), [
				// «Макет»
				'custom_design'
				// «Вступление в силу»
				,'custom_design_from'
				// «Утрата силы»
				,'custom_design_to'
				// «Дополнительные макетные правила»
				,'custom_layout_update'
				/**
				 * По стандарту CommerceML 2.08 описание должно передаваться в ветке
				 * «Каталог/Товары/Товар/Описание».
				 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
				 * Передавать описание как обычное свойство товаров не нужно.
				 */
				,'description'
				// «Допустимо ли поздравительное сообщение?»
				,'gift_message_available'
				/**
				 * По стандарту CommerceML 2.08 цены должны передаваться в ветке
				 * «ПакетПредложений/Предложения/Предложение/Цены».
				 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
				 * Передавать цены как обычные свойства товаров не нужно.
				 */
				,'group_price'
				// «Является ли данный товар периодической услугой?»
				,'is_recurring'
				// «В какой момент открывать реальную цену?»
				,'msrp_display_actual_price_type'
				// «Накладывает ли производитель на товар ценово-рекламные ограничения?»
				,'msrp_enabled'
				/**
				 * По стандарту CommerceML 2.08 название должно передаваться в ветке
				 * «Каталог/Товары/Товар/Наименование».
				 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
				 * Передавать название как обычное свойство товаров не нужно.
				 */
				,'name'
				// «Место отображения свойств»
				,'options_container'
				// «Тип макета»
				,'page_layout'
				/**
				 * По стандарту CommerceML 2.08 цены должны передаваться в ветке
				 * «ПакетПредложений/Предложения/Предложение/Цены».
				 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
				 * Передавать цены как обычные свойства товаров не нужно.
				 */
				,'price'
				/**
				 * По стандарту CommerceML 2.08 артикул должен передаваться в ветке
				 * «Каталог/Товары/Товар/ИдентификаторТовара/Артикул».
				 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
				 * А текущая версия 4.0.5.2 «Помощника импорта товаров с сайта»
				 * дополнения 1С-Битрикс для обмена данными с интернет-магазином
				 * http://www.1c-bitrix.ru/download/1c/ecommerce/4.0.5.2_UT11.1.9.61.zip
				 * требует размещения поля «Артикул» в ветке
				 * «Каталог/Товары/Товар/Артикул».
				 * В любом случае, передавать артикул как обычное свойство товаров не нужно.
				 */
				,'sku'
				// «Состояние» (продавать или нет).
				// Думаю, клиентам передача значений этого свойства в 1С
				// пока не потребуется.
				,'status'
				/**
				 * «Налоговая группа».
				 * Думаю, клиентам передача значений этого свойства в 1С пока не потребуется.
				 * Однако стандарт CommerceML 2.08 поддерживает передачу таких сведений в ветке
				 * «Каталог/Товары/Товар/СтавкиНалогов».
				 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
				 */
				,'tax_class_id'
				/**
				 * Я думаю, передавать внешней системе «url_key» смысла нет.
				 * Вместо этого разумнее передавать полный веб-адрес товара
				 * на витрине магазина.
				 * @see \Df\C1\Cml2\Export\Processor\Catalog\Attribute\Url
				 */
				,'url_key'
				// «Видимость».
				// Думаю, клиентам передача значений этого свойства в 1С
				// пока не потребуется.
				,'visibility'
			])
		&&
			/**
			 * По стандарту CommerceML 2.08 картинки должны передаваться в ветке
			 * «Каталог/Товары/Товар/Картинка».
			 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
			 * Передавать картинки как обычные свойства товаров не нужно.
			 */
			!in_array($this->getAttribute()->getFrontendInput(), ['gallery', 'media_image'])
	;});}

	/**       
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getВариантыЗначений() {
		/** @var array(mixed => mixed) $result */
		if (!$this->этоСправочник()) {
			$result = [];
		}
		else switch ($this->getAttribute()->getSourceModel()) {
			case 'eav/entity_attribute_source_boolean':
				$result = $this->getВариантыЗначений_Boolean();
				break;
			case 'eav/entity_attribute_source_table':
				$result = $this->getВариантыЗначений_SourceTable();
				break;
			default:
				$result = $this->getВариантыЗначений_CustomSourceModel();
				break;
		}
		return $result;
	}	
	
	/**
	 * @override
	 * @return string
	 */
	protected function getИд() {return dfc($this, function() {
		if (!$this->getAttribute()->get1CId()) {
			$this->setData(self::$P__ATTRIBUTE, df_attributes()->createOrUpdate(array(
				\Df\C1\C::ENTITY_EXTERNAL_ID => df_t()->guid()
			), $this->getAttribute()->getName()));
		}
		return $this->getAttribute()->get1CId();
	});}

	/**
	 * Действуем по аналогии с @see Mage_Eav_Model_Entity_Attribute_Frontend_Abstract::getValue()
	 * В принципе, для простых свойств значение можно получить вызывая этот метод так:
	 * $result = $this->getAttribute()->getFrontend()->getValue($product);
	 * @override
	 * @param \Df_Catalog_Model_Product $product
	 * @return string|string[]|null
	 */
	protected function getЗначение(\Df_Catalog_Model_Product $product) {return
		$this->getЗначение_postProcess($product->getData($this->getAttribute()->getName()))
	;}

	/**
	 * @override
	 * @return string
	 */
	protected function getНаименование() {return $this->getAttribute()->getFrontendLabel();}

	/**
	 * @override
	 * @return string
	 */
	protected function getОписание() {return $this->getAttribute()->getNote();}

	/**
	 * @override
	 * @return string
	 */
	protected function getТипЗначений() {return dfc($this, function() {
		/** @var string $result */
		switch ($this->getAttribute()->getFrontendInput()) {
			case 'boolean':
			case 'multiselect':
			case 'select':
				/**
				 * Обратите внимание, что вариант «Булево»
				 * официально недопустим в текущей версии 2.08 стандарта CommerceML 2
				 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
				 * Можно попробовать использовать его на свой страх и риск.
				 */
				$result = 'Справочник';
				break;
			case 'date':
				$result = 'Время';
				break;
			case 'price':
				$result = 'Число';
				break;
			case 'media_image':
			case 'text':
			case 'textarea':
			case 'weee':
			default:
				$result = 'Строка';
		}
		return $result;
	});}

	/**
	 * @override
	 * @return bool
	 */
	protected function isМножественное() {return dfc($this, function() {return
		'multiselect' === $this->getAttribute()->getFrontendInput()
	;});}

	/** @return \Df_Catalog_Model_Resource_Eav_Attribute */
	private function getAttribute() {return $this->cfg(self::$P__ATTRIBUTE);}

	/** @return array(mixed => mixed) */
	private function getВариантыЗначений_Boolean() {return dfc($this, function() {return
		['Справочник' => [
			['ИдЗначения' => 'true', 'Значение' => 'Да']
			,['ИдЗначения' => 'false', 'Значение' => 'Нет']
		]]
	;});}

	/** @return array(mixed => mixed) */
	private function getВариантыЗначений_CustomSourceModel() {return dfc($this, function() {
		/** @var string[] $values */
		$values = [];
		// например: catalog/product_attribute_source_countryofmanufacture
		foreach ($this->getAttribute()->getSource()->getAllOptions() as $option) {
			/** @var array(string => string) $option */
			/** @var string $value */
			$value = dfa($option, 'value');
			// не экспортируем опцию «-- выберите значение --» («-- Please Select --»)
			if (!df_empty_string($value)) {
				$values[]= df_cdata(dfa($option, 'label'));
			}
		}
		return ['Значение' => $values];
	});}

	/** @return array(mixed => mixed) */
	private function getВариантыЗначений_SourceTable() {
		return dfc($this, function() {
			/** @var array(string => mixed) $values */
			$values = [];
			foreach ($this->getAttribute()->getOptions($this->store()) as $option) {
				/** @var \Df_Eav_Model_Entity_Attribute_Option $option */
				/**
				 * По аналогии с
				 * @see \Df\C1\Cml2\Import\Processor\ReferenceList::assignExternalIdToOptions()
				 */
				if (!$option->get1CId()) {
					$option->set1CId(df_t()->guid());
					$option->save();
				}
				$values[] = [
					'ИдЗначения' => $option->get1CId()
					,'Значение' => df_cdata($option->getValue())
				];
			}
			return ['Справочник' => $values];
		});
	}

	/**
	 * @param mixed $value
	 * @return string|string[]|null
	 */
	private function getЗначение_postProcess($value) {
		/** @var string|string[] $result */
		if (is_array($value)) {
			// multiselect
			$result = array_map(array($this, __FUNCTION__), $value);
		}
		else if (is_null($value)) {
			$result = null;
		}
		else {
			if (!$this->этоСправочник()) {
				switch ($this->getAttribute()->getFrontendInput()) {
					case 'date':
						if (!$value instanceof \Zend_Date) {
							$value = new \Zend_Date($value);
						}
						$result = $this->entry()->date($value);
						break;
					case 'price':
						$result = df_f2($value);
						break;
					default:
						// например, свойство «Вес»
						if ('decimal' === $this->getAttribute()->getBackendType()) {
							$result = df_f2($value);
						}
						else {
							$result = df_cdata($value);
						}
				}
			}
			else {
				switch ($this->getAttribute()->getSourceModel()) {
					case 'eav/entity_attribute_source_boolean':
						$result = df_bts($value);
						break;
					case 'eav/entity_attribute_source_table':
						/**
						 * Текущая версия 4.0.5.2 «Помощника импорта товаров с сайта»
						 * дополнения 1С-Битрикс для обмена данными с интернет-магазином
						 * http://www.1c-bitrix.ru/download/1c/ecommerce/4.0.5.2_UT11.1.9.61.zip
						 * не понимает, когда в качестве значения свойства
						 * передаётся внешний идентификатор значения свойства.
						 * Однако оставим здесь в комментарии
						 * программный код получения внешнего идентификатора значения свойства:
							$optionId = $this->getAttribute()->getSource()->getOptionId($value);
							$option = $this->getAttribute()->getOptions($this->store())->getItemById($optionId);
							$result = $option->get1CId();
						 */
						$result = $this->getAttribute()->getFrontend()->getOption($value);
						df_result_string_not_empty($result);
						break;
					default:
						$result = $this->getAttribute()->getFrontend()->getOption($value);
						df_result_string_not_empty($result);
						$result = df_cdata($result);
				}
			}
		}
		return $result;
	}

	/** @return bool */
	private function этоСправочник() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 'Справочник' === $this->getТипЗначений();
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
			->_prop(self::$P__DOCUMENT, \Df\C1\Cml2\Export\Document\Catalog::class)
			->_prop(self::$P__ATTRIBUTE, \Df_Catalog_Model_Resource_Eav_Attribute::class)
		;
	}
	/** @var string */
	private static $P__ATTRIBUTE = 'attribute';
	/**
	 * @used-by \Df\C1\Cml2\Export\Document\Catalog::processorForAttribute()
	 * @static
	 * @param \Df_Catalog_Model_Resource_Eav_Attribute $attribute
	 * @param \Df\C1\Cml2\Export\Document\Catalog $document
	 * @return \Df\C1\Cml2\Export\Processor\Catalog\Attribute\Real
	 */
	public static function i(
		\Df_Catalog_Model_Resource_Eav_Attribute $attribute
		,\Df\C1\Cml2\Export\Document\Catalog $document
	) {
		return new self([self::$P__DOCUMENT => $document, self::$P__ATTRIBUTE => $attribute]);
	}
}