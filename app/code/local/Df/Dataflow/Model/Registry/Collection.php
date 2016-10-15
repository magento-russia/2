<?php
abstract class Df_Dataflow_Model_Registry_Collection
	extends Df_Core_Model
	implements IteratorAggregate, Countable {
	/** @return Varien_Data_Collection */
	abstract protected function createCollection();
	/** @return string */
	abstract protected function getEntityClass();

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	public function addEntity(Mage_Core_Model_Abstract $entity) {
		$this->validateEntity($entity);
		$this->addEntityToExternalIdMap($entity);
		$this->addEntityToLabelMap($entity);
	}

	/**
	 * @param Zend_Validate_Interface $validator
	 * @return void
	 */
	public function addValidator(Zend_Validate_Interface $validator) {
		$this->getValidator()->addValidator($validator);
	}

	/**
	 * @override
	 * @return int
	 */
	public function count() {return $this->getCollectionRm()->count();}

	/**
	 * @param string $externalId
	 * @return Mage_Core_Model_Abstract|null
	 */
	public function findByExternalId($externalId) {
		// Обратите внимание, что если перед поиском коллекция ещё не была загружена,
		// то она будет загружена автоматически.
		return dfa($this->getMapFromExternalIdToEntity(), $externalId);
	}

	/**
	 * @param int $id
	 * @return Mage_Core_Model_Abstract|null
	 */
	public function findById($id) {
		// Обратите внимание, что если перед поиском коллекция ещё не была загружена,
		// то она будет загружена автоматически.
		return $this->getCollectionRm()->getItemById($id);
	}

	/**
	 * @param string $label
	 * @return Mage_Core_Model_Abstract|null
	 */
	public function findByLabel($label) {
		// Обратите внимание, что если перед поиском коллекция ещё не была загружена,
		// то она будет загружена автоматически.
		return dfa($this->getMapFromLabelToEntity(), $label);
	}

	/**
	 * @override
	 * @return Traversable
	 */
	public function getIterator() {return $this->getCollectionRm()->getIterator();}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	public function removeEntity(Mage_Core_Model_Abstract $entity) {
		$this->validateEntity($entity);
		$this->removeEntityFromExternalIdMap($entity);
		$this->removeEntityFromLabelMap($entity);
	}

	/**
	 * @return void
	 * @throws \Df\Core\Exception\Batch|Exception
	 */
	public function save() {
		/** @var \Df\Core\Exception\Batch $batchException */
		$batchException = new \Df\Core\Exception\Batch();
		df_admin_begin();
		try {
			foreach ($this->getCollectionRm() as $entity) {
				/** @var Mage_Core_Model_Abstract $entity */
				/**
				 * Обратите внимание, что сохранение в Magento — интеллектуальное:
				 * Magento сохраняет только те объекты, свойства которых изменились:
				 * hasDataChanges = true.
				 * Ну, и мы дополнительно вызываем hasDataChanges: не помешает.
				 */
				if ($entity->hasDataChanges()) {
					try {
						$this->saveEntity($entity);
					}
					catch (Exception $e) {
						$batchException->addException(new \Df\Core\Exception\Entity($entity, $e));
					}
				}
			}
		}
		catch (Exception $e) {
			df_admin_end();
			throw $e;
		}
		df_admin_end();
		$batchException->throwIfNeeed();
	}

	/** @return Varien_Data_Collection */
	protected function getCollectionRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->createCollection();
			/**
			 * Почему-то в этот момент элементы коллекций (товарные разделы, самодельные блоки)
			 * уже оказываются помеченными как изменённые.
			 * Может, это некие обработчики загрузки коллекции шерудят?
			 */
			Df_Varien_Data_Collection::unsetDataChanges($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return string|null
	 */
	protected function getEntityExternalId(Mage_Core_Model_Abstract $entity) {
		return $entity->getData(Df_1C_Const::ENTITY_EXTERNAL_ID);
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return string|null
	 */
	protected function getEntityLabel(Mage_Core_Model_Abstract $entity) {return null;}

	/** @return Df_Core_Model_StoreM */
	protected function getStoreDefault() {return df_store();}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	protected function saveEntity(Mage_Core_Model_Abstract $entity) {
		if (!$this->isScopeDefault()) {
			$this->setStoreToEntity($entity, $this->store());
		}
		$entity->save();
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @param Df_Core_Model_StoreM $store
	 * @return void
	 */
	protected function setStoreToEntity(Mage_Core_Model_Abstract $entity, Df_Core_Model_StoreM $store) {}

	/**
	 * @used-by isScopeDefault()
	 * @used-by saveEntity()
	 * @used-by Df_Dataflow_Model_Registry_Collection_Categories::createCollection()
	 * @used-by Df_Dataflow_Model_Registry_Collection_Products::createCollection()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {
		/**
		 * Обратите внимание, что этот метод нельзя записать одной строкй так:
		 * return $this->cfg(self::$P__STORE, $this->getStoreDefault());
		 * потому что getStoreDefault() может быть реализовано как
		 * df()->registry()->getStoreProcessed(),
		 * и такая реализация возбудит исключительную ситуацию,
		 * если система не сможет идентифицировать обрабатываемый магазин по URL.
		 */
		/** @var Df_Core_Model_StoreM $result */
		$result = $this->cfg(self::$P__STORE);
		if (!$result) {
			$result = $this->getStoreDefault();
		}
		return $result;
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 * @throws \Df\Core\Exception
	 */
	protected function validateEntity(Mage_Core_Model_Abstract $entity) {
		if (!$this->getValidator()->isValid($entity)) {
			df_error(df_cc_n($this->getValidator()->getMessages()));
		}
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	private function addEntityToExternalIdMap(Mage_Core_Model_Abstract $entity) {
		// перед добавлением нового элемента надо, разумеется, загрузить всю коллекцию (все элементы)
		$this->getMapFromExternalIdToEntity();
		/** @var string|null $externalId */
		$externalId = $this->getEntityExternalId($entity);
		if ($externalId) {
			df_assert_string_not_empty($externalId);
			$this->{__CLASS__ . '::getMapFromExternalIdToEntity'}[$externalId] = $entity;
		}
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	private function addEntityToLabelMap(Mage_Core_Model_Abstract $entity) {
		// перед добавлением нового элемента надо, разумеется, загрузить всю коллекцию (все элементы)
		$this->getMapFromLabelToEntity();
		/** @var string|null $label */
		$label = $this->getEntityLabel($entity);
		if ($label) {
			df_assert_string_not_empty($label);
			$this->{__CLASS__ . '::getMapFromLabelToEntity'}[$label] = $entity;
		}
	}

	/** @return array(string => Mage_Core_Model_Abstract) */
	private function getMapFromExternalIdToEntity() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Mage_Core_Model_Abstract) $result */
			$result = array();
			foreach ($this->getCollectionRm() as $entity) {
				/** @var Mage_Core_Model_Abstract $entity */
				/** @var string|null $externalId */
				$externalId = $this->getEntityExternalId($entity);
				if (!is_null($externalId)) {
					df_assert_string($externalId);
					$result[$externalId] = $entity;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => Mage_Core_Model_Abstract) */
	private function getMapFromLabelToEntity() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Mage_Core_Model_Abstract) $result */
			$result = array();
			foreach ($this->getCollectionRm() as $entity) {
				/** @var Mage_Core_Model_Abstract $entity */
				/** @var string|null $label */
				$label = $this->getEntityLabel($entity);
				if ($label) {
					df_assert_string($label);
					$result[$label] = $entity;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Validate */
	private function getValidator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Validate();
			$this->{__METHOD__}->addValidator(\Df\Zf\Validate\ClassT::i($this->getEntityClass()));
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isScopeDefault() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->store()->getId() === df_store_id();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	private function removeEntityFromExternalIdMap(Mage_Core_Model_Abstract $entity) {
		$this->getMapFromExternalIdToEntity();
		/** @var string|null $externalId */
		$externalId = $this->getEntityExternalId($entity);
		if ($externalId) {
			df_assert_string($externalId);
			unset($this->{__CLASS__ . '::getMapFromExternalIdToEntity'}[$externalId]);
		}
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	private function removeEntityFromLabelMap(Mage_Core_Model_Abstract $entity) {
		$this->getMapFromLabelToEntity();
		/** @var string|null $label */
		$label = $this->getEntityLabel($entity);
		if ($label) {
			df_assert_string($label);
			unset($this->{__CLASS__ . '::getMapFromLabelToEntity'}[$label]);
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__STORE, Df_Core_Model_StoreM::_C, false);
	}

	const _C = __CLASS__;
	/** @var string */
	protected static $P__STORE = 'store';
}