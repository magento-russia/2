<?php
class Df_Adminhtml_Block_Tax_Rate_ImportExport extends Mage_Adminhtml_Block_Tax_Rate_ImportExport {
	// здесь не надо перекрывать метод __

	/**
	 * Цель перекрытия —
	 * перевести надписи на кнопках экспорта и импорта налоговых ставок
	 * на экране «Продажи» → «НДС» → «Импорт и экспорт ставок».
	 * @override
	 * @param string $label
	 * @param string $onclick
	 * @param string $class
	 * @param string $id
	 * @return string
	 */
	public function getButtonHtml($label, $onclick, $class = '', $id = null) {
		return parent::getButtonHtml($this->__($label), $onclick, $class, $id);
	}
}