<?php
/**
 * Reward action for converting spent money to points
 */
class Df_Reward_Model_Action_OrderExtra extends Df_Reward_Model_Action_Abstract {
	/**
	 * Quote instance, required for estimating checkout reward (order subtotal - discount)
	 *
	 * @var Mage_Sales_Model_Quote
	 */
	protected $_quote = null;

	/**
	 * Return action message for history log
	 *
	 * @param array $args Additional history data
	 * @return string
	 */
	public function getHistoryMessage($args = array())
	{
		$incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';
		return df_h()->reward()->__('Earned points for order #%s.', $incrementId);
	}

	/**
	 * Setter for $_entity and add some extra data to history
	 *
	 * @param Varien_Object $entity
	 * @return Df_Reward_Model_Action_Abstract
	 */
	public function setEntity($entity)
	{
		parent::setEntity($entity);
		$this->getHistory()->addAdditionalData(array(
			'increment_id' => $this->getEntity()->getIncrementId()
		));
		return $this;
	}

	/**
	 * Quote setter
	 *
	 * @param Mage_Sales_Model_Quote $quote
	 * @return Df_Reward_Model_Action_OrderExtra
	 */
	public function setQuote(Mage_Sales_Model_Quote $quote)
	{
		$this->_quote = $quote;
		return $this;
	}

	/**
	 * Retrieve points delta for action
	 *
	 * @param int $websiteId
	 * @return int
	 */
	public function getPoints($websiteId)
	{
		if ($this->_quote) {
			$quote = $this->_quote;
			// known issue: no support for multishipping quote
			$address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
			$monetaryAmount = $quote->getBaseSubtotal() - abs(1 * $address->getBaseDiscountAmount());
		} else {
			$monetaryAmount = $this->getEntity()->getBaseTotalPaid() - $this->getEntity()->getBaseShippingAmount() - $this->getEntity()->getBaseTaxAmount();
		}
		$pointsDelta = $this->getReward()->getRateToPoints()->calculateToPoints((float)$monetaryAmount);
		/**
		 * Вот здесь, если мы находимся на странице корзины, нелпохо бы ещё учесть ценовые правила
		 */
		if ($this->_quote) {
			/**
			 * Видимо, наличие $this->_quote указывает, что мы находимся на странице корзины.
			 * Смотрим, какие ценовые правила применимы к корзине.
			 */
			/** @var array $ruleIds */
			$ruleIds = array();
			foreach (df_h()->reward()->getSalesRuleApplications() as $salesRuleApplication) {
				/** @var Varien_Object $salesRuleApplication */
				/** @var Mage_SalesRule_Model_Rule $rule */
				$rule = $salesRuleApplication->getData('rule');
				df_assert($rule instanceof Mage_SalesRule_Model_Rule);
				$ruleIds[]= $rule->getId();
			}
			$ruleIds = rm_array_unique_fast($ruleIds);
			/** @var array $rewardRules */
			$rewardRules = Df_Reward_Model_Resource_Reward::s()->getRewardSalesrule($ruleIds);
			/** @var array $rulesPoints */
			$rulesPoints= array();
			foreach ($rewardRules as $rewardRule) {
				/** @var array|object $rewardRule */
				/** @var int $ruleId */
				$ruleId = rm_nat(df_a($rewardRule, 'rule_id'));
				$rulesPoints[$ruleId] = df_a($rewardRule, 'points_delta', 0);
			}
			foreach (df_h()->reward()->getSalesRuleApplications() as $salesRuleApplication) {
				/** @var Varien_Object $salesRuleApplication */
				/** @var Mage_SalesRule_Model_Rule $rule */
				$rule = $salesRuleApplication->getData('rule');
				df_assert($rule instanceof Mage_SalesRule_Model_Rule);
				/** @var int $qty */
				$qty = rm_nat0($salesRuleApplication->getData('qty'));
				/** @var int $maxQty */
				$maxQty = rm_nat0($rule->getDiscountQty());
				if (0 < $maxQty) {
					/** @var int $usedQty */
					$usedQty = rm_nat0($rule->getData('used_qty'));
					$qty = min ($qty, $maxQty - $usedQty);
					/**
					 * Обратите внимание, что, в отличие от других типов правил,
					 * для накопительного правила мы трактуем параметр
					 * «Наибольшее количество товарных единиц, к которым применяется скидка»
					 * с учётом ВСЕХ товаров в корзине, а не толькоединиц конкретного товара.
					 */
					$rule->setData('used_qty', $qty);
				}
				$pointsDelta += $qty * df_a($rulesPoints, $rule->getId());
				$ruleIds[]= $rule->getId();
			}
		}
		return $pointsDelta;
	}
}