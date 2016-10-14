<?php
class Df_Index_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * @param Mage_Index_Model_Process|string $process
	 * @return void
	 */
	public function reindex($process) {
		if (is_string($process)) {
			$process = df_mage()->index()->indexer()->getProcessByCode($process);
		}
		df_assert($process instanceof Mage_Index_Model_Process);
		/** @see Mage_Index_Model_Process::reindexEverything() */
		$process->unsetData('runed_reindexall');
		// @todo Лучше перестраивать расчётные таблицы только для обрабатываемого магазина.
		// Сейчас же мы перестраиваем расчётные таблицы для всех магазинов системы.
		$process->reindexEverything();
	}

	/** @return void */
	public function reindexEverything() {
		/** @uses reindex() */
		df_mage()->index()->indexer()->getProcessesCollection()->walk(array($this, 'reindex'));
	}

	/** @return Df_Index_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}