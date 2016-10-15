<?php
abstract class Df_Parser_Model_Pq_Page extends Df_Parser_Model_Pq implements Zend_Validate_Interface {  
	/**                           
	 * @override        
	 * @see Zend_Validate_Interface
	 * @return string[]
	 */
	public function getMessages() {return array();}
	
	/** @return Zend_Uri_Http */
	public function getUri() {return $this->cfg(self::P__URI);}

	/** @return bool */
	public function isExist() {return !!$this->getContents();}
	
	/**                           
	 * @override      
	 * @see Zend_Validate_Interface
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {return true;}

	/** @return string */
	protected function getContents() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getBrowser()->getPage($this->getUri()->getUri(), $throwOnError = false)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @overrude
	 * @return phpQueryObject
	 */
	protected function getPq() {
		if (!isset($this->{__METHOD__})) {
			if (!$this->isExist()) {
				$this->getBrowser()->throwPageIsNotExist($this->getUri()->getUri());
			}
			$this->{__METHOD__} = df_pq($this->getContents());
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Parser_Model_Browser */
	private function getBrowser() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Parser_Model_Browser::i(get_class($this), $this);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__URI, 'Zend_Uri_Http');
	}

	const P__URI = 'uri';
}