<?php
abstract class Df_1C_Cml2_Import_Processor_Order_Item extends Df_1C_Cml2_Import_Processor {
	/**
	 * @override
	 * @return Df_1C_Cml2_Import_Data_Entity_Order_Item
	 */
	protected function getEntity() {return parent::getEntity();}

	/** @return Df_1C_Cml2_Import_Data_Entity_Order */
	protected function getEntityOrder() {return $this->cfg(self::$P__ENTITY_ORDER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__ENTITY, Df_1C_Cml2_Import_Data_Entity_Order_Item::_C)
			->_prop(self::$P__ENTITY_ORDER, Df_1C_Cml2_Import_Data_Entity_Order::_C)
		;
	}
	/** @var string */
	private static $P__ENTITY_ORDER = 'entity_order';

	/**
	 * @used-by Df_1C_Cml2_Import_Processor_Order::orderItemsProcess()
	 * @param string $class
	 * @param Df_1C_Cml2_Import_Data_Entity_Order_Item $orderItem
	 * @param Df_1C_Cml2_Import_Data_Entity_Order $order
	 * @return Df_1C_Cml2_Import_Processor_Order_Item
	 */
	public static function ic(
		$class
		, Df_1C_Cml2_Import_Data_Entity_Order_Item $orderItem
		, Df_1C_Cml2_Import_Data_Entity_Order $order
	) {
		return df_ic($class, __CLASS__, array(
			self::$P__ENTITY => $orderItem, self::$P__ENTITY_ORDER => $order
		));
	}
}