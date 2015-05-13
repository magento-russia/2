<?php
abstract class Df_Shipping_Model_Config_Backend_Validator_Strategy
	extends Df_Admin_Model_Config_Backend_Validator_Strategy {
	/**
	 * Система показывает администратору диагностические сообщения над настройками всех модулей.
	 * Чтобы администратор знал, к какому конкретно модулю относится диагностическое сообщение,
	 * надо определить название модуля, что и делает данный метод.
	 * @return string
	 */
	protected function getModuleTitle() {
		/** @var string $result */
		$result =
			df_mage()->adminhtml()->getConfig()
				->getSystemConfigNodeLabel(
					'df_shipping'
					,$this->getBackend()->getData('group_id')
				)
		;
		df_result_string($result);
		return $result;
	}

	const _CLASS = __CLASS__;

}