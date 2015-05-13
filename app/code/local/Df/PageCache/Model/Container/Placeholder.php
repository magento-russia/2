<?php
class Df_PageCache_Model_Container_Placeholder
{
	const HTML_NAME_PATTERN = '/<!--\{(.*?)\}-->/i';

	/**
	 * Associative array of definition hash to informative definition
	 *
	 * @var array
	 */
	protected static $_definitionMap = array();

	/**
	 * Original placeholder definition based on HTML_NAME_PATTERN
	 * @var string
	 */
	protected $_definition;

	/**
	 * Placeholder name (first word from definition before " ")
	 * @var string
	 */
	protected $_name;

	/**
	 * Placeholder attributes
	 * @var $_attributes array
	 */
	protected $_attributes = array();

	/**
	 * Class constructor.
	 * Initialize placeholder name and attributes based on definition
	 *
	 * @param string $definition
	 */
	public function __construct($definition)
	{
		if ($definition && array_key_exists($definition, self::$_definitionMap)) {
			$definition = self::$_definitionMap[$definition];
		}
		$this->_definition = $definition;
		$definition     = explode(' ', $definition);
		$this->_name    = $definition[0];
		$count = count($definition);
		if ($count>1) {
			for ($i=1; $i<$count; $i++) {
				$info = explode('=', $definition[$i]);
				$this->_attributes[$info[0]] = isset($info[1]) ? trim($info[1], '"\'') : null;
			}
		}
	}

	/**
	 * Get placeholder name
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Get placeholder definition
	 * @return string
	 */
	public function getDefinition()
	{
		return $this->_definition;
	}

	/**
	 * Get attribute by specific code
	 * @param $code string
	 * @return string
	 */
	public function getAttribute($code)
	{
		return isset($this->_attributes[$code]) ? $this->_attributes[$code] : null;
	}

	/**
	 * Set attribute by specific code
	 * @param $code string
	 * @param $value
	 * @return string
	 */
	public function setAttribute($code, $value)
	{
		$this->_attributes[$code] = $value;
	}

	/**
	 * Get regular expression pattern to replace placeholder content
	 * @return string
	 */
	public function getPattern()
	{
		return '/' . preg_quote($this->getStartTag(), '/') . '(.*?)' . preg_quote($this->getEndTag(), '/') . '/ims';
	}

	/**
	 * Get placeholder content replacer
	 *
	 * @return string
	 */
	public function getReplacer()
	{
		$def = $this->_definition;
		$container = $this->getAttribute('container');
		$containerClass = 'container="'.$this->getContainerClass().'"';
		$def = str_replace('container="'.$container.'"', $containerClass, $def);
		$def = str_replace('container=\''.$container.'\'', $containerClass, $def);
		return '<!--{' . $def . '}-->';
	}

	/**
	 * Get class name of container related with placeholder
	 *
	 * @return string
	 */
	public function getContainerClass()
	{
		$class = $this->getAttribute('container');
		if (strpos($class, '/') !== false) {
			return Mage::getConfig()->getModelClassName($class);
		}
		return $class;
	}

	/**
	 * Retrieve placeholder definition hash
	 *
	 * @return string
	 */
	protected function _getDefinitionHash()
	{
		$definition = $this->getDefinition();
		$result = array_search($definition, self::$_definitionMap);
		if ($result === false) {
			$result = $this->getName() . '_' . md5($definition);
			self::$_definitionMap[$result] = $definition;
		}
		return $result;
	}

	/**
	 * Get placeholder start tag for block html generation
	 *
	 * @return string
	 */
	public function getStartTag()
	{
		return '<!--{' . $this->_getDefinitionHash() . '}-->';
	}

	/**
	 * Get placeholder end tag for block html generation
	 *
	 * @return string
	 */
	public function getEndTag()
	{
		return '<!--/{' . $this->_getDefinitionHash() . '}-->';
	}
}
