<?php
/**
 * @method string getInstanceType()
 */
class Df_Widget_Model_Widget_Instance extends Mage_Widget_Model_Widget_Instance {
	/**
	 * @override
	 * @return Df_Widget_Model_Resource_Widget_Instance_Collection
	 */
	public function getResourceCollection() {return self::c();}

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
	 * @return Df_Widget_Model_Resource_Widget_Instance
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Widget_Model_Resource_Widget_Instance::s();}

	/**
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Widget::getEntityClass()
	 * @used-by Df_Localization_Onetime_Processor_Cms_Widget::_construct()
	 * @used-by Df_Widget_Model_Resource_Widget_Instance_Collection::_construct()
	 */
	
	/** @var bool */
	private static $P__NEED_SAVE_RELATIONS = 'need_save_relations';
	/**
	 * @static
	 * @param bool $forUpdating [optional]
	 * @return Df_Widget_Model_Resource_Widget_Instance_Collection
	 */
	public static function c($forUpdating = false) {
		return Df_Widget_Model_Resource_Widget_Instance_Collection::i($forUpdating);
	}
	/** @return Df_Widget_Model_Widget_Instance */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}