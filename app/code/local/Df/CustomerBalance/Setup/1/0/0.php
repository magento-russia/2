<?php
class Df_CustomerBalance_Setup_1_0_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$t_BALANCE = df_table(Df_CustomerBalance_Model_Resource_Balance::TABLE);
		$t_HISTORY = df_table(Df_CustomerBalance_Model_Resource_Balance_History::TABLE);
		$this->dropTable($t_BALANCE);
		$this->dropTable($t_HISTORY);
		$this->run("
			create table if not exists `{$t_BALANCE}` (
				`balance_id` int(10) unsigned NOT null AUTO_INCREMENT
				, `customer_id` int(10) unsigned NOT null DEFAULT 0
				, `website_id` smallint(5) unsigned NOT null DEFAULT 0
				, `amount` decimal(12,4) NOT null DEFAULT 0
				, PRIMARY KEY (`balance_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			create table if not exists `{$t_HISTORY}` (
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
			, df_table('customer/entity')
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
			,df_table('core/website')
			, 'website_id'
			, 'SET null'
		);
		$this->run("DELETE FROM {$t_BALANCE} WHERE website_id IS null;");
		$this->conn()->dropForeignKey($t_BALANCE, 'FK_CUSTOMERBALANCE_WEBSITE');
		$this->conn()->dropKey($t_BALANCE, 'FK_CUSTOMERBALANCE_WEBSITE');
		$this->conn()->changeColumn(
			$t_BALANCE, 'website_id', 'website_id','smallint(5) unsigned NOT null DEFAULT 0'
		);
		$this->conn()->addConstraint(
			'FK_CUSTOMERBALANCE_WEBSITE'
			, $t_BALANCE
			, 'website_id'
			, df_table('core/website')
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
			,df_table('core/website')
			, 'website_id'
			, 'SET null'
		);
	}

	/**
	 * @param array(array(string|array(string => string))) $attributesData
	 * @return void
	 */
	private function addAttributes(array $attributesData) {
		/**
		 * 2014-02-10
		 * Не приходит в голову, как здесь использовать @see array_map().
		 * Вариант array_map(array($this->getSetupSales(), 'addAttribute'), $attributesData);
		 * неправильный, потому что тогда метод @uses Mage_Sales_Model_Mysql4_Setup::addAttribute()
		 * получит всего один параметр из массива.
		 */
		foreach ($attributesData as $attributeData) {
			/** @var array(string|array(string => string)) $attributeData */
			call_user_func_array(array($this->getSetupSales(), 'addAttribute'), $attributeData);
		}
	}
}