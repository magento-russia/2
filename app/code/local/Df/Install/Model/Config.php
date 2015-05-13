<?php
class Df_Install_Model_Config extends Mage_Install_Model_Config {
	/**
	 * Цель перекрытия —
	 * предоставление программной возможности удаления шагов установки Magento.
	 * Эта возможность используется для удаления шага принятия лицензионного соглашения.
	 * @override
	 * @return Varien_Object[]
	 */
	public function getWizardSteps() {
		/** @var Varien_Object[] $result */
		$result = parent::getWizardSteps();
		/** @var int[] $indicesToRemove */
		$indicesToRemove = array();
		foreach ($result as $index => $step) {
			/** @var int $index */
			/** @var Varien_Object $step */
			if ('true' === $step->getData('remove')) {
				$indicesToRemove[]= $index;
			}
		}
		return array_diff_key($result, array_flip($indicesToRemove));
	}
}