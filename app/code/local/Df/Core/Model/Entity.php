<?php
abstract class Df_Core_Model_Entity extends Df_Core_Model {
	/** @return string */
	abstract public function getName();

	/** @return Df_Core_Model_Entity_Dependency_Collection */
	public function getDependenciesInfo() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Entity_Dependency_Collection::i();
			$this->initDependenciesInfo();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $name
	 * @return Df_Core_Model_Entity
	 */
	public function getDependencyByName($name) {
		df_param_string($name, 0);
		if (!isset($this->{__METHOD__}[$name])) {
			/** @var Df_Core_Model_Entity_Dependency $dependencyInfo */
			$dependencyInfo = $this->getDependenciesInfo()->getItemById($name);
			df_assert($dependencyInfo instanceof Df_Core_Model_Entity_Dependency);
			/** @var Df_Core_Model_Entity $result */
			$result = df_model($dependencyInfo->getEntityClassName());
			df_assert($result instanceof Df_Core_Model_Entity);
			/** @var int $dependencyId */
			$dependencyId = rm_nat0($this->cfg($dependencyInfo->getEntityIdFieldName()));
			if (0 < $dependencyId) {
				$result->load($dependencyId);
			}
			$this->{__METHOD__}[$name] = $result;
		}
		return $this->{__METHOD__}[$name];
	}

	/**
	 * @override
	 * @return int|null
	 */
	public function getId() {
		return
			is_null(parent::getId())
			/**
			 * Для несохранявшихся ранее в базу данных объектов
			 * надо возвращать именно null, а не 0,
			 * потому что если вернуть 0, то не сработает проверка
			 * (!is_null($object->getId()) && (!$this->_useIsObjectNew || !$object->isObjectNew()))
			 * в методе @used-by Mage_Core_Model_Resource_Db_Abstract::save()
			 */
			? parent::getId()
			: (int)parent::getId()
		;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getIdFieldName() {
		/** @var string $result */
		$result = parent::getIdFieldName();
		/**
		 * Вместо «id» используйте уникальное в рамках предметной области
		 * имя поля для идентификатора, например: «location_id», «warehouse_id».
		 * На уникальности имен идентификатора в рамках предметной области
		 * основывается, например, алгоритм метода @used-by Df_Core_Model_Form_Builder::getEntity()
		 */
		df_assert_ne('id', $result);
		return $result;
	}

	/** @return string */
	public function getSessionKey() {return get_class($this);}

	/** @return Df_Core_Model_Entity */
	protected function initDependenciesInfo() {return $this;}

	/** @used-by Df_Core_Model_Form_Builder::_construct() */
	const _C = __CLASS__;
	const DEPENDENCY_INFO__CLASS_NAME_MF = 'class_name';
	const DEPENDENCY_INFO__DELETE_CASCADE = 'delete_cascade';
	const DEPENDENCY_INFO__ID_FIELD_NAME = 'id_field_name';
}