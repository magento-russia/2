<?php
class Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue
	extends Df_1C_Model_Cml2_Import_Data_Entity {
	/**
	 * Добавили к названию метода окончание «Magento»,
	 * чтобы избежать конфликта с родительским методом
	 * Df_Core_Model_SimpleXml_Parser_Entity::getAttribute()
	 * @return Mage_Catalog_Model_Resource_Eav_Attribute
	 */
	public function getAttributeMagento() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Catalog_Model_Resource_Eav_Attribute|null $result */
			if (
					$this->getEntityAttribute()
				&&
					/**
					 * Значение «[неизвестно]» в справочнике должно отсутствовать.
					 * Добавляем его вручную.
					 */
					(self::$VALUE__UNKNOWN !== $this->getValue())
			)  {
				$result =
					$this->getAttributes()->findByExternalId(
						$this->getEntityAttribute()->getExternalId()
					)
				;
				df_assert($result);
			}
			else {
				$result = $this->setupAttribute();
			}
			if (!$this->getEntityAttribute()) {
				$this->setupOption($result);
				$this->getAttributes()->addEntity($result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getExternalId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				mb_strtolower(implode(': ', array($this->getName(), $this->getValue())))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * В новой версии модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
	 * мы можем получить следующую структуру:
	 *
		<ХарактеристикаТовара>
			<Наименование>Цвет (Обувь (Для характеристик))</Наименование>
			<Значение>Белый</Значение>
		</ХарактеристикаТовара>
	 *
	 * Здесь:
	 * 		«Цвет» — название товарного свойства
	 * 		«Обувь» — название типа товаров
	 *
	 * Мало того, что нам, разумеется, лучше дать товарному свойство в Magento название «Цвет»
	 * вместо «Цвет (Обувь (Для характеристик))»,
	 * так есть ещё и более важный аспект:
	 * в новой версии модуля 1С-Битрикс значения настраиваемых опций из ветки <ХарактеристикаТовара>
	 * дополнительно дублируются в соседней ветке <ЗначенияСвойств>:
	 *
		<Предложение>
			(...)
			<ХарактеристикиТовара>
				(...)
				<ХарактеристикаТовара>
					<Наименование>Цвет (Обувь (Для характеристик))</Наименование>
					<Значение>Белый</Значение>
				</ХарактеристикаТовара>
				(...)
			</ХарактеристикиТовара>
			<ЗначенияСвойств>
				(...)
				<ЗначенияСвойства>
					<Ид>05e26d72-01e4-11dc-a411-00055d80a2d1</Ид>
					<Значение>05e26d73-01e4-11dc-a411-00055d80a2d1</Значение>
				</ЗначенияСвойства>
	 			(...)
			</ЗначенияСвойств>
		</Предложение>
	 *
	 * Нам нужно сопоставить «свойство» «характеристике» —
	 * это позволит нам избежать двукратного создания в Magento одного и того же свойства.
	 * Сопоставить же можно по имени: характеристике «Цвет» будет соответствовать справочник «Цвет»:
	 *
		<Свойство>
			<Ид>05e26d72-01e4-11dc-a411-00055d80a2d1</Ид>
			<НомерВерсии>AAAACQAAAAA=</НомерВерсии>
			<ПометкаУдаления>false</ПометкаУдаления>
			<Наименование>Цвет</Наименование>
			<Внешний>false</Внешний>
			<ТипЗначений>Справочник</ТипЗначений>
			<ВариантыЗначений>
	 			(...)
				<Справочник>
					<ИдЗначения>dacc2953-7473-11df-b338-0011955cba6b</ИдЗначения>
					<Значение>Черный</Значение>
				</Справочник>
				<Справочник>
					<ИдЗначения>dacc2952-7473-11df-b338-0011955cba6b</ИдЗначения>
					<Значение>Зеленый</Значение>
				</Справочник>
				(...)
			</ВариантыЗначений>
		</Свойство>
	 *
	 * А для сопоставления нам нужно очистить имя «Цвет (Обувь (Для характеристик))»
	 * от шелухи и оставить «Цвет».
	 *
	 * @override
	 * @return string|null
	 */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = parent::getName();
			if ($result && rm_ends_with($result, '))')) {
				$result = df_trim(rm_first(explode('(', $result)));
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Mage_Eav_Model_Entity_Attribute_Option */
	public function getOption() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getOptionByAttribute($this->getAttributeMagento());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getValue() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание на важность значения «неизвестно».
			 * Мы можем получить из 1С подобное товарное предложение:
					<Предложение>
						(...)
						<ХарактеристикиТовара>
							<ХарактеристикаТовара>
								<Наименование>Размер</Наименование>
								<Значение>36</Значение>
							</ХарактеристикаТовара>
							<ХарактеристикаТовара>
								<Наименование>Тип кожи</Наименование>
								<Значение/>
							</ХарактеристикаТовара>
							(...)
						</ХарактеристикиТовара>
						<ЗначенияСвойств>
							<ЗначенияСвойства>
								<Ид>14ed8b06-55bd-11d9-848a-00112f43529a</Ид>
								<Значение>14ed8b08-55bd-11d9-848a-00112f43529a</Значение>
							</ЗначенияСвойства>
							(...)
						</ЗначенияСвойств>
					</Предложение>
			 * В 1С для этого товара неизвестно значение опции «Тип кожи».
			 * В Magento все опции внутри ветки ХарактеристикиТовара
			 * становятся настраиваемыми опциями настраиваемого товара.
			 * Если мы импортируем для данного товара
			 * пустую строку в качестве значения опции «Тип кожи»,
			 * то на витрине мы не сможем заказать данный товар,
			 * ибо в выпадающем списке опций значение будет отсутствовать.
			 * Да и даже если бы там пустое значение присутствовало,
			 * оно бы в вводило в ступор поупателя
			 * (аналогично, плохо обстояли бы дела с блоком пошаговой фильтрации,
			 * ибо непонятно, как там показывать пустую строку).
			 * Поэтому вместо пустой строки используем значение «неизвестно».
			 *
			 * Смотрите также комментарий к методу
			 * @see Df_1C_Model_Cml2_Import_Data_Collection_OfferPart_OptionValues::addAbsentItems()
			 * Тот метод содержит решение этой же проблемы
			 * для версий младше версии 4 модуля 1С-Битрикс.
			 */
			$this->{__METHOD__} = $this->getEntityParam('Значение', self::$VALUE__UNKNOWN);
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getValueId() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Опция имеет следующую структуру данных:
			  array(
					[option_id] => 35
					[attribute_id] => 148
					[sort_order] => 2
					[rm_1c_id] => 14ed8b52-55bd-11d9-848a-00112f43529a
					[default_value] => натуральная кожа
					[store_default_value] =>
					[value] => натуральная кожа
				 )
			 */
			$this->{__METHOD__} = rm_nat0($this->getOption()->getId());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getAttributeCodeGenerated() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_h()->_1c()->generateAttributeCode(
					$this->getName()
					// Намеренно убрал второй параметр ($this->getEntityProduct()->getAppliedTypeName()),
					// потому что счёл ненужным в данном случае
					// использовать приставку для системных имён товарных свойств,
					// потому что приставка (прикладной тип товара),
					// как правило, получается слишком длинной,
					// а название системного имени товарного свойства
					// ограничено 32 символами
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Product */
	protected function getEntityProduct() {return $this->getOffer()->getEntityProduct();}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Offer */
	protected function getOffer() {return $this->cfg(self::P__OFFER);}

	/** @return Mage_Catalog_Model_Resource_Eav_Attribute */
	protected function setupAttribute() {
		return
			$this->getAttributes()->findByCodeOrCreate(
				df_a($this->getAttributeData(), 'attribute_code')
				, $this->getAttributeData()
			)
		;
	}

	/**
	 * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
	 * @return void
	 */
	protected function setupOption(Mage_Catalog_Model_Resource_Eav_Attribute $attribute) {
		// Назначаем справочным значениям идентификаторы из 1С
		/** @var Mage_Eav_Model_Entity_Attribute_Option $option */
		$option = $this->getOptionByAttribute($attribute);
		$option->setData(Df_Eav_Const::ENTITY_EXTERNAL_ID, $this->getExternalId());
		$option->save();
	}

	/** @return array(string => mixed) */
	private function getAttributeData() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Catalog_Model_Resource_Eav_Attribute|null $attribute */
			$attribute =
				$this->getAttributes()->findByExternalId(
					$this->getEntityAttribute()
					? $this->getEntityAttribute()->getExternalId()
					: $this->getAttributeExternalId()
				)
			;
			$this->{__METHOD__} =
				$attribute
				?
					array_merge($attribute->getData(), array(
						/**
						 * Вот здесь, похоже, удачное место,
						 * чтобы добавить в уже присутствующий в Magento справочник
						 * значение текущей опции, если его там нет
						 */
						'option' =>
							Df_Eav_Model_Entity_Attribute_Option_Calculator::calculateStatic(
								$attribute
								,array('option_0' => array($this->getValue()))
								,$isModeInsert = true
								,$caseInsensitive = true
							)
					))
				:
					array(
						'entity_type_id' => rm_eav_id_product()
						,'attribute_code' => $this->getAttributeCodeGenerated()
						/**
						 * В Magento CE 1.4, если поле «attribute_model» присутствует,
						 * то его значение не может быть пустым
						 * @see Mage_Eav_Model_Config::_createAttribute
						 */
						,'backend_model' => null
						,'backend_type' => 'int'
						,'backend_table' => null
						,'frontend_model' => null
						,'frontend_input' => 'select'
						,'frontend_label' => $this->getName()
						,'frontend_class' => null
						,'source_model' => null
						,'is_required' => 0
						,'is_user_defined' => 1
						,'default_value' => null
						,'is_unique' => 0
						// в Magento CE 1.4 значением поля «note» не может быть null
						,'note' => ''
						,'frontend_input_renderer' => null
						,'is_global' => 1
						,'is_visible' => 1
						,'is_searchable' => 1
						,'is_filterable' => 1
						,'is_comparable' => 1
						,'is_visible_on_front' => 1
						,'is_html_allowed_on_front' => 0
						,'is_used_for_price_rules' => 0
						,'is_filterable_in_search' => 1
						,'used_in_product_listing' => 0
						,'used_for_sort_by' => 0
						,'is_configurable' => 1
						,'is_visible_in_advanced_search' => 1
						,'position' => 0
						,'is_wysiwyg_enabled' => 0
						,'is_used_for_promo_rules' => 0
						,/**
						 * Какое-то значение тут надо установить,
						 * потому что оно будет одним из ключей в реестре
						 * (второй ключ — название справочника)
						 */
						Df_Eav_Const::ENTITY_EXTERNAL_ID => $this->getAttributeExternalId()
						,'option' => array(
							'value' => array('option_0' => array($this->getValue()))
							,'order' => array('option_0' => 0)
							,'delete' => array('option_0' => 0)
						)
					)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getAttributeExternalId() {return 'RM 1С - ' .  $this->getName();}

	/** @return Df_Dataflow_Model_Registry_Collection_Attributes */
	private function getAttributes() {return df()->registry()->attributes();}

	/**
	 * В новой версии модуля 1С-Битрикс значения настраиваемых опций из ветки <ХарактеристикаТовара>
	 * дополнительно дублируются в соседней ветке <ЗначенияСвойств>:
	 *
		<Предложение>
			(...)
			<ХарактеристикиТовара>
				(...)
				<ХарактеристикаТовара>
					<Наименование>Цвет (Обувь (Для характеристик))</Наименование>
					<Значение>Белый</Значение>
				</ХарактеристикаТовара>
				(...)
			</ХарактеристикиТовара>
			<ЗначенияСвойств>
				(...)
				<ЗначенияСвойства>
					<Ид>05e26d72-01e4-11dc-a411-00055d80a2d1</Ид>
					<Значение>05e26d73-01e4-11dc-a411-00055d80a2d1</Значение>
				</ЗначенияСвойства>
	 			(...)
			</ЗначенияСвойств>
		</Предложение>
	 *
	 * Нам нужно сопоставить «свойство» «характеристике» —
	 * это позволит нам избежать двукратного создания в Magento одного и того же свойства.
	 * Сопоставить же можно по имени: характеристике «Цвет» будет соответствовать справочник «Цвет»:
	 *
		<Свойство>
			<Ид>05e26d72-01e4-11dc-a411-00055d80a2d1</Ид>
			<НомерВерсии>AAAACQAAAAA=</НомерВерсии>
			<ПометкаУдаления>false</ПометкаУдаления>
			<Наименование>Цвет</Наименование>
			<Внешний>false</Внешний>
			<ТипЗначений>Справочник</ТипЗначений>
			<ВариантыЗначений>
	 			(...)
				<Справочник>
					<ИдЗначения>dacc2953-7473-11df-b338-0011955cba6b</ИдЗначения>
					<Значение>Черный</Значение>
				</Справочник>
				<Справочник>
					<ИдЗначения>dacc2952-7473-11df-b338-0011955cba6b</ИдЗначения>
					<Значение>Зеленый</Значение>
				</Справочник>
				(...)
			</ВариантыЗначений>
		</Свойство>
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Attribute_ReferenceList|null
	 */
	private function getEntityAttribute() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_Attribute_ReferenceList|null $result */
			$result =
				$this->getState()->import()->collections()->getAttributes()->findByName(
					$this->getName()
				)
			;
			/**
			 * Возможно, что «свойство» и «характеристика» получили одинаковое имя по случайности?
			 * Сопоставимое «свойство» обязательно должно быть типа «справочник».
			 */
			if (!($result instanceof Df_1C_Model_Cml2_Import_Data_Entity_Attribute_ReferenceList)) {
				$result = null;
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
	 * @return Mage_Eav_Model_Entity_Attribute_Option
	 */
	private function getOptionByAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute) {
		/** @var Mage_Eav_Model_Entity_Attribute_Source_Table $source */
		$source = $attribute->getSource();
		df_assert($source instanceof Mage_Eav_Model_Entity_Attribute_Source_Table);
		/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $options */
		$options = Df_Eav_Model_Resource_Entity_Attribute_Option_Collection::i();
		df_h()->eav()->assert()->entityAttributeOptionCollection($options);
		$options->setPositionOrder('asc');
		$options->setAttributeFilter($attribute->getId());
		$options->setStoreFilter($attribute->getStoreId());
		$options->addFieldToFilter('tdv.value', $this->getValue());
		if (1 !== count($options)) {
			df_error(
				'Не могу найти в базе данных Magento опцию «%s» товарного свойства «%s».'
				, $this->getValue()
				, $attribute->getFrontendLabel()
			);
		}
		/** @var Mage_Eav_Model_Entity_Attribute_Option $option */
		$result = $options->fetchItem();
		df_assert($result instanceof Mage_Eav_Model_Entity_Attribute_Option);
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__OFFER, Df_1C_Model_Cml2_Import_Data_Entity_Offer::_CLASS);
	}

	/**
	 * Используется из @see Df_1C_Model_Cml2_Import_Data_Collection_OfferPart_OptionValues::getItemClass()
	 */
	const _CLASS = __CLASS__;
	const P__OFFER = 'offer';
	/** @var string */
	protected static $VALUE__UNKNOWN = '[неизвестно]';
}