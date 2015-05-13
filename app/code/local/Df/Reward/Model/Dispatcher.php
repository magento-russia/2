<?php
class Df_Reward_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_block_abstract_to_html_after(Varien_Event_Observer $observer) {
		try {
			/** @var Mage_Core_Block_Abstract $block */
			$block = $observer->getData('block');
			if ('checkout.payment.methods' === $block->getNameInLayout()) {
				/** @var Varien_Object $transport */
				$transport = $observer->getData('transport');
				df_assert($transport instanceof Varien_Object);
				/** @var string $html */
				$html = $transport->getData('html');
				$html =
					df_concat(
						$this->getBlockReward()->toHtml()
						,$html
						,$this->getBlockRewardScripts()->toHtml()
					)					
				;
				$transport->setData('html', $html);
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * Модуль при получении сообщения «sales_quote_collect_totals_before»
	 * обнуляет свои счётчики
	 * применимых к корзине правил с накопительными скидками
	 *
	 * @param Varien_Event_Observer $observer
	 * @return void
	 *
	 */
	public function sales_quote_collect_totals_before(Varien_Event_Observer $observer) {
		try {
			if (df_h()->reward()->isEnabledOnFront()) {
				/**
				 * Обнуляем коллекцию применимых к корзине правил с накопительными скидками
				 */
				df_h()->reward()->getSalesRuleApplications()->clear();
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * Подсчитываем применимые к корзине правила с накопительными скидками
	 *
	 * @param Varien_Event_Observer $observer
	 * @return void
	 *
	 */
	public function salesrule_validator_process(Varien_Event_Observer $observer) {
		try {
			if (df_h()->reward()->isEnabledOnFront()) {
				/**
				 * Чтобы коллекция не ругалась на элемент без идентификатора
				 */
				$observer->setData('id', rm_uniqid());
				df_h()->reward()->getSalesRuleApplications()
					->addItem(
						$observer
					)
				;
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/** @return Df_Reward_Block_Checkout_Payment_Additional */
	private function getBlockReward() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Reward_Block_Checkout_Payment_Additional::i('reward.points');
			$this->{__METHOD__}->setTemplate('df/reward/checkout/onepage/payment/additional.phtml');
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Reward_Block_Checkout_Payment_Additional */
	private function getBlockRewardScripts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Reward_Block_Checkout_Payment_Additional::i('reward.scripts');
			$this->{__METHOD__}->setTemplate('df/reward/checkout/onepage/payment/scripts.phtml');
		}
		return $this->{__METHOD__};
	}
}