<?php
abstract class Df_Dataflow_Model_Registry_MultiCollection
	extends Df_Core_Model_Abstract implements IteratorAggregate {
	/**
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Dataflow_Model_Registry_Collection
	 */
	abstract protected function getCollectionForStore(Mage_Core_Model_Store $store);

	/**
	 * @override
	 * @return Traversable
	 */
	public function getIterator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new ArrayIterator($this->getEntities());
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	public function save() {
		foreach ($this->getCollections() as $collection) {
			/** @var Df_Dataflow_Model_Registry_Collection $collection */
			$collection->save();
		}
	}

	/** @return array(int => Df_Dataflow_Model_Registry_Collection) */
	private function getCollections() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => Df_Dataflow_Model_Registry_Collection) $result */
			$result = array();
			foreach (Mage::app()->getStores($withDefault = true) as $store) {
				/** @var Mage_Core_Model_Store $store */
				$result[$store->getId()] = $this->getCollectionForStore($store);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Model_Abstract[] */
	private function getEntities() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Abstract[] $result */
			$result = array();
			foreach ($this->getCollections() as $collection) {
				/** @var Df_Dataflow_Model_Registry_Collection $collection */
				foreach ($collection as $entity) {
					/** @var Mage_Core_Model_Abstract $entity */
					$result[]= $entity;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	}
