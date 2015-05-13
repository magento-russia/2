<?php
/**
 * @method Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareCollection getEvent()
 */
class Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (
				df_cfg()->sales()->orderGrid()->productColumn()->getEnabled()
			&&
				df_enabled(Df_Core_Feature::SALES)
		) {
			/**
			 * Важно подсчитать количество элементов в коллекции до выполнения последующих действий,
			 * иначе расчёт будет неверным:
			 * @link http://magento-forum.ru/topic/4219/
			 * Дело в том, что дальнейшие действия приводят к тому,
			 * что стандартный код подсчёта количества элементов срабатывает так (неверно):
			 *
			 * SELECT COUNT(*) FROM `sales_flat_order_grid` AS `main_table`
			   INNER JOIN `sales_flat_order_item` AS `sales/order_item`
			   ON `sales/order_item`.order_id=`main_table`.entity_id GROUP BY `entity_id`
			 *
			 * Этот запрос возвращает не то, что нужно.
			 * Решение вызыывать getSize сразу является блестящим!
			 */
			$this->getEvent()->getCollection()->getSize();
			try {
				$this->getEvent()->getCollection()
					->getSelect()->getAdapter()->query(
						'SET SESSION group_concat_max_len = 20000;'
					)
				;
			}
			catch(Exception $e) {
				df_handle_entry_point_exception($e, false);
			}
			$this->getEvent()->getCollection()
				->join(
					'sales/order_item'
					,'`sales/order_item`.order_id=`main_table`.entity_id'
					,array(
						self::COLLECTION_ITEM_PARAM__DF_SKUS =>
							$this->createConcatExpression('sku')
						,self::COLLECTION_ITEM_PARAM__DF_NAMES =>
							$this->createConcatExpression('name')
						,self::COLLECTION_ITEM_PARAM__DF_QTYS =>
							$this->createConcatExpression('qty_ordered')
						,self::COLLECTION_ITEM_PARAM__DF_PRODUCT_IDS =>
							$this->createConcatExpression('product_id')
						,self::COLLECTION_ITEM_PARAM__DF_ORDER_ITEM_IDS =>
							$this->createConcatExpression('item_id')
						,self::COLLECTION_ITEM_PARAM__DF_TOTALS =>
							$this->createConcatExpression('row_total')
						,self::COLLECTION_ITEM_PARAM__DF_PARENTS =>
							new Zend_Db_Expr(
								rm_sprintf(
									'group_concat(
										IFnull(`sales/order_item`.%s, 0) SEPARATOR "%s"
									)'
									,'parent_item_id'
									,Df_Core_Const::T_UNIQUE_SEPARATOR
								)
							)
					)
				)
			;
			$this->getEvent()->getCollection()->getSelect()->group('entity_id');
		}
	}

	/**
	 * @param string $fieldName
	 * @return Zend_Db_Expr
	 */
	private function createConcatExpression($fieldName) {
		df_param_string($fieldName, 0);
		return
			new Zend_Db_Expr(
				rm_sprintf(
					'group_concat(`sales/order_item`.%s SEPARATOR "%s")'
					,$fieldName
					,Df_Core_Const::T_UNIQUE_SEPARATOR
				)
			)
		;
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareCollection::_CLASS;
	}

	const _CLASS = __CLASS__;
	const COLLECTION_ITEM_PARAM__DF_ORDER_ITEM_IDS = 'df_order_item_ids';
	const COLLECTION_ITEM_PARAM__DF_PRODUCT_IDS = 'df_product_ids';
	const COLLECTION_ITEM_PARAM__DF_SKUS = 'df_skus';
	const COLLECTION_ITEM_PARAM__DF_NAMES = 'df_names';
	const COLLECTION_ITEM_PARAM__DF_QTYS = 'df_qtys';
	const COLLECTION_ITEM_PARAM__DF_PARENTS = 'df_parents';
	const COLLECTION_ITEM_PARAM__DF_TOTALS = 'df_totals';
}