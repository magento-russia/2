<?php
class Df_Localization_Onetime_Dictionary_Filesystem_Operation
	extends \Df\Xml\Parser\Entity {
	/** @return string|null */
	public function getDestinationFull() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set($this->getPathFull($this->getDestinationRelative()));
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getDestinationDir() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dirname($this->getDestinationFull());
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getSourceFull() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
			if (!$this->isSourceUrl()) {
				$result = $this->getPathFull($this->getSourceRelative());
			}
			else {
				/** @var string|bool $contents */
				$contents = @file_get_contents($this->getSourceRelative());
				if ($contents) {
					$fileName = Mage::getBaseDir('media') . DS . 'downloaded';
					if (@file_put_contents($fileName, $contents)) {
						$result = $fileName;
					}
				}
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return bool */
	public function isItMove() {return $this->isChildExist('move');}

	/**
	 * @param string|null $pathRelative
	 * @return string|null
	 */
	private function getPathFull($pathRelative) {
		/** @var string|null $result */
		$result = null;
		if ($pathRelative) {
			$result = df_cc_path(Mage::getBaseDir(), $pathRelative);
			/**
			 * Обратите внимание, что мы намеренно не проверяем здесь,
			 * присутствует ли файл, потому что он не обязан присутствовать,
			 * например, при вызове из @see getDestinationFull()
			 */
		}
		return $result;
	}

	/** @return string|null */
	public function getDestinationRelative() {return $this->leaf('destination');}

	/** @return string|null */
	private function getSourceRelative() {return $this->leaf('source');}

	/** @return bool */
	private function isSourceUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_contains($this->getSourceRelative(), 'http');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Localization_Onetime_Dictionary_Filesystem_Operations::itemClass()
	 * @used-by Df_Localization_Onetime_Processor_Filesystem::_construct()
	 */

}


 