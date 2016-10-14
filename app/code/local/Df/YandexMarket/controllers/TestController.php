<?php
class Df_YandexMarket_TestController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		try {
			/** @var string $pattern */
			$pattern = 'La scala ODB волокно бамбука 100% Евро %s';
			/** @var string $result */
			$result = rm_sprintf($pattern, '220х140');
			$this
				->getResponse()
				->setHeader('Content-Type', 'text/plain; charset=UTF-8')
				->setBody($result)
			;
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
	}

	/** @return void */
	public function index2Action() {
		try {
			/** @var phpQueryObject $pq */
			$pq = df_pq(file_get_contents('http://www.avislogistics.kz/rus/calculator/'));
			/** @var phpQueryObject $pqOptions */
			$pqOptions = df_pq('#country1 option', $pq);
			/** @var array(string => string) $options */
			$options = array();
			foreach ($pqOptions as $domOption) {
				/** @var DOMNode $domOption */
				/** @var string $label */
				$label = $domOption->textContent;
				if ('' !== $label) {
					/** @var string|null $value */
					$value = null;
					if (!is_null($domOption->attributes)) {
						/** @var DOMNode|null $domValue */
						$domValue = $domOption->attributes->getNamedItem('value');
						if (!is_null($domValue)) {
							$value = $domValue->nodeValue;
						}
					}
					$options[$label] = $value;
				}
			}
			$this->getResponse()
				->setHeader('Content-Type', 'text/plain; charset=UTF-8')
				->setBody(rm_print_params($options))
			;
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
	}

	/** @return void */
	public function index3Action() {
		/** @var Df_Core_Xml_Generator_Document $document */
		$document = Df_Core_Xml_Generator_Document::_i(array(
			Df_Core_Xml_Generator_Document::P__CONTENTS_AS_ARRAY => array(
				'a' => 'превед'
				,'b' => rm_cdata('медвед')
			)
			,Df_Core_Xml_Generator_Document::P__TAG_NAME => 'тест'
		));
		try {
			rm_report('test-{time}.xml', $document->getXml());
			$this->getResponse()
				->setHeader('Content-Type', 'text/xml')
				->setBody($document->getXml())
			;
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
	}

	/** @return void */
	public function index4Action() {
		try {
			$this
				->getResponse()
				->setHeader('Content-Type', 'text/plain; charset=UTF-8')
				->setBody(print_r(gettype(round(10.5)), true))
			;
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
	}
}