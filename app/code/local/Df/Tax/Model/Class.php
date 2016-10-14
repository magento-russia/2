<?php
class Df_Tax_Model_Class extends Mage_Tax_Model_Class {
	/**
	 * Добавляем возможность указывать для налоговой ставки страну,
	 * чтобы в дальнейшем, при использовании налоговых ставок в выпадающих списках,
	 * (например, при назначении налоговой ставки товару)
	 * не показывать администраторам интернет-магазинам одной страны налоговые ставки других стран).
	 * @used-by Df_Tax_Setup_3_0_0::_process()
	 * @used-by Df_Tax_Setup_3_0_0::addClasses()
	 * @used-by Df_Adminhtml_Block_Tax_Class_Edit_Form::_prepareForm()
	 * @used-by Df_Tax_Model_Resource_Class_Collection::filterByShopCountry()
	 */
	const P__ISO2 = 'rm_iso2';
}