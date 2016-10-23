<?php
abstract class Df_Shipping_Config_Backend_Validator_Strategy
	extends Df_Admin_Config_Backend_Validator_Strategy {
	/**
	 * Система показывает администратору диагностические сообщения над настройками всех модулей.
	 * Чтобы администратор знал, к какому конкретно модулю относится диагностическое сообщение,
	 * надо определить название модуля, что и делает данный метод.
	 *
	 * Обратите внимание, что родительская реализация @see Df_Core_Model::moduleTitle()
	 * нас не устраивает, потому что, например, потомок нашего класса
	 * @see Df_Shipping_Config_Backend_Validator_Strategy_Origin_SpecificCountry
	 * будет использоваться совершенно разными модулями доставки.
	 *
	 * @override
	 * @see Df_Core_Model::moduleTitle()
	 * @return string
	 */
	protected function moduleTitle() {
		return df_config_adminhtml()->getSystemConfigNodeLabel(
			'df_shipping', $this->getBackend()->getData('group_id')
		);
	}
}