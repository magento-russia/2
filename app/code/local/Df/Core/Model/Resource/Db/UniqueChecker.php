<?php
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
 */
class Df_Core_Model_Resource_Db_UniqueChecker extends Df_Core_Model {
	/** @return array(string => string|int|float|null|bool) */
	private function getDataToSave() {return $this->cfg(self::$P__DATA_TO_SAVE);}

	/** @return Mage_Core_Model_Abstract */
	private function getModel() {return $this->cfg(self::$P__MODEL);}

	/** @return Mage_Core_Model_Resource_Db_Abstract */
	private function getResourceForModel() {return $this->getModel()->getResource();}

	/** @return Varien_Db_Adapter_Interface */
	private function getWriteAdapterForModel() {return $this->cfg(self::$P__WRITE_APADTER);}

	/** @return void */
	private function process() {
		/** @var Mage_Core_Model_Abstract $model */
		$model = $this->getModel();
		/** @var string[] $existent */
		$existent = [];
		/** @var array(array(string => string|string[])) $fields */
		$fields = $this->getResourceForModel()->getUniqueFields();
		if (!empty($fields)) {
			/**
			 * В оригинальном коде
			 * @see Mage_Core_Model_Resource_Db_Abstract::_checkUnique()
			 * @see Mage_Core_Model_Mysql4_Abstract::_checkUnique()
			 * на этом месте вместо нашего кода
			 * $fields = array(array('field' => $fields, 'title' => $fields));
			 * стоит странный, и, по видимому, ошибочный код:
				if (!is_array($fields)) {
					$this->_uniqueFields = array(
						array(
							'field' => $fields,
							'title' => $fields
					));
				}
			 * Ошибочный он потому, что раз $fields не является массивом,
			 * то мы не можем применять к нему foreach ниже.
			 * Видимо, этот код пришёл из каких-то совсем устаревших версий Magento CE
			 * ради совместимости с какими-то сторонними модулями,
			 * но такой совместимости всё равно не добивается.
			 */
			df_check_array($fields);
			/** @var Varien_Object $data */
			$data = new Varien_Object($this->getDataToSave());
			$select = $this->getWriteAdapterForModel()->select();
			$select->from($this->getResourceForModel()->getMainTable());
			foreach ($fields as $unique) {
				/** @var array(string => string|string[]) $unique */
				$select->reset(Zend_Db_Select::WHERE);
				/**
				 * Поле «field» может быть массивом.
				 * @see Mage_Core_Model_Resource_Url_Rewrite::_initUniqueFields():
						$this->_uniqueFields = array(
							array(
								'field' => array('id_path','store_id','is_system'),
								'title' => Mage::helper('core')->__('ID Path for Specified Store')
							),
							array(
								'field' => array('request_path','store_id'),
								'title' => Mage::helper('core')->__('Request Path for Specified Store'),
							)
						);
				 */
				if (is_array($unique['field'])) {
					foreach ($unique['field'] as $field) {
						$select->where($field . '=?', trim($data->getData($field)));
					}
				}
				else {
					$select->where($unique['field'] . '=?', trim($data->getData($unique['field'])));
				}
				/**
				 * Вот только ради второй части этого условия ($this->getModel()->getId() === '0')
				 * мы и городили весь этот класс!
				 * Смотрите комментарий в шапке класса с подробными объяснениями:
				 * @see Df_Core_Model_Resource_Db_UniqueChecker
				 *
				 * 2015-08-03
				 * Обратите внимание, что у нас метод @see Df_Core_Model_StoreM::getId()
				 * возвращает целое число, а не строку,
				 * и стандартная проверка ядра $object->getId() === '0' уже не является корректной,
				 * поэтому добавил ещё одно условие.
				 */
				if ($model->getId() || $model->getId() === '0' || 0 === $model->getId()) {
					$select->where($model->getIdFieldName() . '!=?', $model->getId());
				}
				$test = $this->getWriteAdapterForModel()->fetchRow($select);
				if ($test) {
					$existent[] = $unique['title'];
				}
			}
		}
		if (!empty($existent)) {
			if (count($existent) == 1 ) {
				$error = Mage::helper('core')->__('%s already exists.', $existent[0]);
			}
			else {
				$error = Mage::helper('core')->__('%s already exist.', df_csv_pretty($existent));
			}
			// 2015-08-03
			// Добавляем дополнительное логирование.
			df_notify_exception('Магазин не прошёл проверку на уникальность', df_dump($model->getData()));
			Mage::throwException($error);
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DATA_TO_SAVE, DF_V_ARRAY)
			->_prop(self::$P__MODEL, 'Mage_Core_Model_Abstract')
			->_prop(self::$P__WRITE_APADTER, 'Varien_Db_Adapter_Interface')
		;
	}
	/** @var string */
	private static $P__DATA_TO_SAVE = 'data_to_save';
	/** @var string */
	private static $P__MODEL = 'model';
	/** @var string */
	private static $P__WRITE_APADTER = 'write_apadter';

	/**
	 * @param Mage_Core_Model_Abstract $model
	 * @param Varien_Db_Adapter_Interface $writeAdapter
	 * @param array(string => string|int|float|null|bool) $dataToSave
	 * @return void
	 * @throws Mage_Core_Exception
	 */
	public static function check(
		Mage_Core_Model_Abstract $model, Varien_Db_Adapter_Interface $writeAdapter, array $dataToSave) {
		/** @var Df_Core_Model_Resource_Db_UniqueChecker $i */
		$i = new self(array(
			self::$P__DATA_TO_SAVE => $dataToSave
			, self::$P__MODEL => $model
			, self::$P__WRITE_APADTER => $writeAdapter
		));
		$i->process();
	}
}