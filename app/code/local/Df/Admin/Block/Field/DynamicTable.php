<?php
/**
 * @singleton
 * КЭШИРОВАНИЕ НАДО РЕАЛИЗОВЫВАТЬ КРАЙНЕ ОСТОРОЖНО!!!
 * Обратите внимание, что Magento не создаёт отдельные экземпляры данного класса
 * для вывода каждого поля!
 * Magento использует ЕДИНСТВЕННЫЙ экземпляр данного класса для вывода всех полей!
 * Поэтому в объектах данного класса нельзя кешировать информацию,
 * которая индивидуальна для поля конкретного поля!
 *
 * Все классы, которые мы указываем в качестве «frontend_model» для интерфейсного поля,
 * в том числе и данный класс, используются как объекты-одиночки.
 * Конструируются «frontend_model» в методе
 * @used-by Mage_Adminhtml_Block_System_Config_Form::initFields():
	if ($element->frontend_model) {
		$fieldRenderer = Mage::getBlockSingleton((string)$element->frontend_model);
	} else {
		$fieldRenderer = $this->_defaultFieldRenderer;
	}
 * Обратите внимание, что для конструирования используется метод @uses Mage::getBlockSingleton()
 * Он-то как раз и обеспечивает одиночество объектов.
 *
 * Рисование полей происходит в методе
 * @see Mage_Adminhtml_Block_System_Config_Form_Field::render()
 * @see Df_Adminhtml_Block_Config_Form_Field::render()
		$html .= '<td class="value">';
		$html .= $this->_getElementHtml($element);
 *
 * Этот метод принимает в качестве параметра элемент, который надо отобразить ($element),
 * и затем вызывает метод @uses Mage_Adminhtml_Block_System_Config_Form_Field::_getElementHtml()
 * Наш класс этот метод перекрывает: @see Df_Admin_Block_Field_DynamicTable::_getElementHtml()
 *
 * @method Varien_Data_Form_Element_Abstract|Df_Varien_Data_Form_Element_Abstract getElement()
 */
abstract class Df_Admin_Block_Field_DynamicTable
	extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {
	/**
	 * 2015-02-06
	 * Этот метод используеся для колонок,
	 * требующих вместо стандартного текстового поля ввода
	 * некий другой элемент управления.
	 * Например, выпадающий список: @see Df_Admin_Config_DynamicTable_Column_Select
	 *
	 * 2015-02-17
	 * Добавлять наши колонки надо именно в массив @uses _columns,
	 * а не в свой собственный, потому что массив @uses _columns прямо используется в шаблоне
	 * @used-by adminhtml/default/default/template/system/config/form/field/array.phtml
	 * Причём, обратите внимание, что ядро Magento не знает,
	 * что у нас $column — не массив, а объект, и ядро Magento
	 * в одном (только одном!) месте указанного выше шаблона применяет к $column
	 * нотацию работы с массивами: $column['label'] (только для ключа «label»).
	 * В нашем случае это работает потому, что базовый класс @see Varien_Object
	 * поддерживает такую нотацию посредством реализации интерфейса @see ArrayAccess:
	 * используется метод @uses Varien_Object::offsetGet(),
	 * который аналогичен методу @see Varien_Object::getData()
	 *
	 * @used-by adminhtml/default/default/template/system/config/form/field/array.phtml
	 * @used-by _renderCellTemplate()
	 * @param Df_Admin_Config_DynamicTable_Column $column
	 * @return void
	 */
	public function addColumnRm(Df_Admin_Config_DynamicTable_Column $column) {
		$this->_columns[$column->getName()] = $column;
	}

	/**
	 * Обёртываем результат родительского метода
	 * @uses Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract::_getElementHtml()
	 * тегом «div» с идентификатором $element->getHtmlId()
	 * Назначение конкретно этому элементу конкретно этого идентификатора
	 * необходимо для корректного скрытия/показа элемента посредством JavaScript
	 * при зависимости его от значений других элементов.
	 * Такая зависимость назначается директивой <depends> в файле etc/system.xml модуля, например:
			<depends><popular__enable>1</popular__enable></depends>
	 * Странно, что родительский метод этого не делает: видимо, в родительском методе дефект.
	 * Используется это идентификатор в методе JavaScript
	 * @see FormElementDependenceController::trackChange():
		if (!$(idTo)) {
			return;
		}
	 *
	 * @used-by Mage_Adminhtml_Block_System_Config_Form_Field::render()
	 * @used-by Df_Adminhtml_Block_Config_Form_Field::render()
			$html .= '<td class="value">';
			$html .= $this->_getElementHtml($element);
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		return rm_tag('div'
			/**
			 * 2015-04-18
			 * Класс CSS используется для задания ширины таблицы и колонок:
			 * @see C:/work/p/2015/01/03/1c/code/skin/adminhtml/rm/default/df/css/source/forms/_grid.scssк).
			 */
			, array('id' => $element->getHtmlId(), 'class' => get_class($this))
			, parent::_getElementHtml($element)
		);
	}

	/**
	 * @override
	 * @used-by adminhtml/default/default/template/system/config/form/field/array.phtml
	 * Обратите внимание, что в строке, которую возвращает данный метод,
	 * одиночные кавычки должны быть экранированы,
	 * потому что данный метод вызывается в виде php-вставки в программный код JavaScript
	 * следующим образом:
		+'<td>'
			+'<?php echo $this->_renderCellTemplate($columnName)?>'
		+'<\/td>'
	 * Как мы видим, результат данного метода тупо обрамляется одинарными кавычками
	 * без их экранирования и затем интерпретируется как строка в программном коде на JavaScript.
	 * @param string $columnName
	 * @return string
	*/
	protected function _renderCellTemplate($columnName) {
		/** @var Df_Admin_Config_DynamicTable_Column|array(string=>mixed)|null $column */
		$column = dfa($this->_columns, $columnName);
		return
			$column instanceof Df_Admin_Config_DynamicTable_Column
			? $column->renderTemplate($this->getElement())
			: parent::_renderCellTemplate($columnName)
		;
	}
}