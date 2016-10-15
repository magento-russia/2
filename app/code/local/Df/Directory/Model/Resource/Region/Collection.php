<?php
class Df_Directory_Model_Resource_Region_Collection 
	extends Mage_Directory_Model_Resource_Region_Collection {
	/**
	 * @param string $name
	 * @return int
	 */
	public function getIdByName($name) {
		/** @var Df_Directory_Model_Region|null $region */
		$region = $this->getItemByName($name);
		return is_null($region) ? null : df_nat0($region->getId());
	}

	/**
	 * @param string $name
	 * @return Df_Directory_Model_Region|null
	 */
	public function getItemByName($name) {
		return dfa($this->getMapFromNameToItem(), mb_strtoupper($name));
	}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Region
	 */
	public function getResource() {return Df_Directory_Model_Resource_Region::s();}

	/** @return array(string => Df_Directory_Model_Region) */
	private function getMapFromNameToItem() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				/** @uses Df_Directory_Model_Region::getNameOriginal() */
				array_combine(df_strtoupper($this->walk('getNameOriginal')), $this->getItems())
			;
		}
		return $this->{__METHOD__};
	}
}