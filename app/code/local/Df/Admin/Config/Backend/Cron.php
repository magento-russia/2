<?php
abstract class Df_Admin_Config_Backend_Cron extends Mage_Core_Model_Config_Data {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getCronJobName();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTimeConfigFieldName();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTimeConfigGroupName();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getFrequencyConfigFieldName();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getFrequencyConfigGroupName();

	/**
	 * @override
	 * @throws Exception
	 * @return void
	 */
	protected function _afterSave() {
		try {
			$this
				->saveConfigKey(
					$this->getCronSchedulePath(), $this->getCronExpression()
				)
			;
			$this->saveConfigKey(
				$this->getCronModelPath(), rm_leaf_s(rm_config_node($this->getCronModelPath()))
			);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
			//throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
		}
	}

	/**
	 * @param string $groupName
	 * @param string $fieldName
	 * @return mixed
	 */
	private function getConfigFieldValue($groupName, $fieldName) {
		df_param_string($groupName, 0);
		df_param_string($fieldName, 1);
		/** @var mixed $result */
		$result = $this->getData(df_concat_xpath('groups', $groupName, 'fields', $fieldName, 'value'));
		return $result;
	}

	/** @return string */
	private function getCronExpression() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode(' ', array(
				// минута
				$this->getMinute()
				,// час
				$this->getHour()
				,// день месяца
					(
							$this->getFrequency()
						===
							Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY
					)
					? '1'
					: '*'
				,// месяц года
				'*'
				,// день недели
					(
							$this->getFrequency()
						===
							Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY
					)
				? '1'
				: '*'
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getCronModelPath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 'crontab/jobs/' . $this->getCronJobName() . '/run/model';
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getCronSchedulePath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 'crontab/jobs/' . $this->getCronJobName() . '/schedule/cron_expr';
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getFrequency() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getConfigFieldValue(
					$this->getFrequencyConfigGroupName()
					, $this->getFrequencyConfigFieldName()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getHour() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_nat0(df_trim_left(dfa($this->getTime(), 0), '0'));
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getMinute() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_nat0(df_trim_left(dfa($this->getTime(), 1), '0'));
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	private function getTime() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getConfigFieldValue(
					$this->getTimeConfigGroupName()
					,$this->getTimeConfigFieldName()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $path
	 * @param string $value
	 * @return Df_Admin_Config_Backend_Cron
	 */
	private function saveConfigKey($path, $value) {
		df_param_string($path, 0);
		df_param_string($value, 1);
		/** @var Df_Core_Model_Config_Data $configData */
		$configData = Df_Core_Model_Config_Data::i();
		$configData->load($path, 'path');
		$configData->setValue($value);
		$configData->setPath($path);
		$configData->save();
		return $this;
	}
}