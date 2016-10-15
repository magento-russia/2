<?php
/** @method Df_Adminhtml_Model_Event_Block_SalesRule_Actions_PrepareForm getEvent() */
class Df_PromoGift_Model_Handler_Adminhtml_Block_Actions_PrepareForm_AddInputMaxUsagesPerQuote
	extends Df_Core_Model_Handler {
	/** @return void */
	public function handle() {
		// добавляем поле
		$this->getEvent()->getActionsFieldset()->addField(
			Df_PromoGift_Model_Rule::P__MAX_USAGES_PER_QUOTE
			,'text'
			,array(
				'name' => Df_PromoGift_Model_Rule::P__MAX_USAGES_PER_QUOTE
				,'label' =>
					'Сколько раз можно применять правило для одного и того же заказа'
					. ' (опция предназначена только для модуля «Промо-подарки»)'
			)
			// поле, после которого система разместит наше поле
			,'discount_qty'
		);
	}

	/**
	 * Класс события (для валидации события)
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Adminhtml_Model_Event_Block_SalesRule_Actions_PrepareForm::class;
	}

	/** @used-by Df_PromoGift_Observer::adminhtml_block_salesrule_actions_prepareform() */

}