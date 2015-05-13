<?php
class Df_Core_Block_Text_List extends Mage_Core_Block_Text_List {
	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		$this->setText('');
		foreach ($this->getSortedChildren() as $name) {
			$block = $this->getLayout()->getBlock($name);
			/**
			 * 2013-12-11
			 * Странно, как я за 3 года развития Российской сборки Magento не додумался до этого раньше!
			 * Разумеется, если блок был удалён модулем «Удобная настройка витрины»,
			 * то нет никакой надобности возбуждать исключительную ситуацию
			 * «Invalid block» / «Недействительный тип блока» / «Система не нашла блок»,
			 * как это делает родительский класс @see Mage_Core_Block_Text_List.
			 */
			if ($block) {
				$this->addText($block->toHtml());
			}
		}
		return
			!$this->_beforeToHtml()
			? ''
			: $this->getText()
		;
	}
}