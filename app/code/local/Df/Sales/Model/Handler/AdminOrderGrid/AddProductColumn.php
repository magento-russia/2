<?php
/** @method Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter getEvent() */
class Df_Sales_Model_Handler_AdminOrderGrid_AddProductColumn extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (df_cfg()->sales()->orderGrid()->productColumn()->getEnabled()) {
			$this
				->registerProductColumnRenderer()
				->addProductColumn()
			;
		}
	}

	/** @return Df_Sales_Model_Handler_AdminOrderGrid_AddProductColumn */
	private function registerProductColumnRenderer() {
		/** @var array|null $columnRenderers */
		$columnRenderers = $this->_getData('column_renderers');
		if (is_null($columnRenderers)) {
			$columnRenderers = array();
		}
		df_assert_array($columnRenderers);
		$this->getEvent()->getGrid()->setData('column_renderers',
				array(self::$COLUMN_TYPE => Df_Sales_Block_Admin_Grid_OrderItemsWrapper::_C)
			+
				$columnRenderers
		);
		return $this;
	}

	/** @return Df_Sales_Model_Handler_AdminOrderGrid_AddProductColumn */
	private function addProductColumn() {
		$this->getEvent()->getGrid()->addColumnAfter(
			'df_products'
			,array('header' => 'Товары', 'type'  => self::$COLUMN_TYPE)
			,$this->getPreviousColumnId()
		);
		return $this;
	}
	
	/** @return string|null */
	private function getPreviousColumnId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				df_a(
					array_keys($this->getEvent()->getGrid()->getColumns())
					// Минус 2, потому что:
					// самый левый столбец с флажками не учитывается
					// администратор ведёт отчёт с 1, а система — с 0.
					,df_cfg()->sales()->orderGrid()->productColumn()->getOrdering() - 2
				)
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter::_C;
	}

	/** @used-by Df_Sales_Observer::rm_adminhtml_block_sales_order_grid__prepare_columns_after() */
	const _C = __CLASS__;
	/** @var string */
	private static $COLUMN_TYPE = 'df_order_grid_products';
}