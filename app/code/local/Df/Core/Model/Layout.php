<?php
class Df_Core_Model_Layout extends Mage_Core_Model_Layout {
	/**
	 * Публичный доступ к системному методу @uses _getBlockInstance()
	 * @used-by df_block()
	 * @param string|Mage_Core_Block_Abstract $block
	 * @param array(string => mixed) $attributes
	 * @return Mage_Core_Block_Abstract
	 */
	public function getBlockInstance($block, array $attributes=array()) {
		return $this->_getBlockInstance($block, $attributes);
	}

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
	 * @param string|Mage_Core_Block_Abstract $block
	 * @param array(string => mixed) $attributes
	 * @return Mage_Core_Block_Abstract
	 * @throws Exception
	 */
	protected function _getBlockInstance($block, array $attributes=array()) {
		/** @var Mage_Core_Block_Abstract $result */
		try {
			$result = parent::_getBlockInstance($block, $attributes);
		}
		catch (Exception $e) {
			df_notify_exception($e);
			/** @var string $commonPart */
			$commonPart = "\nСмотрите отчёт в папке var/log.";
			if (is_string($block)) {
				df_error("Не могу создать блок класса «{$block}».{$commonPart}");
			}
			else if (is_object($block)) {
				df_error("Класс «%s» недопустим для блока.{$commonPart}", get_class($block));
			}
			else {
				throw $e;
			}
		}
		return $result;
	}
}