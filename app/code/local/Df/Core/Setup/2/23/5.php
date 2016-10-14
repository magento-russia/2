<?php
class Df_Core_Setup_2_23_5 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$this->processStores();
		$this->processStoreGroups();
		$this->processWebsites();
	}

	/**
	 * @param Varien_Data_Collection $collection
	 * @param string $translatorMethod
	 * @return void
	 */
	private function processCollection(Varien_Data_Collection $collection, $translatorMethod) {
		foreach ($collection as $entity) {
			/** @var Mage_Core_Model_Abstract $entity */
			$this->processEntity($entity, $translatorMethod);
		}
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @param string $translatorMethod
	 * @return void
	 */
	private function processEntity(Mage_Core_Model_Abstract $entity, $translatorMethod) {
		/**
		 * Magento Community Edition версий ниже 1.7.0.0
		 * содержит дефект, который приводит к сбою при сохранении объектов с нулевым идентификатором.
		 * Правильный код (смотреть в Magento CE не ниже 1.7.0.0):
		 * @see Mage_Core_Model_Resource_Db_Abstract::_checkUnique():
			if ($object->getId() || $object->getId() === '0') {
			  $select->where($this->getIdFieldName() . '!=?', $object->getId());
		 	}
		 * Дефектный код (смотреть в Magento CE ниже 1.7.0.0):
		 * @see Mage_Core_Model_Resource_Db_Abstract::_checkUnique():
		 * @see Mage_Core_Model_Mysql4_Abstract::_checkUnique():
			if ($object->getId()) {
			  $select->where($this->getIdFieldName().' != ?', $object->getId());
			}
		 *
		 * 2015-08-03
		 * Раньше тут стоял код:
		 *
			static $hasCheckUniqueBug;
			if (is_null($hasCheckUniqueBug)) {
				$hasCheckUniqueBug = df_magento_version('1.7.0.0', '<');
			}
			if (!$hasCheckUniqueBug || (0 < rm_nat0($entity->getId()))) {
				$entity->setDataUsingMethod(
					'name', $this->$translatorMethod($entity->getDataUsingMethod('name'))
				);
				$entity->save();
			}
		 *
		 * В принципе, для объектов класса @see Df_Core_Model_Store
		 * эту проблему мы уже решили классом @see Df_Core_Model_Resource_Db_UniqueChecker:
		 * @see Df_Core_Model_Resource_Store::_checkUnique().
		 * А другие объекты, обрабатываемые в текущем классе,
		 * не имеют нулевых идентификаторов.
		 * Так что отсюда проверку на дефект убрал.
		 */
		$entity->setDataUsingMethod(
			'name', $this->$translatorMethod($entity->getDataUsingMethod('name'))
		);
		$entity->save();
	}

	/** @return void */
	private function processStoreGroups() {
		// Переводим англоязычные назания «Default», «Main Website Store»
		$this->processCollection(Df_Core_Model_Store_Group::c(true), 'translateStoreGroupName');
	}

	/** @return void */
	private function processStores() {
		// Переводим англоязычные назания «Admin», «Default Store View»
		$this->processCollection(Df_Core_Model_Store::c(true), 'translateStoreName');
	}

	/** @return void */
	private function processWebsites() {
		// Переводим англоязычные назания «Admin», «Main Website»
		$this->processCollection(Df_Core_Model_Website::c(true), 'translateWebsiteName');
	}

	/**
	 * @param string $name
	 * @param array(string => string) $dictionary
	 * @return string
	 */
	private function translate($name, array $dictionary) {return df_a($dictionary, $name, $name);}

	/**
	 * @param string $name
	 * @return string
	 */
	private function translateStoreGroupName($name) {
		return $this->translate($name, array(
			'Default' => 'магазин по умолчанию'
			,'Main Website Store' => 'основной магазин'
		));
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function translateStoreName($name) {
		return $this->translate($name, array(
			'Admin' => 'административная витрина'
			,'Default Store View' => 'основная витрина'
		));
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function translateWebsiteName($name) {
		return $this->translate($name, array(
			'Admin' => 'административный сайт'
			,'Main Website' => 'основной сайт'
		));
	}
}