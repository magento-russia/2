<?php
class Df_PromoGift_Model_Validate_Rule_ApplicableToCurrentQuote
	extends Df_Core_Model_Abstract
	implements Zend_Validate_Interface {
	/**
	 * Returns an array of message codes that explain why a previous isValid() call
	 * returned false.
	 *
	 * If isValid() was never called or if the most recent isValid() call
	 * returned true, then this method returns an empty array.
	 *
	 * This is now the same as calling array_keys() on the return value from getMessages().
	 * @return array
	 * @deprecated Since 1.5.0
	 */
	public function getErrors() {
		return array();
	}

	/** @return array */
	public function getMessages() {
		return array();
	}

	/**
	 * @param Mage_SalesRule_Model_Rule|mixed $value
	 * @return boolean
	 * @throws Zend_Validate_Exception If validation of $value is impossible
	 */
	public function isValid($value) {
		return
				($value instanceof Mage_SalesRule_Model_Rule)
			&&
				$this->isApplicableToQuote($value)
		;
	}

	/** @return Mage_Sales_Model_Quote */
	private function getQuote() {
		return rm_session_checkout()->getQuote();
	}

	/**
	 * Не исчерпал ли покупатель возможность получать подарки по данному правилу
	 *
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return bool
	 */
	private function isTheCustomerAlreadyGotMaxGiftsByThisRuleDuringPrevoiusCheckouts(
		Mage_SalesRule_Model_Rule $rule
	) {
		/** @var bool $result */
		$result = false;
		/** @var int $ruleId */
		$ruleId = $rule->getId();
		if ($ruleId) {
			df_assert_integer($ruleId);
			/** @var int $usesPerCustomer */
			$usesPerCustomer = $rule[Df_SalesRule_Const::DB__SALES_RULE__USES_PER_CUSTOMER];
			if ($usesPerCustomer) {
				df_assert_integer($usesPerCustomer);
				/** @var int $customerId */
				$customerId = $this->getQuote()->getData('customer_id');
				df_assert_integer($customerId);
				/**
				 * Смотрим, сколько раз данный покупатель уже получал
				 * (не просто кладя в корзину, успешно завершая оформление заказа!)
				 * подарки по данному правилу
				 */
				/** @var Mage_SalesRule_Model_Rule_Customer $ruleCustomer */
				$ruleCustomer = df_model('salesrule/rule_customer');
				$ruleCustomer->loadByCustomerRule($customerId, $ruleId);
				if ($ruleCustomer->getId()) {
					if (
							$usesPerCustomer
						<=
							$ruleCustomer[Df_SalesRule_Const::DB__SALES_RULE_CUSTOMER__TIMES_USED]
					) {
						$result = true;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return bool
	 */
	private function isApplicableToQuote(Mage_SalesRule_Model_Rule $rule) {
		/** @var bool $result */
		$result = true;
		/**
		 * Здесь надо отбраковать подарочное правило, * если подарок по этому правилу уже находится в корзине покупателя.
		 *
		 * Такая отбраковка нужна не калькулятору ценовых правил
		 * (он-то сам прекрасно проводит такую отбраковку!), * а нам: чтобы определить, стоит ли показывать наш блок с подарками.
		 *
		 *
		 * Я вижу пока 2 способа отбраковки.
		 * В обоих случаях требуется, чтобы калькулятор работал до данного метода
		 * (а так оно и есть — проверил на практике).
		 *
		 * 		[1]	У объекта Mage_SalesRule_Model_Rule в процессе работы калькулятора
		 * 			ведётся учёт характеристики «times_used».
		 *
		 * 		[2]	Мы можем самостоятельно вести такой учёт, * 			перехватывая сообщение «salesrule_validator_process».
		 *
		 * Второй способ надёжнее — его и применим.
		 *
		 * Обратите внимание, что стандартный калькулятор ценовых правил
		 * работает (видимо) только на странице корзины.
		 *
		 * Поэтому, чтобы управлять видимостью нашего блока на других страницах, * надо сохранять результаты вычислений в сессии.
		 *
		 *
		 * Результаты вычислений будем обнулять
		 * при получении сообщения «sales_quote_collect_totals_before»:
		 * как я посмотрел, это — идеальное сообщение для данного действия.
		 *
		 *
		 * @todo: Обратите внимание, что в некоторых магазинах
		 * покупатель способен класть товары в корзину, не переходя на страницу корзины.
		 *
		 * Видимо, в этом случае калькулятор не сработает, и мы будем не в состоянии
		 * правильно управлять видимостью нашего блока.
		 *
		 */

		/** @var int $ruleUsageCountForCurrentQuote */
		$maxUsagesPerQuote =
			(int)
				(
					$rule
						->getData(
							Df_PromoGift_Const::DB__SALES_RULE__MAX_USAGES_PER_QUOTE
						)
				)
		;
		/** @var int $ruleUsageCountForCurrentQuote */
		$ruleUsageCountForCurrentQuote =
			df_h()->promoGift()->getCustomerRuleCounter()
				->getCounterValue($rule->getId())
		;
		if (
				($ruleUsageCountForCurrentQuote >= $maxUsagesPerQuote)
			||
				$this->isTheCustomerAlreadyGotMaxGiftsByThisRuleDuringPrevoiusCheckouts($rule)
		) {
			$result = false;
		}
		if ($result) {
			/**
			 * Распаковываем содержимое свойств conditions и actions, * которое в БД хранится в запакованном (serialized) виде
			 */
			$rule->afterLoad();
			/** @var Mage_SalesRule_Model_Rule_Condition_Combine $conditions */
			$conditions = $rule->getConditions();
			/** @var Varien_Object $container */
			$container =
				new Varien_Object(
					array(
						'quote' => $this->getQuote()
						/**
						 * Для Mage_SalesRule_Model_Rule_Condition_Product_Found
						 */
						,'all_items' => $this->getQuote()->getAllItems()
					)
				)
			;
			$result =
				$conditions->validate(
					$container
				)
			;
		}
		return $result;
	}

	const _CLASS = __CLASS__;

	/** @return Df_PromoGift_Model_Validate_Rule_ApplicableToCurrentQuote */
	public static function i() {return new self;}
}