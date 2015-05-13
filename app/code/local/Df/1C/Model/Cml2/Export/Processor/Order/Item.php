<?php
class Df_1C_Model_Cml2_Export_Processor_Order_Item extends Df_1C_Model_Cml2_Export_Processor {
	/** @return mixed[] */
	public function getDocumentData() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $result */
			$result =
				array(
					'Ид' => $this->getProductExternalId()
					,'Наименование' => $this->getProductNameForExport()
					//$this->getProduct()->getName()
					,'БазоваяЕдиница' =>
						array(
							Df_Varien_Simplexml_Element::KEY__ATTRIBUTES =>
								array(
									'Код' => '796'
									,'НаименованиеПолное' => 'Штука'
									,'МеждународноеСокращение' => 'PCE'
								)
							,Df_Varien_Simplexml_Element::KEY__VALUE => 'шт'
						)
					,'ЦенаЗаЕдиницу' =>
						df_h()->_1c()->formatMoney(
							$this->getOrderItemExtended()->getPrice()
						)
					,'Количество' => $this->getOrderItemExtended()->getQtyOrdered()
					,'Сумма' =>
						df_h()->_1c()->formatMoney(
							/**
							 * getRowTotal — это без налогов и скидок
							 */
								$this->getOrderItemExtended()->getRowTotal()
							+
								$this->getOrderItemExtended()->getTaxAmount()
							-
								abs(
									$this->getOrderItemExtended()->getDiscountAmount()
								)
						)

					,'СтавкиНалогов' =>
						array(
							'СтавкаНалога' =>
								array(
									'Наименование' => 'НДС'
									,'Ставка' =>
										df_h()->_1c()->formatMoney(
												100
											*
												$this->getOrderItemExtended()->getTaxAmount()
											/
												$this->getOrderItemExtended()->getRowTotal()
										)
								)
						)
					,'Налоги' =>
						array(
							'Налог' =>
								array(
									'Наименование' => 'НДС'
									,'УчтеноВСумме' => rm_bts(true)
									,'Сумма' =>
										df_h()->_1c()->formatMoney(
											$this->getOrderItemExtended()->getTaxAmount()
										)
								)
						)
					,'Скидки' =>
						array(
							'Скидка' =>
								array(
									'Наименование' => 'Совокупная скидка'
									,'УчтеноВСумме' => rm_bts(true)
									,'Сумма' =>
										/**
										 * Magento хранит скидки в виде отрицательных чисел
										 */
										df_h()->_1c()->formatMoney(
											abs(
												$this->getOrderItemExtended()->getDiscountAmount()
											)
										)
								)
						)
					,'ЗначенияРеквизитов' =>
						array(
							'ЗначениеРеквизита' =>
								array(
									array(
										'Наименование' => 'ВидНоменклатуры'
										,'Значение' => $this->getAttributeSetName()
									)
									,array(
										'Наименование' => 'ТипНоменклатуры'
										,'Значение' => 'Товар'
									)
								)
						)
				)
			;
			if ($this->isProductCreatedInMagento()) {
				/**
				 * Если товар был создан в Magento,
				 * а не импортирован ранее из 1С:Управление торговлей, * то 1С:Управление торговлей импортирует его
				 * на основании указанной в документе-заказе информации.
				 *
				 * Поэтому постараемся здесь наиболее полно описать такие товары.
				 *
				 * К сожалению, 1С:Управление торговлей не воспринимает
				 * дополнительные характеристики таких товаров
				 * (например, мы не можем экспортировать описание товара)
				 */
				$result =
					array_merge_recursive(
						$result
						,array(
								 /*
							'Комментарий' =>
								implode(
									Df_Core_Const::T_NEW_LINE
									,array(
										implode(
											' = '
											,array(
												'Артикул'
												,$this->getProduct()->getSku()
											)
										)
										,implode(
											' = '
											,array(
												'№ строки заказа'
												,$this->getOrderItem()->getId()
											)
										)
									)
								)
									*/
							/**
							'Описание' =>
								array(
									Df_Varien_Simplexml_Element::KEY__ATTRIBUTES =>
										array(
											'ФорматHTML' => rm_bts(true)
										)
									,Df_Varien_Simplexml_Element::KEY__VALUE =>
										rm_cdata(
											$this->getProduct()->getDescription()
										)
								)

							,'ЗначенияРеквизитов' =>
								array(
									'ЗначениеРеквизита' =>
										array(
											array(
												'Наименование' => 'Описание'
												,'Значение' =>
													rm_cdata(
														$this->getProduct()->getDescription()
													)
											)
										)
								)
							 **/
						)
					)
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getAttributeSetName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Eav_Model_Entity_Attribute_Set::ld($this->getProduct()->getAttributeSetId())
					->getAttributeSetName()
			;
		}
		return $this->{__METHOD__};
	}


	/** @return string */
	private function getItemId() {return '';}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_State::s()->export()->getProducts()->getProductById(
					rm_nat($this->getOrderItem()->getProductId())
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getProductExternalId() {
		/**
		 * У товара может отсутствовать внешний идентификатор, если товар был создан в Magento.
		 * В таком случае мы не назначаем товару внешний идентификатор,
		 * потому что 1С:Управление торговлей всё равно его проигнорирует
		 * и назначит свой идентификатор.
 		 */
		return df_nts($this->getProduct()->getData(Df_Eav_Const::ENTITY_EXTERNAL_ID));
	}

	/** @return string */
	private function getProductNameForExport() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->getProduct()->getName();
			df_assert_string_not_empty($result);
			if ($this->isProductCreatedInMagento()) {
				/** @var array[] $productOptions */
				$productOptions = $this->getOrderItem()->getProductOptions();
				df_assert_array($productOptions);
				/** @var array[] $customOptions */
				$customOptions = df_a($productOptions, 'options', array());
				df_assert_array($customOptions);
				if (0 < count($customOptions)) {
					/** @var string[] $customOptionsKeyValuePairsAsText */
					$customOptionsKeyValuePairsAsText = array();
					foreach ($customOptions as $customOption) {
						/** @var string[] $customOption */
						df_assert_array($customOption);
						/** @var string $label */
						$label = df_a($customOption, 'label');
						df_assert_string($label);
						/** @var string $value */
						$value = df_a($customOption, 'value');
						df_assert_string($value);
						$customOptionsKeyValuePairsAsText[]=
							implode(' = ', array($label, $value))
						;
					}
					$result = rm_sprintf('%s {%s}', $result, df_concat_enum($customOptionsKeyValuePairsAsText));
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Sales_Model_Order_Item */
	private function getOrderItem() {return $this->cfg(self::P__ORDER_ITEM);}

	/** @return Df_Sales_Model_Order_Item_Extended */
	private function getOrderItemExtended() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Sales_Model_Order_Item_Extended::i($this->getOrderItem());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isProductCreatedInMagento() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !$this->getProductExternalId();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ORDER_ITEM, 'Mage_Sales_Model_Order_Item');
	}

	const _CLASS = __CLASS__;
	/**
	 * Значение 74 мы можем ставить без опаски, потому что 1С:Управление торговлей
	 * сама использует идентификаторы такой длины для вариантов настраиваемых товаров, например:
	 * b79b0fe2-c8a5-11e1-a928-4061868fc6eb#cb2b9d20-c97a-11e1-a928-4061868fc6eb
	 */
	const EXTERNAL_ID__MAX_LENGTH = 74;
	const EXTERNAL_ID__PREFIX_MAGENTO = 'magento';
	const P__ORDER_ITEM = 'order_item';

	/**
	 * @static
	 * @param Mage_Sales_Model_Order_Item $orderItem
	 * @return Df_1C_Model_Cml2_Export_Processor_Order_Item
	 */
	public static function i(Mage_Sales_Model_Order_Item $orderItem) {
		return new self(array(self::P__ORDER_ITEM => $orderItem));
	}
}