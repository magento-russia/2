<?php
namespace Df\IPay;
use Mage_Payment_Model_Info as II;
class TransactionState extends \Df_Core_Model {
	/** @return void */
	public function clear() {$this->ii()->unsAdditionalInformation(self::$KEY)->save();}

	/** @return string|null */
	public function get() {return $this->ii()->getAdditionalInformation(self::$KEY);}

	/** @return void */
	public function restore() {
		if ($this->_previousState) {
			$this->update($this->_previousState);
		}
	}

	/**
	 * @param string $newState
	 * @return void
	 */
	public function update($newState) {
		df_param_string($newState, 0);
		$this->_previousState = $this->get();
		// Обратите внимание, что хранить состояние транзации в сессии было бы неправильно:
		// это не защищает при одновременной оплате одного заказа несколькими пользователями
		$this->ii()->setAdditionalInformation(self::$KEY, $newState)->save();
	}
	/** @var string|null */
	private $_previousState = null;

	/** @return II */
	private function ii() {return $this[self::$P__PAYMENT];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PAYMENT, II::class);
	}

	/** @var string */
	private static $P__PAYMENT = 'payment';
	/** @var string */
	private static $KEY = 'df_ipay__transaction_state';
	/**
	 * @static
	 * @param II $paymentInfo
	 * @return self
	 */
	public static function i(II $paymentInfo) {return new self([self::$P__PAYMENT => $paymentInfo]);}
}