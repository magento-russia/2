<?php
class Df_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {
	/**
	 * Цель перекрытия —
	 * сигнализации о событии @uses Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareCollection.
	 * @override
	 * @param Varien_Data_Collection $collection
	 * @return void
	 */
	public function setCollection($collection) {
		/**
		 * Нам недостаточно события _load_before,
		 * потому что не все коллекции заказов используются для таблицы заказов,
		 * а в Magento 1.4 по коллекции невозможно понять,
		 * используется ли она для таблицы заказов или нет
		 * (в более поздних версиях Magento понять можно, потому что
		 * коллекция, используемая для таблицы заказов, принадлежит особому классу).
		 *
		 * Это событие перехватывается методом
		 * @used-by Df_Sales_Observer::rm_adminhtml_block_sales_order_grid__prepare_collection()
		 * для добавления в коллекцию колонки с перечислением заказанных товаров.
		 */
		Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareCollection::dispatch($collection);
		parent::setCollection($collection);
	}

	/**
	 * Цель перекрытия —
	 * сигнализации о событии
	 * @uses Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter
	 * @override
	 * @return Df_Adminhtml_Block_Sales_Order_Grid
	 */
	protected function _prepareColumns() {
		parent::_prepareColumns();
		/**
		 * Это событие перехватывается методом
		 * @used-by Df_Sales_Observer::rm_adminhtml_block_sales_order_grid__prepare_columns_after()
		 * для добавления в коллекцию колонки с перечислением заказанных товаров.
		 */
		Mage::dispatchEvent(
			Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter::EVENT
			,array('grid' => $this)
		);
		// Учитывая, что обработчики вызванного выше события могли изменить столбцы,
		// столбцы надо упорядочить заново.
		$this->sortColumnsByOrder();
		return $this;
	}
}