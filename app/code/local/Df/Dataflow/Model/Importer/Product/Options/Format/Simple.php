<?php
class Df_Dataflow_Model_Importer_Product_Options_Format_Simple
	extends Df_Dataflow_Model_Importer_Product_Options_Format_Abstract {
	/** @return Df_Dataflow_Model_Importer_Product_Options_Format_Simple */
	public function process() {
		$this->deletePreviousOptionWithSameTitle();
		/** @var $option Df_Catalog_Model_Product_Option */
		$option = Df_Catalog_Model_Product_Option::i();
		$option
			->setProduct($this->getProduct())
			->addOption(array(
				'type' => 'drop_down'
				,'is_require' => 1
				,'title' => $this->getImportedKey()
				,'values' => $this->getValues()
			))
			->saveOptions()
		;
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getImportedKey() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_preg_match($this->getPattern(), parent::getImportedKey());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPattern() {return "#^\s*df_custom_options\s*\[([^\]]+)\]\s*$#u";}

	/** @return Df_Dataflow_Model_Importer_Product_Options_Format_Simple */
	private function deletePreviousOptionWithSameTitle() {
		/** @var Df_Catalog_Model_Product_Option[] $options */
		$options = $this->getProduct()->getOptionsByTitle($this->getImportedKey());
		df_assert_array($options);
		foreach ($options as $option) {
			/** @var Df_Catalog_Model_Product_Option $option */
			$option->deleteWithDependencies();
		}
		return $this;
	}

	/** @return mixed[][] */
	private function getValues() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array();
			$ordering = 0;
			foreach ($this->getValuesTitles() as $title) {
				/** @var string $title */
				$this->{__METHOD__}[]=
					array(
						'title' => $title
						,'price' => 0
						,'price_type' => 'fixed'
						,'sort_order' => $ordering++
					)
				;
			}
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getValuesTitles() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_parse_csv($this->getImportedValue());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Dataflow_Model_Importer_Product_Options_Format_Simple */
	public static function i() {return new self;}
}