<?php
/**
 * Этот класс позволяет добавлять дополнительную информацию в заголовок отчёта о сбое.
 * Стандартный заголовок отчёта о сбое выглядит так:
	URL:                http://localhost.com:831/df-1c/cml2/index/?type=catalog&mode=import&filename=offers___6dbd4e7d-7612-4427-8ebe-68cdb712e8d2.xml&
	Версия Magento:     2.36.8 (1.9.0.1)
	Версия PHP:         5.5.12
	Время:              2014-08-11 18:30:07 MSK
	***********************************
	Тип цен «Розничная», указанный администратором как основной, отсутствует в 1С:Управление торговлей.
	***********************************
 *
 * Вызов
		Df_Qa_Model_Context::s()->addItem('Схема CommerceML', '2.0.8');
 * добавит к заголовку отчёта о сбое версию схемы CommerceML, и заголовок будет выглядеть так:
	URL:                http://localhost.com:831/df-1c/cml2/index/?type=catalog&mode=import&filename=offers___6dbd4e7d-7612-4427-8ebe-68cdb712e8d2.xml&
	Версия Magento:     2.36.8 (1.9.0.1)
	Версия PHP:         5.5.12
	Время:              2014-08-11 18:30:07 MSK
	Схема CommerceML:   2.0.8
	***********************************
	Тип цен «Розничная», указанный администратором как основной, отсутствует в 1С:Управление торговлей.
	***********************************
 *
 * @see Df_1C_Model_Cml2_Action_Catalog_Import::processInternal()
 */
class Df_Qa_Model_Context extends Df_Core_Model_Abstract implements IteratorAggregate {
	/**
	 * @param string $label
	 * @param string $value
	 */
	public function addItem($label, $value) {$this->getItems()->offsetSet($label, $value);}

	/**
	 * @override
	 * @return Traversable
	 */
	public function getIterator() {return $this->getItems();}

	/** @return string */
	public function render() {
		/** @var string[] $rows */
		$rows = array();
		foreach ($this->getItems() as $label => $value) {
			/** @var string $label */
			/** @var string $value */
			$rows []= df_concat(df_text()->pad($label . ':', 21), $value);
		}
		return !$rows ? '' : "\n" . implode("\n", $rows);
	}

	/** @return ArrayObject */
	private function getItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new ArrayObject(array());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Qa_Model_Context */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}