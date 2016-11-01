<?php
class Df_C1_Cml2_Export_Processor_Sale_Order_Item extends Df_C1_Cml2_Export_Processor_Sale {
	/** @return array(string => mixed) */
	public function getDocumentData() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => mixed) $result */
			$result = array(
				'Ид' => $this->getProductExternalId()
				,'Наименование' => $this->getProductNameForExport()
				//$this->getProduct()->getName()
				,'БазоваяЕдиница' => $this->entry()->unit()
				,'ЦенаЗаЕдиницу' => df_f2($this->getOrderItemExtended()->getPrice())
				,'Количество' => $this->getOrderItemExtended()->getQtyOrdered()
				/**
				 * @uses Df_Sales_Model_Order_Item_Extended::getRowTotal() —
				 * это без налогов и скидок
				 */
				,'Сумма' => df_f2(
						$this->getOrderItemExtended()->getRowTotal()
					+
						$this->getOrderItemExtended()->getTaxAmount()
					-
						abs($this->getOrderItemExtended()->getDiscountAmount())
				)
				,'СтавкиНалогов' => array(
					'СтавкаНалога' => array(
						'Наименование' => 'НДС'
						,'Ставка' => df_f2(
								100
							*
								$this->getOrderItemExtended()->getTaxAmount()
							/
								$this->getOrderItemExtended()->getRowTotal()
						)
					)
				)
				,'Налоги' => array(
					'Налог' => $this->entry()->tax(
						'НДС', $this->getOrderItemExtended()->getTaxAmount(), true
					)
				)
				,'Скидки' => array(
					'Скидка' =>
						$this->entry()->discount(
							'Совокупная скидка'
							// Magento хранит скидки в виде отрицательных чисел
							, abs($this->getOrderItemExtended()->getDiscountAmount())
							, true
						)
				)
				,'ЗначенияРеквизитов' => array(
					'ЗначениеРеквизита' => array(
						$this->entry()->name('ВидНоменклатуры', $this->getAttributeSetName())
						,$this->entry()->name('ТипНоменклатуры', 'Товар')
					)
				)
			);
			if ($this->isProductCreatedInMagento()) {
				/**
				 * Если товар был создан в Magento,
				 * а не импортирован ранее из 1С:Управление торговлей,
				 * то 1С:Управление торговлей импортирует его
				 * на основании указанной в документе-заказе информации.
				 *
				 * Поэтому постараемся здесь наиболее полно описать такие товары.
				 *
				 * К сожалению, 1С:Управление торговлей не воспринимает
				 * дополнительные характеристики таких товаров
				 * (например, мы не можем экспортировать описание товара)
				 */
				$result =
					df_extend(
						$result
						,array(
								 /*
							'Комментарий' =>
								implode(
									Df_Core_Const::N
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
									\Df\Xml\X::ATTR =>
										array(
											'ФорматHTML' => df_bts(true)
										)
									,\Df\Xml\X::CONTENT =>
										df_cdata(
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
													df_cdata(
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
				Df_C1_Cml2_State::s()->export()->getProducts()->getProductById(
					df_nat($this->getOrderItem()->getProductId())
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
		return df_nts($this->getProduct()->get1CId());
	}

	/** @return string */
	private function getProductNameForExport() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->getProduct()->getName();
			df_assert_string_not_empty($result);
			if ($this->isProductCreatedInMagento()) {
				/** @var array(string => mixed|array(string => string)) $productOptions */
				$productOptions = $this->getOrderItem()->getProductOptions();
				df_assert_array($productOptions);
				/** @var array(array(string => string)) $customOptions */
				$customOptions = df_nta(dfa($productOptions, 'options'));
				if ($customOptions) {
					/** @var string[] $customOptionsKeyValuePairsAsText */
					$customOptionsKeyValuePairsAsText = array();
					foreach ($customOptions as $customOption) {
						/** @var array(string => string) $customOption */
						df_assert_array($customOption);
						/** @var string $label */
						$label = dfa($customOption, 'label');
						df_assert_string($label);
						/** @var string $value */
						$value = dfa($customOption, 'value');
						df_assert_string($value);
						$customOptionsKeyValuePairsAsText[]= implode(' = ', array($label, $value));
					}
					$result = sprintf('%s {%s}', $result, df_csv($customOptionsKeyValuePairsAsText));
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Sales_Model_Order_Item */
	private function getOrderItem() {return $this->cfg(self::$P__ORDER_ITEM);}

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
		$this->_prop(self::$P__ORDER_ITEM, 'Mage_Sales_Model_Order_Item');
	}
	/** @var string */
	private static $P__ORDER_ITEM = 'order_item';

	/**
	 * @used-by Df_C1_Cml2_Export_Processor_Sale_Order::getDocumentData_OrderItems()
	 * @static
	 * @param Mage_Sales_Model_Order_Item $orderItem
	 * @return Df_C1_Cml2_Export_Processor_Sale_Order_Item
	 */
	public static function i(Mage_Sales_Model_Order_Item $orderItem) {
		return new self(array(self::$P__ORDER_ITEM => $orderItem));
	}
}