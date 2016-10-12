<?php
abstract class Df_Core_Model_Form_Builder extends Df_Core_Model {
	/** @return Df_Core_Model_Form_Builder */
	abstract protected function addFormFields();
	/** @return string */
	abstract protected function getEntityClass();

	/** @return Varien_Data_Form_Element_Fieldset */
	public function getFieldset() {return $this->cfg(self::P__FIELDSET);}

	/** @return Varien_Data_Form */
	public function getForm() {return $this->getFieldset()->getForm();}

	/** @return Df_Core_Model_Form_Builder */
	public function run() {
		$this
			->addFormKey()
			->addFormFieldForEntityId()
			->addFormFields()
			->addFormData()
		;
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $label
	 * @param string $type
	 * @param bool $required[optional]
	 * @param array $config[optional]
	 * @param bool $after[optional]
	 * @return Df_Core_Model_Form_Builder
	 */
	protected function addField(
		$name
		,$label
		,$type
		,$required = false
		,array $config = array()
		,$after = false
	) {
		$this->getFieldset()
			->addField(
				$this->preprocessFieldId($name)
				,$type
				,array_merge(
					array(
						Df_Varien_Data_Form_Element_Abstract::P__NAME =>
							$this->preprocessFieldName($name)
						,Df_Varien_Data_Form_Element_Abstract::P__LABEL => $label
						,Df_Varien_Data_Form_Element_Abstract::P__TITLE => $label
						,Df_Varien_Data_Form_Element_Abstract::P__REQUIRED => $required
					)
					,$config
				)
				,$after
			)
		;
		return $this;
	}

	/** @return array(string => mixed) */
	protected function getDataCalculated() {return array();}

	/** @return array(string => mixed) */
	protected function getDataDefault() {return array();}

	/** @return Df_Core_Model_Entity */
	protected function getEntity() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_Entity $result */
			$result = null;
			if (!$this->isDependent()) {
				$result = Mage::registry($this->getEntityClass());
			}
			else {
				/** @var string $entityClass */
				$entityClass = $this->getEntityClass();
				$result = new $entityClass();
				if (!is_null($this->getEntityParent()->getId())) {
					/** @var string $entityId */
					$entityIdAsString =
						/**
						 * Обратите внимание, что мы основываемся на уникальности
						 * имёт полей идентификаторов в рамках предметной области
						 * (другими словами, что поле идентификатора называются не просто «id»,
						 * а, например, «location_id», «warehouse_id»).
						 * Эта уникальность контролируется методом getIdFieldName()
						 */
						$this->getEntityParent()->getData($result->getIdFieldName())
					;
					df_assert($entityIdAsString);
					/** @var int $entityId */
					$entityId = rm_nat($entityIdAsString);
					$result->load($entityId);
					rm_nat($result->getId());
				}
			}
			df_assert($result instanceof Df_Core_Model_Entity);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return mixed[] */
	protected function getSessionData() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $result */
			$result =
				df_mage()->adminhtml()->session()->getData(
					$this->getEntityClass()
					,$clear = true
				)
			;
			if (is_null($result)) {
				$result = array();
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	protected function isDependent() {return $this->cfg(self::P__DEPENDENT, false);}

	/**
	 * @param string $fieldId
	 * @return string
	 */
	protected function preprocessFieldId($fieldId) {
		return rm_concat_clean('_', $this->getNamespace(), $fieldId);
	}

	/**
	 * @param string $fieldName
	 * @return string
	 */
	protected function preprocessFieldName($fieldName) {
		df_param_string_not_empty($fieldName, 0);
		return !$this->getNamespace() ? $fieldName : $this->getNamespace() . '[' . $fieldName . ']';
	}

	/**
	 * @param string $className
	 * @param string $namespace
	 * @return Df_Core_Model_Form_Builder
	 */
	protected function runDependentBuilder($className, $namespace) {
		/** @var Df_Core_Model_Form_Builder $result */
		$result =
			df_model(
				$className
				,array(
					Df_Core_Model_Form_Location_Builder::P__DEPENDENT => true
					,Df_Core_Model_Form_Location_Builder::P__ENTITY_PARENT => $this->getEntity()
					,Df_Core_Model_Form_Location_Builder::P__FIELDSET => $this->getFieldset()
					,Df_Core_Model_Form_Location_Builder::P__NAMESPACE => $namespace
				)
			)
		;
		df_assert($result instanceof Df_Core_Model_Form_Builder);
		$result->run();
		return $result;
	}

	/** @return Df_Core_Model_Form_Builder */
	private function addFormData() {
		$this->getForm()->addValues($this->getFormData());
		return $this;
	}

	/** @return Df_Core_Model_Form_Builder */
	private function addFormFieldForEntityId() {
		if ($this->getEntity()->getId()) {
			/**
			 * Обратите внимание,
			 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
			 * потому что addField() возвращает не $fieldset, а созданное поле.
			 */
			$this->getFieldset()
				->addField(
					$this->preprocessFieldId($this->getEntity()->getIdFieldName())
					,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
					,array(
						'name' => $this->preprocessFieldName($this->getEntity()->getIdFieldName())
					)
				)
			;
		}
		return $this;
	}

	/** @return Df_Core_Model_Form_Builder */
	private function addFormKey() {
		if (!$this->isDependent()) {
			/**
			 * Обратите внимание,
			 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
			 * потому что addField() возвращает не $fieldset, а созданное поле.
			 */
			$this->getFieldset()
				->addField(
					'form_key'
					,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
					,array(
						'name' => 'form_key'
						,'value' => rm_session_core()->getFormKey()
					)
				)
			;
		}
		return $this;
	}

	/** @return Df_Core_Model_Entity|null */
	private function getEntityParent() {
		/** @var Df_Core_Model_Entity|null $result */
		$result = $this->cfg(self::P__ENTITY_PARENT);
		if ($this->isDependent() || !is_null($result)) {
			df_assert($result instanceof Df_Core_Model_Entity);
		}
		return $result;
	}

	/** @return array */
	private function getFormData() {
		/** @var array $result */
		$result = array();
		foreach ($this->getFormDataRaw() as $fieldIdRaw => $fieldValue) {
			/** @var string $fieldIdRaw */
			/** @var string $fieldValue */
			/** @var string $fieldId */
			$fieldId = $this->preprocessFieldId($fieldIdRaw);
			$result[$fieldId] = $fieldValue;
		}
		return $result;
	}

	/** @return array(string => mixed) */
	private function getFormDataRaw() {
		return
			// Наличие данных в сессии является следствием их невалидности.
			// Администратор должен их исправить.
			$this->getSessionData()
			? $this->getSessionData()
			: (
				is_null($this->getEntity()->getId())
				? $this->getDataDefault()
				: array_merge($this->getEntity()->getData(), $this->getDataCalculated())
			)
		;
	}

	/** @return string */
	private function getNamespace() {return $this->cfg(self::P__NAMESPACE, '');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DEPENDENT, self::V_BOOL, false)
			->_prop(self::P__ENTITY_PARENT, Df_Core_Model_Entity::_CLASS, false)
			->_prop(self::P__FIELDSET, 'Varien_Data_Form_Element_Fieldset')
			->_prop(self::P__NAMESPACE, self::V_STRING, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__DEPENDENT = 'dependent';
	const P__ENTITY_PARENT = 'entity_parent';
	const P__FIELDSET = 'fieldset';
	const P__NAMESPACE = 'namespace';
}