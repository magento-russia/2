<?php
class Df_Zf_Form extends Zend_Form {
	/**
	 * @param array $data
	 * @return Df_Zf_Form
	 */
	public function setValues(array $data) {
		/**
		 * Обратите внимание, что isValid не только проверяет допустимость данных, * но и устанавливает эти данные в форму
		 */
		if (!$this->isValid($data)) {
			$this->throwValidationException();
		}
		return $this;
	}

	/**
	 * @throws Df_Core_Exception_InvalidUserInput
	 * @return void
	 */
	protected function throwValidationException() {
		/** @var Df_Core_Exception_InvalidUserInput $result */
		$exception = new Df_Core_Exception_InvalidUserInput();
		foreach ($this->getMessages() as $elementName => $messages) {
			/** @var string $elementName */
			/** @var array $messages */
			if ($messages) {
				/** @var Zend_Form_Element|null $element */
				$element = $this->getElement($elementName);
				/** @var string|null $message */
				$message = $element->getAttrib(self::FORM_ELEMENT_ATTRIB__MESSAGE_FOR_INVALID_VALUE_CASE);
				if (is_null($message)) {
					/** @var string $label */
					$label = $this->getElement($elementName)->getLabel();
					if (!df_check_string($label)) {
						$label = $elementName;
					}
					$message = rm_sprintf('Вы указали недопустимое значение для поля «%s».', $label);
					foreach ($messages as $concreteMessage) {
						/** @var string $concreteMessage */
						$message = implode('<br/>', array($message, $concreteMessage));
					}
				}
				/** @var Df_Core_Model_Message_InvalidUserInput $invalidUserInputMessage */
				$invalidUserInputMessage = new Df_Core_Model_Message_InvalidUserInput($message);
				$invalidUserInputMessage->setElement($element);
				$exception->addMessage($invalidUserInputMessage);
			}
		}
		throw $exception;
	}

	const FORM_ELEMENT_ATTRIB__MESSAGE_FOR_INVALID_VALUE_CASE = 'messageForInvalidValueCase';
}