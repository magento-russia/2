<?php
/**
 * @method string getInstanceType()
 */
class Df_Widget_Model_Widget_Instance extends Mage_Widget_Model_Widget_Instance {
	/** @return bool */
	public function needSaveRelations() {
		/** @var bool $result */
		$result = $this->getData(self::$P__NEED_SAVE_RELATIONS);
		return is_null($result) ? true : $result;
	}

	/** @return Df_Widget_Model_Widget_Instance */
	public function skipSaveRelations() {
		$this->setData(self::$P__NEED_SAVE_RELATIONS, false);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Widget_Model_Widget_Instance
	 */
	protected function _beforeSave() {
		if ($this->needSaveRelations()) {
			parent::_beforeSave();
		}
		else {
			// заимствуем от родителя код сохранения параметров виджета
			if (is_array($this->getData('widget_parameters'))) {
				$this->setData('widget_parameters', serialize($this->getData('widget_parameters')));
			}
			// Выполняем код дедушки
			if (!$this->getId()) {
				$this->isObjectNew(true);
			}
			Mage::dispatchEvent('model_save_before', array('object' => $this));
			Mage::dispatchEvent($this->_eventPrefix.'_save_before', $this->_getEventData());
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Widget_Model_Resource_Widget_Instance::mf());
	}
	const _CLASS = __CLASS__;
	/** @var bool */
	private static $P__NEED_SAVE_RELATIONS = 'need_save_relations';

	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Widget_Model_Widget_Instance */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}