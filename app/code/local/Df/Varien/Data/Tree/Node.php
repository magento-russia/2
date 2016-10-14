<?php
/**
 * @method Df_Varien_Data_Tree_Node getParent()
 */
class Df_Varien_Data_Tree_Node extends Varien_Data_Tree_Node {
	/**
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	public function cfg($key, $default = null) {
		/** @var mixed $result */
		$result = $this->getData($key);
		if (is_null($result)) {
			$result = $default;
		}
		return $result;
	}

	/** @return string */
	public function getPathAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getParent()
				? $this->getName()
				: implode('/', array($this->getParent()->getPathAsText(), $this->getName()))
			;
		}
		return $this->{__METHOD__};
	}
}