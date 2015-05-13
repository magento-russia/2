<?php
class Df_Licensor_Model_File extends Df_Core_Model_SimpleXml_Parser_Entity {
	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->cfg(self::P__NAME);}

	/** @return Df_Licensor_Model_License */
	public function getLicense() {
		if (!$this->_license) {
			$this->_license =
				Df_Licensor_Model_License::i(
					array(
						Df_Licensor_Model_License::P__DOMAINS => $this->getDomains()
						,Df_Licensor_Model_License::P__FEATURES => $this->getFeatures()
						,Df_Licensor_Model_License::P__SIGNATURE => $this->getSignature()
						,Df_Licensor_Model_License::P__DATE_EXPIRATION => $this->getDateExpiration()
					)
				)
			;
		}
		return $this->_license;
	}
	/** @var Df_Licensor_Model_License */
	private $_license;

	/** @return bool */
	public function isForbidden() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					in_array(Df_Core_Feature::ALL, $this->getFeatures())
				&&
					in_array(Df_Licensor_Model_License::ANY_DOMAIN, $this->getDomains())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return Zend_Date
	 * @throws Exception
	 */
	private function getDateCreation() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df()->date()->createForDefaultTimezone($this->getDocumentParam(self::TAG_CREATED))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return Zend_Date
	 * @throws Exception
	 */
	private function getDateExpiration() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_h()->licensor()->parseDateExpiration(
					$this->getDocumentParam(self::TAG_EXPIRED)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $key
	 * @return string
	 */
	private function getDocumentParam($key) {
		if (!isset($this->{__METHOD__}[$key])) {
			/** @var string $result */
			try {
				$result = $this->descendS('df/license/' . $key, $throw = true);
			}
			catch(Exception $e) {
				$message = 'Файл лицензии на Российскую сборку повреждён.';
				$result = '';
				df_notify_exception($e);
				rm_session()->addError($message);
			}
			$this->{__METHOD__}[$key] = $result;
		}
		return $this->{__METHOD__}[$key];
	}

	/** @return array */
	private function getDomains() {return df_parse_csv($this->getDocumentParam(self::TAG_DOMAINS));}

	/** @return array */
	private function getFeatures() {return df_parse_csv($this->getDocumentParam(self::TAG_FEATURES));}

	/** @return string */
	private function getSignature() {return $this->getDocumentParam(self::TAG_SIGNATURE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__NAME, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__NAME = 'name';
	const TAG_CREATED = 'created';
	const TAG_EXPIRED = 'expired';
	const TAG_DOMAINS = 'domains';
	const TAG_FEATURES = 'features';
	const TAG_SIGNATURE = 'signature';
	/**
	 * @static
	 * @param string $name
	 * @return Df_Licensor_Model_File
	 */
	public static function i($name) {
		return new self(array(
			self::P__NAME => $name, self::P__SIMPLE_XML => file_get_contents($name)
		));
	}
}