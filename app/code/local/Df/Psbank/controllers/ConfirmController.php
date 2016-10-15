<?php
class Df_Psbank_ConfirmController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная система присылает сюда подтверждения факта выполнения
	 * всех запрашиваемых интернет-магазином у платёжной системы операций
	 * @return void
	 */
	public function indexAction() {df_action($this, $this->getActionClass());}

	/** @return string */
	private function getActionClass() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dfa($this->getActionMap(), $this->getActionCode());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getActionCode() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|int $result */
			$result = $this->getRequest()->getParam('TRTYPE');
			if (is_null($result)) {
				df_error(
					'При обращении по данному веб-адресу надо указывать код операции параметром «TRTYPE»,'
					.' однако этот код в Вашем обращении отсутствует.'
				);
			}
			if (!ctype_digit($result)) {
				df_error('Код операции («TRTYPE») должен содержать только цифры');
			}
			$result = df_nat0($result);
			if (!in_array($result, array_keys($this->getActionMap()))) {
				df_error('Системе непонятен код запрашиваемой операции: «%d»', $this->getActionCode());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(int => string) */
	private function getActionMap() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				0 => Df_Psbank_Model_Action_Confirm_Authorize::class
				,1 => Df_Psbank_Model_Action_Confirm_Payment::class
				,21 => Df_Psbank_Model_Action_Confirm_Capture::class
				,22 => Df_Psbank_Model_Action_Confirm_Void::class
			);
		}
		return $this->{__METHOD__};
	}
}