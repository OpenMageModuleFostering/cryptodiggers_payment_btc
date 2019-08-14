<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
Mage::log('install', null, 'cdpbtc.log');
$table = $installer->getConnection()
->newTable($installer->getTable('cdpbtc_payment/tran'))
->addColumn('cdp_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
'identity' => true,
'unsigned' => true,
'nullable' => false,
'primary' => true,
), 'Id')
->addColumn('orderid', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
'nullable' => false,
), 'Order id')
->addColumn('txid', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
'nullable' => false,
), 'Txid')
->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '18,8', array(
'nullable' => false,
), 'Price')
->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
'nullable' => false, "default"=>1,
), 'Status')
->addColumn('createdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
'nullable' => false,
'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
), 'Create at')
->setComment('CDPayment BTC');
$installer->getConnection()->createTable($table);
$installer->endSetup();