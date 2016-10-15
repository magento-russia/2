<?php
namespace Df\Core\Exception;
use Df\Core\Exception;
class Batch extends Exception {
	/**
	 * @param Batch $from
	 * @return void
	 */
	public function addBatch(Batch $from) {
		$this->_exceptions = array_merge($this->_exceptions, $from->_exceptions);
	}

	/**
	 * @param Entity $entityException
	 * @return void
	 */
	public function addException(Entity $entityException) {$this->_exceptions[]= $entityException;}

	/**
	 * @override
	 * @return string
	 */
	public function message() {return df_cc_n($this->messages());}

	/**
	 * @used-by Df_Localization_Onetime_Processor::saveModifiedMagentoEntities()
	 * @return string[]
	 */
	public function messages() {return array_filter(array_map('df_ets', $this->_exceptions));}

	/** @return bool */
	public function hasExceptions() {return !!$this->_exceptions;}

	/**
	 * @used-by Df_Localization_Onetime_Processor::saveModifiedMagentoEntities()
	 * @uses \Df\Core\Exception\Entity::log()
	 * @return void
	 */
	public function log() {df_each($this->_exceptions, 'log');}

	/** @throws Batch */
	public function throwIfNeeed() {
		if ($this->hasExceptions()) {
			throw $this;
		}
	}

	/** @var Entity[] */
	private $_exceptions = array();
}