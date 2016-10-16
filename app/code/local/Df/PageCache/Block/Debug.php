<?php
/**
 * @method $this setDynamicBlockContent(string $value)
 * @method $this setTags(string[] $value)
 * @method $this setType(string $value)
 */
class Df_PageCache_Block_Debug extends Mage_Core_Block_Template {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::__construct()
	 */
    public function __construct() {$this->setTemplate('df/pagecache/blockdebug.phtml');}
}
