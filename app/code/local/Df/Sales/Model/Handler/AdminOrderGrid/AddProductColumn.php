<?php
/**
 * @method Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter getEvent()
 */
class Df_Sales_Model_Handler_AdminOrderGrid_AddProductColumn extends Df_Core_Model_Handler {
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
		$this->getEvent()->getGrid()
			->setData(
				'column_renderers'
				,array_merge(
					$columnRenderers
					,array(
						self::COLUMN_TYPE__DF_ORDER_GRID_PRODUCTS =>
							Df_Sales_Block_Admin_Widget_Grid_Column_Renderer_Products::_CLASS
					)
				)
			)
		;
		return $this;
	}

	/** @return Df_Sales_Model_Handler_AdminOrderGrid_AddProductColumn */
	private function addProductColumn() {
		$this->getEvent()->getGrid()
			->addColumnAfter(
				'df_products'
				,array(
					'header' => 'Товары'
					,'type'  => self::COLUMN_TYPE__DF_ORDER_GRID_PRODUCTS
				)
				,$this->getPreviousColumnId()
			)
		;
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
		return Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter::_CLASS;
	}

	const _CLASS = __CLASS__;
	const COLUMN_TYPE__DF_ORDER_GRID_PRODUCTS = 'df_order_grid_products';
}