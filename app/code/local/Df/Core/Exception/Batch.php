<?php
class Df_Core_Exception_Batch extends Df_Core_Exception {
	/**
	 * @param Df_Core_Exception_Batch $from
	 * @return void
	 */
	public function addBatch(Df_Core_Exception_Batch $from) {
		$this->_exceptions = array_merge($this->_exceptions, $from->_exceptions);
	}

	/**
	 * @param Df_Core_Exception_Entity $entityException
	 * @return void
	 */
	public function addException(Df_Core_Exception_Entity $entityException) {
		$this->_exceptions[]= $entityException;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMessageRm() {return df_concat_n($this->getMessagesRm());}

	/** @return string[] */
	public function getMessagesRm() {return array_filter(array_map('rm_ets', $this->_exceptions));}

	/** @return bool */
	public function hasExceptions() {return !!$this->_exceptions;}

	/**
	 * @used-by Df_Localization_Onetime_Processor::saveModifiedMagentoEntities()
	 * @uses Df_Core_Exception_Entity::log()
	 * @return void
	 */
	public function log() {df_each($this->_exceptions, 'log');}

	/**
	 * @return null
	 * @throws Df_Core_Exception_Batch
	 */
	public function throwIfNeeed() {
		if ($this->hasExceptions()) {
			throw $this;
		}
	}

	/** @var Df_Core_Exception_Entity[] */
	private $_exceptions = array();
}