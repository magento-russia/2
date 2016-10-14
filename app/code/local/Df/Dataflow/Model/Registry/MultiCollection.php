<?php
abstract class Df_Dataflow_Model_Registry_MultiCollection
	extends Df_Core_Model implements IteratorAggregate {
	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return Df_Dataflow_Model_Registry_Collection
	 */
	abstract protected function getCollectionForStore(Df_Core_Model_StoreM $store);

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

	/**
	 * @return void
	 * @throws Df_Core_Exception_Batch|Exception
	 */
	public function save() {
		/** @var Df_Core_Exception_Batch $batchException */
		$batchException = new Df_Core_Exception_Batch();
		foreach ($this->getCollections() as $collection) {
			/** @var Df_Dataflow_Model_Registry_Collection $collection */
			try {
				$collection->save();
			}
			catch (Df_Core_Exception_Batch $partialBatch) {
				$batchException->addBatch($partialBatch);
			}
		}
		$batchException->throwIfNeeed();
	}

	/** @return array(int => Df_Dataflow_Model_Registry_Collection) */
	private function getCollections() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => Df_Dataflow_Model_Registry_Collection) $result */
			$result = array();
			foreach (Mage::app()->getStores($withDefault = true) as $store) {
				/** @var Df_Core_Model_StoreM $store */
				$result[$store->getId()] = $this->getCollectionForStore($store);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Model_Abstract[] */
	private function getEntities() {
		if (!isset($this->{__METHOD__})) {
			/** @uses iterator_to_array() */
			$this->{__METHOD__} = df_merge_single(array_map('iterator_to_array', $this->getCollections()));
		}
		return $this->{__METHOD__};
	}
	}
