<?php
class Df_Varien_Simplexml_Config extends Varien_Simplexml_Config {
	/** @return string */
	public function getModuleName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			/** @var \Df\Xml\X $xmlModules */
			$xmlModules = $this->getNode()->{'modules'};
			if ($xmlModules) {
				foreach ($xmlModules->children() as $moduleName => $child) {
					$result = df_nts($moduleName);
					break;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	/** @var string */
	protected $_elementClass = \Df\Xml\X::class;
	
}