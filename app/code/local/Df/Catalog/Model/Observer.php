<?php
class Df_Catalog_Model_Observer extends Mage_Catalog_Model_Observer {
	/**
	 * @override
	 * Цель перекрытия — ускорение работы перекрываемого метода ядра/
	 * @see Mage_Catalog_Model_Observer::addCatalogToTopmenuItems()
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * Перекрытый родительский метод работает так:
		parent::addCatalogToTopmenuItems($observer);
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function addCatalogToTopmenuItems(Varien_Event_Observer $observer) {
		Df_Catalog_Model_Processor_Menu::i($observer['menu'])->process();
	}
}