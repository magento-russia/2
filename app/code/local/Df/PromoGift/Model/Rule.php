<?php
class Df_PromoGift_Model_Rule extends Df_SalesRule_Model_Rule {
	/** @return int */
	public function getMaxUsagesPerQuote() {
		return (int)$this->_getData(self::P__MAX_USAGES_PER_QUOTE);
	}
	/**
	 * @override
	 * @return Df_PromoGift_Model_Resource_Rule_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/** @return bool */
	public function isApplicableToQuote() {
		/** @var bool $result */
		$result = true;
		/**
		 * Здесь надо отбраковать подарочное правило,
		 * если подарок по этому правилу уже находится в корзине покупателя.
		 *
		 * Такая отбраковка нужна не калькулятору ценовых правил
		 * (он-то сам прекрасно проводит такую отбраковку!),
		 * а нам: чтобы определить, стоит ли показывать наш блок с подарками.
		 *
		 *
		 * Я вижу пока 2 способа отбраковки.
		 * В обоих случаях требуется, чтобы калькулятор работал до данного метода
		 * (а так оно и есть — проверил на практике).
		 *
		 * 		[1]	У объекта Mage_SalesRule_Model_Rule в процессе работы калькулятора
		 * 			ведётся учёт характеристики «times_used».
		 *
		 * 		[2]	Мы можем самостоятельно вести такой учёт,
		 * 			перехватывая сообщение «salesrule_validator_process».
		 *
		 * Второй способ надёжнее — его и применим.
		 *
		 * Обратите внимание, что стандартный калькулятор ценовых правил
		 * работает (видимо) только на странице корзины.
		 *
		 * Поэтому, чтобы управлять видимостью нашего блока на других страницах,
		 * надо сохранять результаты вычислений в сессии.
		 *
		 *
		 * Результаты вычислений будем обнулять
		 * при получении сообщения «sales_quote_collect_totals_before»:
		 * как я посмотрел, это — идеальное сообщение для данного действия.
		 *
		 * Обратите внимание, что в некоторых магазинах
		 * покупатель способен класть товары в корзину, не переходя на страницу корзины.
		 *
		 * Видимо, в этом случае калькулятор не сработает, и мы будем не в состоянии
		 * правильно управлять видимостью нашего блока.
		 *
		 */
		/** @var int $ruleUsageCountForCurrentQuote */
		$ruleUsageCountForCurrentQuote =
			df_h()->promoGift()->getCustomerRuleCounter()->getCounterValue($this->getId())
		;
		if (
				($ruleUsageCountForCurrentQuote >= $this->getMaxUsagesPerQuote())
			||
				$this->isTheCustomerAlreadyGotMaxGiftsByThisRuleDuringPrevoiusCheckouts()
		) {
			$result = false;
		}
		if ($result) {
			/**
			 * Распаковываем содержимое свойств conditions и actions,
			 * которое в БД хранится в запакованном (serialized) виде
			 */
			$this->afterLoad();
			/** @var Mage_SalesRule_Model_Rule_Condition_Combine $conditions */
			$conditions = $this->getConditions();
			/** @var Varien_Object $container */
			$container = new Varien_Object(array(
				'quote' => df_quote()
				/** для @see Mage_SalesRule_Model_Rule_Condition_Product_Found */
				,'all_items' => df_quote()->getAllItems()
			));
			$result = $conditions->validate($container);
		}
		return $result;
	}

	/**
	 * Не исчерпал ли покупатель возможность получать подарки по данному правилу
	 * @return bool
	 */
	private function isTheCustomerAlreadyGotMaxGiftsByThisRuleDuringPrevoiusCheckouts() {
		/** @var bool $result */
		$result = false;
		/** @var int $ruleId */
		$ruleId = $this->getId();
		if ($ruleId) {
			df_assert_integer($ruleId);
			/** @var int $usesPerCustomer */
			$usesPerCustomer = $this->getUsesPerCustomer();
			if ($usesPerCustomer) {
				df_assert_integer($usesPerCustomer);
				/** @var int $customerId */
				$customerId = df_quote()->getCustomerId();
				df_assert_integer($customerId);
				// Смотрим, сколько раз данный покупатель уже получал
				// (не просто кладя в корзину, успешно завершая оформление заказа!)
				// подарки по данному правилу.
				/** @var Mage_SalesRule_Model_Rule_Customer $ruleCustomer */
				$ruleCustomer = df_model('salesrule/rule_customer');
				$ruleCustomer->loadByCustomerRule($customerId, $ruleId);
				if ($ruleCustomer->getId() && ($usesPerCustomer <= $ruleCustomer->getTimesUsed())) {
					$result = true;
				}
			}
		}
		return $result;
	}

	/**
	 * @used-by Df_PromoGift_Model_PromoAction::_construct()
	 * @used-by Df_PromoGift_Model_Resource_Rule_Collection::_construct()
	 */

	const P__MAX_USAGES_PER_QUOTE = 'df_max_usages_per_quote';
	/** @return Df_PromoGift_Model_Resource_Rule_Collection */
	public static function c() {return new Df_PromoGift_Model_Resource_Rule_Collection;}
}