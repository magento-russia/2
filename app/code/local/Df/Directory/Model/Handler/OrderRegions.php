<?php
/** @method Df_Core_Model_Event_Core_Collection_Abstract_LoadBefore getEvent() */
class Df_Directory_Model_Handler_OrderRegions extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		$this->getEvent()->getCollection()->getSelect()->order(array(
			'rname.name ASC', 'main_table.default_name ASC'
		));
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {return Df_Core_Model_Event_Core_Collection_Abstract_LoadBefore::_C;}

	/** @used-by Df_Directory_Observer::core_collection_abstract_load_before() */
	const _C = __CLASS__;
}