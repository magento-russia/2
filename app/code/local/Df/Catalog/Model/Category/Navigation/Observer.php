<?php
class Df_Catalog_Model_Category_Navigation_Observer extends Df_Core_Model {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_block_abstract_to_html_after(Varien_Event_Observer $observer) {
		if (self::isHookRight()) {
			if (!$this->_catalogNavigationInserted) {
				if (!$this->_inProcessing) {
					$this->_inProcessing = true;
					/**
					 * При загрузке главной страницы мы сюда попадаем 55 раз
					 */
					$this->_catalogNavigationInserted =
						$this->getInserter($observer)->insert()
					;
					$this->_inProcessing = false;
				}
			}
		}
	}
	/**
	 * При обработке текущего блока мы создаём новые блоки,
	 * и нам надо избежать бесконечной рекурсии
	 * @var bool
	 */
	private $_inProcessing = false;
	/** @var bool */
	private $_catalogNavigationInserted = false;

	/**
	 * @param Varien_Event_Observer $observer
	 * @return Df_Catalog_Model_Category_Content_Inserter
	 */
	private function getInserter(Varien_Event_Observer $observer) {
		return
			Df_Catalog_Model_Category_Content_Inserter::i(
				array(
					Df_Catalog_Model_Category_Content_Inserter::P__OBSERVER => $observer
					,Df_Catalog_Model_Category_Content_Inserter::P__BLOCK_TO_INSERT =>
						$this->getNavigationBlock()
				)
			)
		;
	}

	/** @return Df_Catalog_Block_Category_Navigation */
	private function getNavigationBlock() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_block(new Df_Catalog_Block_Category_Navigation());
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;

	/**
	 * Определяет, подлежит ли текущий блок обработке
	 * @return bool
	 */
	private static function isHookRight() {
		/** @var bool $result */
		static $result;
		if (!isset($result)) {
			$result =
					df_enabled(Df_Core_Feature::TWEAKS)
				&&
					df_cfg()->catalog()->navigation()->getEnabled()
				&&
					!df_is_admin()
				&&
					rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
			;
		}
		return $result;
	}

}