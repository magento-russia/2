<?php
class Df_Core_Xml_Generator_Document extends Df_Core_Xml_Generator_Element {
	/**
	 * @param float $amountInBaseCurrency
	 * @return float
	 */
	public function convertMoneyToExportCurrency($amountInBaseCurrency) {
		return rm_float(rm_currency_h()->convertFromBase(
			$amountInBaseCurrency, $this->getExportCurrency(), $this->store()
		));
	}

	/**
	 * @override
	 * @return Df_Core_Sxe
	 */
	public function getElement() {
		/** @var Df_Core_Sxe $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : parent::getElement();
	}

	/**
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_CustomerGroup::getResult()
	 * @return Df_Directory_Model_Currency
	 */
	public function getExportCurrency() {return $this->store()->getDefaultCurrency();}

	/**
	 * Метод публичен, потому что его использует метод
	 * @see Df_Core_Xml_Generator_Part::getOperationNameInPrepositionalCase()
	 * @return string
	 */
	public function getOperationNameInPrepositionalCase() {
		/** @var string $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : '';
	}

	/**
	 * @param bool $reformat [optional]
	 * @return string
	 */
	public function getXml($reformat = false) {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->mixin(__FUNCTION__, $reformat);
			if (is_null($result)) {
				/**
				 * Обратите внимание, что метод ядра Magento CE
				 * @see Varien_Simplexml_Element::asNiceXml()
				 * не сохраняет в документе XML блоки CDATA,
				 * а вместо этого заменяет недопустимые для XML символы их допустимыми кодами,
				 * например: & => &amp;
				 *
				 * Также @see Varien_Simplexml_Element::asNiceXml()
				 * не добавляет к документу заголовок XML: его надо добавить вручную.
				 *
				 * 2015-02-27
				 * Обратите внимание, что для конвертации объекта класса @see SimpleXMLElement в строку
				 * надо использовать именно метод @uses SimpleXMLElement::asXML(),
				 * а не @see SimpleXMLElement::__toString() или оператор (string)$this.
				 *
				 * @see SimpleXMLElement::__toString() и (string)$this
				 * возвращают непустую строку только для концевых узлов (листьев дерева XML).
				 * Пример:
					<?xml version='1.0' encoding='utf-8'?>
						<menu>
							<product>
								<cms>
									<class>aaa</class>
									<weight>1</weight>
								</cms>
								<test>
									<class>bbb</class>
									<weight>2</weight>
								</test>
							</product>
						</menu>
				 * Здесь для $e1 = $xml->{'product'}->{'cms'}->{'class'}
				 * мы можем использовать $e1->__toString() и (string)$e1.
				 * http://3v4l.org/rAq3F
				 * Однако для $e2 = $xml->{'product'}->{'cms'}
				 * мы не можем использовать $e2->__toString() и (string)$e2,
				 * потому что узел «cms» не является концевым узлом (листом дерева XML).
				 * http://3v4l.org/Pkj37
				 * Более того, метод @see SimpleXMLElement::__toString()
				 * отсутствует в PHP версий 5.2.17 и ниже:
				 * http://3v4l.org/Wiia2#v500
				 */
				$result =
					$this->needSkipXmlHeader()
					? $this->getElement()->asXMLPart()
					: (
						$reformat || $this->hasEncodingWindows1251()
						? rm_concat_clean("\n",
							$this->getXmlHeader()
							, $reformat
								? $this->getElement()->asNiceXml()
								: $this->getElement()->asXMLPart()
						)
						: $this->getElement()->asXML()
					)
				;
				// Убеждаемся, что asXML вернуло строку, а не false.
				df_assert_string($result);
				/**
				 * символ 0xB (вертикальная табуляция) допустим в UTF-8,
				 * но недопустим в XML
				 * http://stackoverflow.com/a/10095901
				 */
				$result = str_replace("\x0B", "&#x0B;", $result);
				if ($this->hasEncodingWindows1251()) {
					$result = rm_1251_to($result);
				}
				if ($this->needRemoveLineBreaks()) {
					$result = df_t()->removeLineBreaks($result);
				}
				if ($this->needDecodeEntities()) {
					$result = html_entity_decode($result, ENT_NOQUOTES, 'UTF-8');
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function hasEncodingWindows1251() {
		/** @var bool $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : false;
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function log($message) {
		if ($this->needLog()) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$this->getLogger()->log(rm_format($arguments));
		}
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function notify($message) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		$message = rm_format($arguments);
		$this->log($message);
		if (!df_is_it_my_local_pc() && $this->getEmailAddressesToNotify()) {
			Df_Qa_Message_Notification::i(array(
				Df_Qa_Message_Notification::P__NOTIFICATION => $message
				,Df_Qa_Message_Notification::P__NEED_LOG_TO_FILE => false
				,Df_Qa_Message_Notification::P__NEED_NOTIFY_DEVELOPER => false
				,Df_Qa_Message_Notification::P__RECIPIENTS =>
					$this->getEmailAddressesToNotify()
			))->log();
		}
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function preprocessUrl($url) {return $url;}

	/**
	 * Использование извне:
	 * @used-by Df_Core_Xml_Generator_Part::store()
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Category::getExternalId()
	 * Использование наследниками:
	 * @used-by Df_1C_Cml2_Export_Document_Catalog::getКаталог_Наименование()
	 * @used-by Df_1C_Cml2_Export_Document_Catalog::getКлассификатор_Наименование()
	 * @used-by Df_1C_Cml2_Export_Document_Catalog::getПакетПредложений_Наименование()
	 * @used-by Df_Catalog_Model_XmlExport_Catalog::getCategories()
	 * @used-by Df_Catalog_Model_XmlExport_Catalog::getCategoriesAsTree()
	 * Использование внутри:
	 * @used-by getExportCurrency()
	 * @return Df_Core_Model_StoreM
	 */
	public function store() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_StoreM $result */
			$result = rm_state()->getStoreProcessed($needThrow = false);
			$this->{__METHOD__} = $result ? $result : rm_store();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_Core_Sxe
	 */
	protected function createElement() {
		/** @var Df_Core_Sxe $result */
		$result = $this->mixin(__FUNCTION__);
		if (is_null($result)) {
			$result = rm_xml(rm_concat_clean("\n",
				$this->getXmlHeader(), $this->getDocType(), $this->getTag()
			));
			$result->addAttributes($this->getAttributes());
			$result->importArray($this->getContentsAsArray(), $this->needWrapInCDataAll());
		}
		return $result;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getAttributes() {
		/** @var array(string => string) $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : parent::getAttributes();
	}

	/** @return array(string => mixed) */
	protected function getContentsAsArray() {
		/** @var array(string => mixed) $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : $this->cfg(self::P__CONTENTS_AS_ARRAY, array());
	}

	/** @return string */
	protected function getDocType() {
		/** @var string $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : $this->cfg(self::P__DOC_TYPE, '');
	}

	/** @return string[] */
	protected function getEmailAddressesToNotify() {return array();}

	/**
	 * «Df_1C_Cml2_Export_Document_Catalog» => «export.document.catalog»
	 * @return string
	 */
	protected function getLogDocumentName() {
		/** @var string $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : rm_model_id($this, '.');
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getTagName() {
		/** @var string $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : parent::getTagName();
	}

	/** @return bool */
	protected function needDecodeEntities() {
		/** @var bool $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : $this->cfg(self::P__NEED_DECODE_ENTITIES, false);
	}

	/** @return bool */
	protected function needLog() {
		/** @var bool $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : df_is_it_my_local_pc();
	}

	/** @return bool */
	protected function needRemoveLineBreaks() {
		/** @var bool $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : $this->cfg(self::P__NEED_REMOVE_LINE_BREAKS, false);
	}

	/** @return bool */
	protected function needSkipXmlHeader() {
		/** @var bool $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : $this->cfg(self::P__NEED_SKIP_XML_HEADER, false);
	}

	/** @return bool */
	protected function needWrapInCDataAll() {
		/** @var bool $result */
		$result = $this->mixin(__FUNCTION__);
		return !is_null($result) ? $result : $this->cfg(self::P__NEED_WRAP_IN_CDATA_ALL, false);
	}

	/** @return Df_Core_Model_Logger */
	private function createLogger() {
		/** @var string $prefix */
		$prefix = implode('-', array_filter(array(rm_module_id($this, '.'), $this->getLogDocumentName())));
		return Df_Core_Model_Logger::s(rm_file_name(
			Mage::getBaseDir('var') . DS . 'log'
			, strtr('rm-{prefix}-{date}-{time}.log', array('{prefix}' => $prefix))
			, $datePartsSeparator = '.'
		));
	}

	/** @return Df_Core_Model_Logger */
	private function getLogger() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_Logger $result */
			$result = $this->cfg(self::P__LOGGER);
			if (!$result) {
				$result = $this->createLogger();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getTag() {return sprintf('<%s/>', $this->getTagName());}

	/** @return string */
	private function getXmlHeader() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->needSkipXmlHeader()
				? ''
				: str_replace(
					'{encoding}'
					, $this->hasEncodingWindows1251() ? 'windows-1251' : 'utf-8'
					, "<?xml version='1.0' encoding='{encoding}'?>"
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CONTENTS_AS_ARRAY, RM_V_ARRAY, false)
			->_prop(self::P__DOC_TYPE, RM_V_STRING, false)
			->_prop(self::P__LOGGER, 'Df_Core_Model_Logger', false)
			->_prop(self::P__NEED_DECODE_ENTITIES, RM_V_BOOL, false)
			->_prop(self::P__NEED_REMOVE_LINE_BREAKS, RM_V_BOOL, false)
			->_prop(self::P__NEED_SKIP_XML_HEADER, RM_V_BOOL, false)
			->_prop(self::P__NEED_WRAP_IN_CDATA_ALL, RM_V_BOOL, false)
		;
	}
	/** @used-by Df_Core_Xml_Generator_Part::_construct() */
	const _C = __CLASS__;
	const P__CONTENTS_AS_ARRAY = 'contents_as_array';
	const P__DOC_TYPE = 'doc_type';
	const P__LOGGER = 'logger';
	const P__NEED_DECODE_ENTITIES = 'need_decode_entities';
	const P__NEED_REMOVE_LINE_BREAKS = 'need_remove_line_breaks';
	const P__NEED_SKIP_XML_HEADER = 'need_skip_xml_header';
	const P__NEED_WRAP_IN_CDATA_ALL = 'need_wrap_in_cdata_all';
	/**
	 * Добавил подчёркивание к названию этого метода,
	 * чтобы метод не конфликтовал с методом i() дочерних классов.
	 * @param array(string => mixed) $parameters
	 * @return Df_Core_Xml_Generator_Document
	 */
	public static function _i(array $parameters = array()) {return new self($parameters);}
}