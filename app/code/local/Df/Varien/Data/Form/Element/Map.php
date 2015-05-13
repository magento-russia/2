<?php
class Df_Varien_Data_Form_Element_Map extends Df_Varien_Data_Form_Element_Abstract {
	/**
	 * @override
	 * @return string
	 */
	public function getAfterElementHtml() {
		/** @var string $result */
		$result =
			__CLASS__
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * Перед отображением элемента устанавливаем ему тип hidden,
	 * а затем возвращаем тип элемента назад.
	 * 
	 * Обратите внимание, что мы не можем вместо этого установить элементу тип hidden в конструкторе,
	 * потому что иначе шаблон widget/form/renderer/fieldset/element.phtml
	 * не отобразит элемент вовсе.
	 * 
	 * @override
	 * @return string
	 */
	public function getElementHtml() {
		/** @var string|null $type */
		$type = $this->getType();
		$this->setType(self::TYPE__HIDDEN);
		/** @var string $result */
		$result = parent::getElementHtml();
		$this->setType($type);
		df_result_string($result);
		return $result;
	}

	const _CLASS = __CLASS__;
}