<?php
namespace Df\C1\Cml2\Export\Processor\Sale\Order;
use Df_Eav_Model_Entity_Attribute_Set as AttributeSet;
use Mage_Sales_Model_Order_Item as OI;
class Item extends \Df\C1\Cml2\Export\Processor\Sale {
	/** @return array(string => mixed) */
	public function getDocumentData() {return dfc($this, function() {return df_extend(
		[
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
		]
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
		,!$this->isProductCreatedInMagento() ? [] : [
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
		]
	);});}

	/** @return string */
	private function getAttributeSetName() {return dfc($this, function() {return
		AttributeSet::ld($this->getProduct()->getAttributeSetId())->getAttributeSetName()
	;});}

	/** @return \Df_Catalog_Model_Product */
	private function getProduct() {return dfc($this, function() {return
		\Df\C1\Cml2\State::s()->export()->getProducts()->getProductById(
			df_nat($this->getOrderItem()->getProductId())
		)
	;});}

	/**
	 * У товара может отсутствовать внешний идентификатор, если товар был создан в Magento.
	 * В таком случае мы не назначаем товару внешний идентификатор,
	 * потому что 1С:Управление торговлей всё равно его проигнорирует
	 * и назначит свой идентификатор.
	 * @return string
	 */
	private function getProductExternalId() {return df_nts($this->getProduct()->get1CId());}

	/** @return string */
	private function getProductNameForExport() {return dfc($this, function() {
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
				$customOptionsKeyValuePairsAsText = [];
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
		return $result;
	});}

	/** @return OI */
	private function getOrderItem() {return $this->cfg(self::$P__ORDER_ITEM);}

	/** @return \Df_Sales_Model_Order_Item_Extended */
	private function getOrderItemExtended() {return dfc($this, function() {return
		\Df_Sales_Model_Order_Item_Extended::i($this->getOrderItem())
	;});}

	/** @return bool */
	private function isProductCreatedInMagento() {return !$this->getProductExternalId();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ORDER_ITEM, OI::class);
	}
	/** @var string */
	private static $P__ORDER_ITEM = 'order_item';

	/**
	 * @used-by \Df\C1\Cml2\Export\Processor\Sale\Order::getDocumentData_OrderItems()
	 * @param OI $orderItem
	 * @return \Df\C1\Cml2\Export\Processor\Sale\Order\Item
	 */
	public static function i(OI $orderItem) {return new self([self::$P__ORDER_ITEM => $orderItem]);}
}