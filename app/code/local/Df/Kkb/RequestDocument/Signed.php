<?php
namespace Df\Kkb\RequestDocument;
abstract class Signed extends \Df_Core_Model {
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
	public function getXml() {return dfc($this, function() {return
		'<document>' . $this->getLetter() . $this->getSignature() . '</document>'
	;});}

	/**
	 * В документации о формате суммы платежа ничего не сказано.
	 *
	 * В примере paysystem_PHP/paysys/kkb.utils.php
	 * в комментации к функции @see process_request()
	 * явно написано, что сумма платежа должна быть целым числом (а не дробным).
	 *
	 * Однако практика показала, что платёжный шлюз Казкоммерцбанка
	 * полне допускает дробные размеры платежей.
	 * @return string
	 */
	protected function amount() {return $this->getRequest()->amount()->getAsString();}

	/** @return \Df\Kkb\Config\Area\Service */
	protected function configS() {return $this->getRequest()->configS();}

	/** @return string */
	protected function getCurrencyCode() {return $this->configS()->getCurrencyCodeInServiceFormat();}

	/** @return \Df\Kkb\Request\Payment|\Df\Kkb\Request\Secondary */
	protected function getRequest() {return $this[self::P__REQUEST];}

	/**
	 * @used-by \Df\Kkb\RequestDocument\Registration::getDocumentData_Order()
	 * @used-by \Df\Kkb\RequestDocument\Secondary::getDocumentData_Payment()
	 * @uses \Df\Kkb\Request\Payment::orderIId()
	 * @uses \Df\Kkb\Request\Secondary::orderIId()
	 * @return string
	 */
	protected function orderIId() {
		/** @var string $result */
		$result = $this->getRequest()->orderIId();
		df_result_string_not_empty($result);
		// из документации: «номер заказа должен состоять не менее чем из 6 ЧИСЛОВЫХ знаков»
		df_assert(ctype_digit($result));
		df_assert_ge(6, strlen($result));
		return $result;
	}

	/** @return \Df\Xml\X */
	private function getElementLetter() {return dfc($this, function() {return
		df_xml_node('merchant', $this->getLetterAttributes())->importArray($this->getLetterBody())
	;});}
	
	/** @return string */
	private function getLetter() {return dfc($this, function() {return
		$this->postProcessXml($this->getElementLetter()->asXMLPart())
	;});}
	
	/** @return string */
	private function getSignature() {return dfc($this, function() {return
		$this->postProcessXml($this->getElementSignature()->asXMLPart())
	;});}

	/** @return \Df\Xml\X */
	private function getElementSignature() {return dfc($this, function() {
		/** @var \Df\Xml\X $result */
		$result = df_xml_node('merchant_sign', array('type' => 'RSA'));
		$result->setValue($this->getSigner()->getSignature());
		return $result;
	});}

	/** @return \Df\Kkb\Signer */
	private function getSigner() {return dfc($this, function() {return
		\Df\Kkb\Signer::i($this->getLetter(), $this->configS())
	;});}

	/**
	 * Из документации:
	 * «Используя XML документы из документации,
	 * не забывайте удалять коментарии и знаки переносов,
	 * которые установленны для наглядности картинки!»
	 *
	 * 2016-05-26
	 * Добавил @uses df_trim(), потому что с 2015-07-07
	 * алгоритм @uses \Df\Core\Helper\Text::removeLineBreaks() изменился,
	 * и на концах документа стали образовываться пробелы.
	 * http://magento-forum.ru/topic/5430/
	 *
	 * @param string $xml
	 * @return string
	 */
	private function postProcessXml($xml) {return df_trim(df_t()->removeLineBreaks($xml));}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__REQUEST, \Df\Payment\Request::class);
	}
	const P__REQUEST = 'request';
}