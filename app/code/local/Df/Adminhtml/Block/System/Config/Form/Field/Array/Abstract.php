<?php
class Df_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
	extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {
	/**
	 * Add a column to array-grid
	 * @override
	 * @param string $name
	 * @param array|Varien_Object $params
	 */
	public function addColumn($name, $params) {
		if (is_array($params)) {
			parent::addColumn($name, $params);
		}
		else {
			df_assert($params instanceof Varien_Object);
			$params->setData(
				array_merge(
					array(
						'label' => 'Column'
						,'size' => false
						,'style' => null
						,'class' => null
						,'renderer' => false
						,'index' => $name
						,'name' => $name
					)
					,$params->getData()
				)
			);
			$this->_columns[$name] = $params;
		}
	}

	/** @return Varien_Data_Form_Element_Abstract */
	public function getElement() {return $this->_getData('element');}

	/**
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		/**
		 * Назначение конкретно этому элементу конкретно этого идентификатора
		 * необходимо для корректного скрытия/показа элемента
		 * при зависимости его от значений других элементов
		 * @see FormElementDependenceController::trackChange():
			if (!$(idTo)) {
				return;
			}
		 */
		return strtr(
			"<div id='{id}'>{inner}</div>", array(
				'{id}' => $element->getHtmlId()
				,'{inner}' => parent::_getElementHtml($element)
			)
		);
	}

	/**
	 * Render array cell for prototypeJS template
	 * @override
	 * @throws Exception
	 * @param string $columnName
	 * @return string
	*/
	protected function _renderCellTemplate($columnName) {
		/** @var array|Varien_Object $column */
		$column = df_a($this->_columns, $columnName);
		/** @var string $result */
		$result = null;
		if (!is_object($column)) {
			$result = parent::_renderCellTemplate($columnName);
		}
		else {
			df_assert($column instanceof Varien_Object);
			/** @var Varien_Object $renderer */
			$renderer = $column->getData('renderer');
			if (!($renderer instanceof Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface)) {
				$result = parent::_renderCellTemplate($columnName);
			}
			else {
				/** @var string $inputName */
				$inputName = $this->getInputNameByColumnName($columnName);
				df_assert_string_not_empty($inputName);
				/** @var array $attributes */
				$attributes = $column->getData('attributes');
				if (is_null($attributes)) {
					$attributes = array();
				}
				$attributes['class'] = $columnName;
				$column->setData('attributes', $attributes);
				/**
				 * Имя столбца станет именем поля
				 */
				$column->setData('name', $inputName);
				/** @var Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface $renderer */
				$renderer->setDataUsingMethod('input_name', $inputName);
				$renderer->setDataUsingMethod('column_name', $columnName);
				$renderer->setColumn($column);
				$result = df_text()->removeLineBreaks(rm_ejs(
					$renderer->render(new Varien_Object())
					. strtr(
						'<script type=\'text/javascript\'>
							jQuery(function() {
								var jRow = jQuery(document.getElementById(\'#{_id}\'));
								var jSelect = jQuery(\'.%columnName%\', jRow);
								jSelect.val(\'#{%columnName%}\');
							});
						</script>'
						,array('%columnName%' => $columnName)
					)
				));
			}
		}
		return $result;
	}

	/**
	 * @param string $columnName
	 * @return string
	 */
	private function getInputNameByColumnName($columnName) {
		return
			rm_sprintf(
				'%s[#{_id}][%s]'
				,$this->getElement()->getName()
				,$columnName
			)
		;
	}

	const _CLASS = __CLASS__;
}