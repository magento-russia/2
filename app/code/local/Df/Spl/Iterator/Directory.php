<?php
abstract class Df_Spl_Iterator_Directory extends Df_Core_Model implements Iterator {
	/**
	 * @param SplFileInfo $fileInfo
	 * @return bool
	 */
	abstract protected function isValid(SplFileInfo $fileInfo);

	/**
	 * @override
	 * @return DirectoryIterator|null
	 */
	public function current() {
		/** @var DirectoryIterator $result */
		$result = $this->getIterator()->current();
		if ($this->isExistButNotValid()) {
			$this->next();
			$result = $this->current();
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	public function key() {return $this->getIterator()->key();}

	/**
	 * @override
	 * @return void
	 */
	public function next() {
		$this->getIterator()->next();
		if ($this->isExistButNotValid()) {
			$this->next();
		}
	}

	/**
	 * @override
	 * @return void
	 */
	public function rewind() {$this->getIterator()->rewind();}

	/**
	 * @override
	 * @return DirectoryIterator|null
	 */
	public function valid() {return $this->getIterator()->valid();}


	/** @return DirectoryIterator */
	private function getIterator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new DirectoryIterator($this->getPath());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPath() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->cfg(self::P__PATH);
			if (!is_dir($result)) {
				df_error('Программист ищет файлы в отсутствующей папке «%s».', $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isExistButNotValid() {
		return $this->getIterator()->valid() && !$this->isValid($this->getIterator()->current());
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PATH, DF_V_STRING_NE);
	}
	const P__PATH = 'path';
}