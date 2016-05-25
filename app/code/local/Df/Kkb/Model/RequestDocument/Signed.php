<?php
abstract class Df_Kkb_Model_RequestDocument_Signed extends Df_Core_Model_Abstract {
	/**                               
	 * @abstract
	 * @return array(string => string)
	 */
	abstract protected function getLetterAttributes();

	/**
	 * @abstract
	 * @return array(string => string)
	 */
	abstract protected function getLetterBody();
	
	/** @return string */
	public function getXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				'<document>' . $this->getLetter() . $this->getSignature() . '</document>'
			;
		}
		return $this->{__METHOD__};
	}
	/** @return string */
	protected function getAmount() {
		/**
		 * В документации о формате суммы платежа ничего не сказано.
		 *
		 * В примере paysystem_PHP/paysys/kkb.utils.php
		 * в комментации к функции @see process_request()
		 * явно написано, что сумма платежа должна быть целым числом (а не дробным).
		 *
		 * Однако практика показала, что платёжный шлюз Казкоммерцбанка
		 * полне допускает дробные размеры платежей.
		 */
		return $this->getRequest()->getAmount()->getAsString();
	}

	/** @return string */
	protected function getCurrencyCode() {
		return $this->getServiceConfig()->getCurrencyCodeInServiceFormat();
	}

	/** @return string */
	protected function getOrderId() {
		/** @var string $result */
		$result = $this->getRequest()->getOrder()->getIncrementId();
		df_result_string_not_empty($result);
		// из документации: «номер заказа должен состоять не менее чем из 6 ЧИСЛОВЫХ знаков»
		df_assert(ctype_digit($result));
		df_assert_ge(6, strlen($result));
		return $result;
	}

	/** @return Df_Payment_Model_Request */
	protected function getRequest() {return $this->cfg(self::P__REQUEST);}

	/** @return Df_Kkb_Model_Config_Area_Service */
	protected function getServiceConfig() {return $this->getRequest()->getServiceConfig();}

	/** @return Df_Varien_Simplexml_Element */
	private function getElementLetter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Varien_Simplexml_Element::createNode('merchant', $this->getLetterAttributes())
					->importArray($this->getLetterBody())
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function getLetter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->postProcessXml($this->getElementLetter()->asXMLPart());
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function getSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->postProcessXml($this->getElementSignature()->asXMLPart());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Varien_Simplexml_Element */
	private function getElementSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Varien_Simplexml_Element::createNode('merchant_sign', array('type' => 'RSA'))
			;
			$this->{__METHOD__}->setValue($this->getSigner()->getSignature());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Kkb_Model_Signer */
	private function getSigner() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Kkb_Model_Signer::i($this->getLetter(), $this->getServiceConfig());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Из документации:
	 * «Используя XML документы из документации,
	 * не забывайте удалять коментарии и знаки переносов,
	 * которые установленны для наглядности картинки!»
	 *
	 * 2016-05-26
	 * Добавил @uses df_trim(), потому что с 2015-07-07
	 * алгоритм @uses Df_Core_Helper_Text::removeLineBreaks() изменился,
	 * и на концах документа стали образовываться пробелы.
	 * http://magento-forum.ru/topic/5430/
	 *
	 * @param string $xml
	 * @return string
	 */
	private function postProcessXml($xml) {return df_trim(df_text()->removeLineBreaks($xml));}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__REQUEST, Df_Payment_Model_Request::_CLASS);
	}
	const P__REQUEST = 'request';
}