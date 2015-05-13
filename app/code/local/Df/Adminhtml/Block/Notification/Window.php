<?php
class Df_Adminhtml_Block_Notification_Window extends Mage_Adminhtml_Block_Notification_Window {
	/**
	 * Переопределяем этот метод лишь для того, чтобы избежать сбоя:
	 * «Notice: Undefined property: Mage_Adminhtml_Block_Notification_Window::$_aclResourcePath».
	 *
	 * Причиной этого сбоя является дефект родительского метода:
	 * там код пытается использовать несуществующую переменную $this->_aclResourcePath
	 * Я внимательно проверил: этот код никак не связан с остальной частью системы,
	 * переменная и ее данные нигде не используются.
	 * Видимо, код остался от устаревших версий Magento.
	 *
	 * Ранее сбой не возникал, потому что в классе Varien_Object
	 * присутствовал магический метод __get.
	 * Я этот магический метод удалил, он мешает новой архитектуре Российской сборки Magento.
	 *
	 * @override
	 * @return bool
	 */
	protected function _isAllowed() {return true;}
}