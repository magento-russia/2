<?php
/**
 * КЭШИРОВАНИЕ НАДО РЕАЛИЗОВЫВАТЬ КРАЙНЕ ОСТОРОЖНО!!!
 * Система использует данный класс и его потмков как одиночек:
 * $renderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
 *
 * @see Mage_Adminhtml_Block_System_Config_Form::initFields():
	if ($element->frontend_model) {
		$fieldRenderer = Mage::getBlockSingleton((string)$element->frontend_model);
	} else {
		$fieldRenderer = $this->_defaultFieldRenderer;
	}
 *
 * Этот класс перекрывает системный класс @see Mage_Adminhtml_Block_System_Config_Form_Field
 * ради устранения сбоя
 * «Warning: Illegal string offset 'value'
 * in app/code/core/Mage/Adminhtml/Block/System/Config/Form/Field.php»,
 * вызаванного дефектом в блоке кода
	elseif ($v['value']==$defText) {
		$defTextArr[] = $v['label'];
		break;
	}
 * метода системного класса @see Mage_Adminhtml_Block_System_Config_Form_Field::render().
 * @link http://magento-forum.ru/topic/4524/
 * Обратите внимание, что хотя в этой теме сам сбой и способ его воспроизведения описаны верно,
 * однако та заплатка, которую я выпустил в день опубликования темы (2014-07-11), была неверной.
 * Новая, верная заплатка выпущена 2014-07-14.
 */
class Df_Adminhtml_Block_System_Config_Form_Field extends Mage_Adminhtml_Block_System_Config_Form_Field {
	/**
	 * Цель перекрытия —
	 * заплатка к методу @see Mage_Adminhtml_Block_System_Config_Form_Field::render(),
	 * который в некоторых ситуациях может приводить к сбою.
	 * Смотрите подробный комментарий внутри метода
	 * @see Df_Adminhtml_Block_System_Config_Form_Field::render().
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element) {
		$id = $element->getHtmlId();
		$html = '<td class="label"><label for="'.$id.'">'.$element->getLabel().'</label></td>';
		$isMultiple = $element->getExtType()==='multiple';

		// replace [value] with [inherit]
		$namePrefix = preg_replace('#\[value\](\[\])?$#', '', $element->getName());

		$options = $element->getValues();

		$addInheritCheckbox = false;
		if ($element->getCanUseWebsiteValue()) {
			$addInheritCheckbox = true;
			$checkboxLabel = Mage::helper('adminhtml')->__('Use Website');
		}
		elseif ($element->getCanUseDefaultValue()) {
			$addInheritCheckbox = true;
			$checkboxLabel = Mage::helper('adminhtml')->__('Use Default');
		}

		if ($addInheritCheckbox) {
			$inherit = $element->getInherit()==1 ? 'checked="checked"' : '';
			if ($inherit) {
				$element->setDisabled(true);
			}
		}
		/**
		 * Обратите внимание, что вызов getTooltip()
		 * отсутствует в устревших версиях Magento CE (в частности, в Magento CE 1.4.0.1),
		 * однако это не является проблемой,
		 * потому что для устаревших версий Magento CE  getTooltip() просто вернёт null.
		 */
		if ($element->getTooltip()) {
			$html .= '<td class="value with-tooltip">';
			$html .= $this->_getElementHtml($element);
			$html .= '<div class="field-tooltip"><div>' . $element->getTooltip() . '</div></div>';
		} else {
			$html .= '<td class="value">';
			$html .= $this->_getElementHtml($element);
		};
		if ($element->getComment()) {
			$html.= '<p class="note"><span>'.$element->getComment().'</span></p>';
		}
		$html.= '</td>';

		if ($addInheritCheckbox) {

			$defText = $element->getDefaultValue();
			if ($options) {
				$defTextArr = array();
				foreach ($options as $k=>$v) {
					if ($isMultiple) {
						if (is_array($v['value']) && in_array($k, $v['value'])) {
							$defTextArr[] = $v['label'];
						}
					}
					// НАЧАЛО ЗАПЛАТКИ
					/**
					 * НАЧАЛО ЗАПЛАТКИ
					 * В Magento CE/EE в этом месте стоит следующий дефектный код:
							elseif ($v['value']==$defText) {
								$defTextArr[] = $v['label'];
								break;
							}
					 *
					 * Этот дефектный код не учитывает, что метод toArray()
					 * класса, заданного как source_model, может вернуть данные в одном из двух форматов:
					 * вот так:
						public function toOptionArray()
						{
							return array(
								array('value' => 0, 'label' => Mage::helper('adminhtml')->__('Random')),
								array('value' => 1,  'label' => Mage::helper('adminhtml')->__('Last Added'))
							);
						}
					 * и так
						public function toOptionArray()
						{
							return array(
								0 => Mage::helper('adminhtml')->__('Random'),
								1 => Mage::helper('adminhtml')->__('Last Added')
							);
						}
					 *
					 * То, что второй формат допустим, видно из реализации метода
					 * @see Varien_Data_Form_Element_Select::getElementHtml()
							foreach ($values as $key => $option) {
								if (!is_array($option)) {
									$html.= $this->_optionToHtml(array(
										'value' => $key,
										'label' => $option),
										$value
									);
								}
								elseif (is_array($option['value'])) {
									$html.='<optgroup label="'.$option['label'].'">'."\n";
									foreach ($option['value'] as $groupItem) {
										$html.= $this->_optionToHtml($groupItem, $value);
									}
									$html.='</optgroup>'."\n";
								}
								else {
									$html.= $this->_optionToHtml($option, $value);
								}
							}
					 * Первая внутри цикла ветка как раз обрабатывает случай,
					 * когда данные от toOptionArray() поступили во втором формате.
					 *
					 * Дефектный программный код ядра учитывает только первый формат,
					 * а получением им данных во втором формате завершается сбоем
					 * «Warning: Illegal string offset 'value'
					 * in app/code/core/Mage/Adminhtml/Block/System/Config/Form/Field.php»
					 * на строке:
					 * elseif ($v['value']==$defText) {
					 *
					 * @link http://magento-forum.ru/topic/4524/
					 *
					 * Обратите внимание, что все source_model в Magento CE
					 * всегда возвращают данные в первом, беспроблемном формате,
					 * однако некоторые source_model Magento EE (проверял на Magento EE 1.13.0.1)
					 * возвращают данные во втором, проблемном формате.
					 * Следующие классы Данные Magento EE возвращают данные в проблемном формате:
					 * @see Enterprise_TargetRule_Model_Source_Position
					 * @see Enterprise_TargetRule_Model_Source_Rotation
					 * @see Enterprise_CatalogPermissions_Model_Adminhtml_System_Config_Source_Grant
					 * @see Enterprise_CatalogPermissions_Model_Adminhtml_System_Config_Source_Grant_Landing
					 *
					 * Так же данные в проблемном формате возвращают некоторые сторонние модули,
					 * в том числе модули некоторых тиражируемых тем.
					 *
					 * По этой причине наша заплатка необходима.
					 */
					else {
						/** @var string $rmValue */
						/** @var string $rmLabel */
						if (is_array($v)) {
							$rmValue = df_a($v, 'value');
							$rmLabel = df_a($v, 'label');
						}
						else {
							$rmValue = $k;
							$rmLabel = $v;
						}
						if ($rmValue == $defText) {
							$defTextArr[] = $rmLabel;
							break;
						}
					}
					// КОНЕЦ ЗАПЛАТКИ
				}
				$defText = join(', ', $defTextArr);
			}

			// default value
			$html.= '<td class="use-default">';
			$html.= '<input id="' . $id . '_inherit" name="'
			. $namePrefix . '[inherit]" type="checkbox" value="1" class="checkbox config-inherit" '
			. $inherit . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" /> ';
			$html.= '<label for="' . $id . '_inherit" class="inherit" title="'
			. htmlspecialchars($defText) . '">' . $checkboxLabel . '</label>';
			$html.= '</td>';
		}

		$html.= '<td class="scope-label">';
		if ($element->getScope()) {
			$html .= $element->getScopeLabel();
		}
		$html.= '</td>';

		$html.= '<td class="">';
		if ($element->getHint()) {
			$html.= '<div class="hint" >';
			$html.= '<div style="display: none;">' . $element->getHint() . '</div>';
			$html.= '</div>';
		}
		$html.= '</td>';

		return $this->_decorateRowHtml($element, $html);
	}

	/**
	 * Метод _decorateRowHtml() отсутствует в устаревших версиях Magento CE
	 * (в частности, в Magento CE 1.4.0.1),
	 * поэтому дублируем его без изменений сюда из последней версии родительского класса
	 * (взял из Magento CE 1.9.0.1).
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @param string $html
	 * @return string
	 */
	protected function _decorateRowHtml($element, $html) {
		return '<tr id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
	}
}