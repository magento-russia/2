<?php
class Df_Catalog_Model_Observer extends Mage_Catalog_Model_Observer {
	/**
	 * Цель перекрытия —
	 * ускорение работы перекрываемого метода ядра
	 * @see Mage_Catalog_Model_Observer::addCatalogToTopmenuItems()
	 * @override
	 * @param Varien_Event_Observer $observer
	 */
	public function addCatalogToTopmenuItems(Varien_Event_Observer $observer) {
		/**
		 * Перекрытый родительский метод работает так:
		 * parent::addCatalogToTopmenuItems($observer);
		 */
		Df_Catalog_Model_Processor_Menu::i($observer->getData('menu'))->process();
	}
}