<?php
abstract class Df_Varien_Data_Form_Processor extends Df_Core_Model {
	/**
	 * @abstract
	 * @return Df_Varien_Data_Form_Processor
	 */
	abstract public function process();

	/**
	 * @param Df_Varien_Data_Form $form
	 * @return Df_Varien_Data_Form_Processor
	 */
	public function setForm(Df_Varien_Data_Form $form) {
		$this->setData(self::P__FORM, $form);
		return $this;
	}

	/** @return Df_Varien_Data_Form */
	protected function getForm() {return $this->cfg(self::P__FORM);}

	
	const P__FORM = 'form';
}