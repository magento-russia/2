<?php
class Df_Varien_Data_Form extends Varien_Data_Form {
	/**
	 * @param string $attributeName
	 * @param string $attributeValue
	 * @return Df_Varien_Data_Form
	 */
	public function addAdditionalHtmlAttribute($attributeName, $attributeValue) {
		df_param_string($attributeName, 0);
		df_param_string($attributeValue, 1);
		$this->setData($attributeName, $attributeValue);
		$this->_additionalHtmlAttributes[]= $attributeName;
		return $this;
	}

	/**
	 * @param string $fieldId
	 * @param string $fieldName
	 * @param mixed $fieldValue
	 * @return Df_Varien_Data_Form
	 */
	public function addHiddenField($fieldId, $fieldName, $fieldValue) {
		df_param_string($fieldId, 0);
		df_param_string($fieldName, 1);
		if (is_array($fieldValue)) {
			$this->addHiddenFieldWithMultipleValues($fieldId, $fieldName, $fieldValue);
		}
		else if ($fieldValue instanceof Df_Varien_Data_Form_Processor) {
			/** @var Df_Varien_Data_Form_Processor $fieldValue */
			$fieldValue->setForm($this);
			$fieldValue->process();
		}
		else {
			$this
				->addField(
					$fieldId
					,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
					,array(
						 'name' => $fieldName
						 , 'value' => $fieldValue
					)
				)
			;
		}
		return $this;
	}

	/**
	 * @param string $fieldId
	 * @param string $fieldName
	 * @param array $fieldValue
	 * @return Df_Varien_Data_Form
	 */
	public function addHiddenFieldWithMultipleValues($fieldId, $fieldName, array $fieldValue) {
		df_param_string($fieldId, 0);
		df_param_string($fieldName, 1);
		foreach ($fieldValue as $subFieldName => $subFieldValue) {
			/** @var string|int $subFieldName */
			/** @var mixed $subFieldValue */
			if (!is_int($subFieldName)) {
				df_assert_string($subFieldName);
			}
			$this->addHiddenField(
				implode('_', array($fieldId, df_string($subFieldName)))
				/**
				 * http://php.net/manual/reserved.variables.post.php
				 *
					you may have multidimensional array in form inputs

					HTML Example:

					<input name="data[User][firstname]" type="text" />
					<input name="data[User][lastname]" type="text" />
					...

					Inside php script
					after submit you can access the individual element like so:

					$firstname = $_POST['data']['User']['firstname'];
				 */
				,sprintf('%s[%s]', $fieldName, df_string($subFieldName))
				,$subFieldValue
			);
		}
		return $this;
	}

	/**
	 * @param array $fields
	 * @return Df_Varien_Data_Form
	 */
	public function addHiddenFields(array $fields) {
		foreach ($fields as $fieldName => $fieldValue) {
			/** @var string $fieldName */
			/** @var mixed $fieldValue */
			df_assert_string($fieldName);
			$this->addHiddenField($fieldName, $fieldName, $fieldValue);
		}
		return $this;
	}

	/** @return array */
	public function getHtmlAttributes() {
		/** @var array $result */
		$result =
			array_merge(
				parent::getHtmlAttributes()
				,$this->getAdditionalHtmlAttributes()
			)
		;
		df_result_array($result);
		return $result;
	}

	/**
	 * @param string $action
	 * @return Df_Varien_Data_Form
	 */
	public function setAction($action) {
		df_param_string($action, 0);
		$this->setData('action', $action);
		return $this;
	}

	/**
	 * @param string $method
	 * @return Df_Varien_Data_Form
	 */
	public function setMethod($method) {
		df_param_string($method, 0);
		$this->setData('method', $method);
		return $this;
	}

	/**
	 * @param string $name
	 * @return Df_Varien_Data_Form
	 */
	public function setName($name) {
		df_param_string($name, 0);
		$this->setData('name', $name);
		return $this;
	}

	/**
	 * @param bool $useContainer
	 * @return Df_Varien_Data_Form
	 */
	public function setUseContainer($useContainer) {
		df_param_boolean($useContainer, 0);
		$this->setData('use_container', $useContainer);
		return $this;
	}

	/** @return array */
	private function getAdditionalHtmlAttributes() {
		/** @var array $result */
		$result = $this->_additionalHtmlAttributes;
		df_result_array($result);
		return $result;
	}
	/** @var array */
	private $_additionalHtmlAttributes = array();
}