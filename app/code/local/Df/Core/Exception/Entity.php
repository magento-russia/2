<?php
class Df_Core_Exception_Entity extends Df_Core_Exception {
	/**
	 * @used-by Df_Dataflow_Model_Registry_Collection::save()
	 * @used-by Df_Varien_Data_Collection::saveModified()
	 * @override
	 * @param Mage_Core_Model_Abstract $entity
	 * @param Exception $exception
	 * @return Df_Core_Exception_Entity
	 */
	public function __construct(Mage_Core_Model_Abstract $entity, Exception $exception) {
		$this->_entity = $entity;
		$this->_exception = $exception;
		parent::__construct();
	}

	/** @return Mage_Core_Model_Abstract */
	public function getEntity() {return $this->_entity;}

	/** @return Exception*/
	public function getException() {return $this->_exception;}

	/**
	 * @override
	 * @return string
	 */
	public function getMessageRm() {
		return strtr(
			"При обработке объекта класса {class} с идентификатором «{id}» произошёл сбой:"
			. "\n«{message}»", array(
				'{class}' => get_class($this->getEntity())
				,'{id}' => $this->getEntity()->getId()
				,'{message}' => rm_ets($this->getException())
			)
		);
	}

	/** @return void */
	public function log() {
		df_notify_exception($this, "Данные сбойного объекта:\n" . rm_dump($this->getEntity()));
	}

	/** @var Mage_Core_Model_Abstract */
	private $_entity;
	/** @var Exception */
	private $_exception;
}