<?php
class Df_CustomerBalance_Model_Setup_1_0_0 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var string $t_BALANCE */
		$t_BALANCE = rm_table('df_customerbalance/balance');
		/** @var string $t_HISTORY */
		$t_HISTORY = rm_table('df_customerbalance/balance_history');
		$this->getSetup()->run("
			CREATE TABLE `{$t_BALANCE}` (
				`balance_id` int(10) unsigned NOT null AUTO_INCREMENT
				, `customer_id` int(10) unsigned NOT null DEFAULT 0
				, `website_id` smallint(5) unsigned NOT null DEFAULT 0
				, `amount` decimal(12,4) NOT null DEFAULT 0
				, PRIMARY KEY (`balance_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			CREATE TABLE `{$t_HISTORY}` (
				`history_id` int(10) unsigned NOT null AUTO_INCREMENT
				, `balance_id` int(10) unsigned NOT null DEFAULT 0
				, `updated_at` datetime null DEFAULT null
				, `action` tinyint(3) unsigned NOT null default '0'
				, `balance_amount` decimal(12,4) unsigned NOT null DEFAULT 0
				, `balance_delta` decimal(12,4) NOT null DEFAULT 0
				, `additional_info` tinytext COLLATE utf8_general_ci null
				, `is_customer_notified` tinyint(1) unsigned NOT null DEFAULT 0
				, PRIMARY KEY (`history_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		$this->conn()->addConstraint(
			'FK_CUSTOMERBALANCE_CUSTOMER'
			, $t_BALANCE
			, 'customer_id'
			, rm_table('customer/entity')
			, 'entity_id'
		);
		$this->conn()->addKey(
			$t_BALANCE
			, 'UNQ_CUSTOMERBALANCE_CW'
			, array('customer_id', 'website_id')
			, 'unique'
		);
		$this->conn()->addConstraint(
			'FK_CUSTOMERBALANCE_HISTORY_BALANCE'
			, $t_HISTORY
			, 'balance_id'
			, $t_BALANCE
			, 'balance_id'
		);
		$this->addAttributes(array(
			array('quote', 'customer_balance_amount_used', array('type'=>'decimal'))
			,array('quote', 'base_customer_balance_amount_used', array('type'=>'decimal'))
			,array('quote_address', 'base_customer_balance_amount', array('type'=>'decimal'))
			,array('quote_address', 'customer_balance_amount', array('type'=>'decimal'))
			,array('order', 'base_customer_balance_amount', array('type'=>'decimal'))
			,array('order', 'customer_balance_amount', array('type'=>'decimal'))
			,array('order', 'base_customer_balance_invoiced', array('type'=>'decimal'))
			,array('order', 'customer_balance_invoiced', array('type'=>'decimal'))
			,array('order', 'base_customer_balance_refunded', array('type'=>'decimal'))
			,array('order', 'customer_balance_refunded', array('type'=>'decimal'))
			,array('invoice', 'base_customer_balance_amount', array('type'=>'decimal'))
			,array('invoice', 'customer_balance_amount', array('type'=>'decimal'))
			,array('creditmemo', 'base_customer_balance_amount', array('type'=>'decimal'))
			,array('creditmemo', 'customer_balance_amount', array('type'=>'decimal'))
			,array('quote', 'use_customer_balance', array('type'=>'int'))
		));
		$this->conn()->changeColumn(
			$t_BALANCE, 'website_id', 'website_id', 'smallint(5) unsigned null DEFAULT null'
		);
		$this->conn()->addConstraint(
			'FK_CUSTOMERBALANCE_WEBSITE'
			, $t_BALANCE
			, 'website_id'
			,rm_table('core/website')
			, 'website_id'
			, 'SET null'
		);
		$this->getSetup()->run("DELETE FROM {$t_BALANCE} WHERE website_id IS null;");
		$this->conn()->dropForeignKey($t_BALANCE, 'FK_CUSTOMERBALANCE_WEBSITE');
		$this->conn()->dropKey($t_BALANCE, 'FK_CUSTOMERBALANCE_WEBSITE');
		$this->conn()->changeColumn(
			$t_BALANCE, 'website_id', 'website_id','smallint(5) unsigned NOT null DEFAULT 0'
		);
		$this->conn()->addConstraint(
			'FK_CUSTOMERBALANCE_WEBSITE'
			, $t_BALANCE
			, 'website_id'
			, rm_table('core/website')
			, 'website_id'
		);
		$this->addAttributes(array(
			array('creditmemo', 'base_customer_balance_total_refunded', array('type'=>'decimal'))
			,array('creditmemo', 'customer_balance_total_refunded', array('type'=>'decimal'))
			,array('order', 'base_customer_balance_total_refunded', array('type'=>'decimal'))
			,array('order', 'customer_balance_total_refunded', array('type'=>'decimal'))
		));
		$this->conn()->addColumn($t_BALANCE, 'base_currency_code', 'CHAR( 3 ) null DEFAULT null');
		$this->conn()->changeColumn(
			$t_BALANCE,'website_id', 'website_id', 'SMALLINT(5) UNSIGNED null DEFAULT null'
		);
		$this->conn()->addConstraint(
			'FK_CUSTOMERBALANCE_WEBSITE'
			, $t_BALANCE
			, 'website_id'
			,rm_table('core/website')
			, 'website_id'
			, 'SET null'
		);
		rm_cache_clean();
	}

	/**
	 * @param array(array(string|array(string => string))) $attributesData
	 * @return void
	 */
	private function addAttributes(array $attributesData) {
		foreach ($attributesData as $attributeData) {
			/** @var array(string|array(string => string)) $attributeData */
			call_user_func_array(array($this->getSetupSales(), 'addAttribute'), $attributeData);
		}
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_CustomerBalance_Model_Setup_1_0_0
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}