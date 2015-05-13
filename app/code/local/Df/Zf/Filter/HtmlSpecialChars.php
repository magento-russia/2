<?php
class Df_Zf_Filter_HtmlSpecialChars extends Zend_Filter_HtmlEntities {
	/**
	 * @param string $value
	 * @return string
	 */
	public function filter($value)
	{
		return htmlspecialchars ((string) $value, $this->getQuoteStyle(), $this->getCharSet(), $this->getDoubleQuote());
	}

	/**
	 * @param array $options
	 */
	public function __construct($options = array())
	{
		// for compatibility with Magento 1.4.0.1
		parent
			::__construct(
				array_merge(
					$options
					,array(
						"charset" => "UTF-8"
					)
				)
			)
		;
	}
}