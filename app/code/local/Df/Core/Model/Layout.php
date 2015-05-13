<?php
class Df_Core_Model_Layout extends Mage_Core_Model_Layout {
	/**
	 * Этот метод реализует очень важное дополнение к родительскому методу:
	 * он оповещает разработчика о сбоях при создании блоков.
	 * Обратите внимание на метод @see Mage_Core_Model_Layout::createBlock:
		 try {
			 $block = $this->_getBlockInstance($type, $attributes);
		 } catch (Exception $e) {
			 Mage::logException($e);
			 return false;
		 }
	 * При стандартном поведении Magento просто записывает сообщение о сбое в журнал сбоев.
	 * Там это сообщение остаётся, как правило, незамеченным администратором и разработчиком!
	 * @override
	 * @param string $block
	 * @param array $attributes
	 * @return Mage_Core_Block_Abstract
	 * @throws Exception
	 */
	protected function _getBlockInstance($block, array $attributes=array()) {
		/** @var Mage_Core_Block_Abstract $result */
		$result = null;
		try {
			$result = parent::_getBlockInstance($block, $attributes);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true, $sendContentTypeHeader = false);
		}
		return $result;
	}
}