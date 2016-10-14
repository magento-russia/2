<?php
class Df_Core_Model_Entity_Dependency_Collection extends Df_Varien_Data_Collection {
	/**
	 * @param string $name
	 * @param string $className
	 * @param string $actionSaveClassName
	 * @param string $idFieldName
	 * @param bool $deleteCascade [optional]
	 * @return Df_Core_Model_Entity_Dependency_Collection
	 */
	public function addDependency(
		$name, $className, $actionSaveClassName, $idFieldName, $deleteCascade = false
	) {
		$this->addItem(Df_Core_Model_Entity_Dependency::i(array(
			Df_Core_Model_Entity_Dependency::P__ACTION_SAVE__CLASS_NAME => $actionSaveClassName
			,Df_Core_Model_Entity_Dependency::P__CLASS_NAME => $className
			,Df_Core_Model_Entity_Dependency::P__DELETE_CASCADE => $deleteCascade
			,Df_Core_Model_Entity_Dependency::P__ID_FIELD_NAME => $idFieldName
			,Df_Core_Model_Entity_Dependency::P__NAME => $name
		)));
		return $this;
	}

	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Core_Model_Entity_Dependency::_C;}

	/** @return Df_Core_Model_Entity_Dependency_Collection */
	public static function i() {return new self;}
}