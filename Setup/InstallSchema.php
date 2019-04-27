<?php

namespace Mygento\Base\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'mygento_base_event'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mygento_base_event')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Event ID'
        )->addColumn(
            'instance',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Server'
        )->addColumn(
            'channel',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Channel'
        )->addColumn(
            'level',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Level'
        )->addColumn(
            'message',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Message'
        )->addColumn(
            'logged_at',
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Log Datetime'
        )->addColumn(
            'context',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Context'
        )->addColumn(
            'extra',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Extra'
        )->addIndex(
            $installer->getIdxName('mygento_base_event', ['instance']),
            ['instance']
        )->addIndex(
            $installer->getIdxName('mygento_base_event', ['channel']),
            ['channel']
        )->addIndex(
            $installer->getIdxName('mygento_base_event', ['level']),
            ['level']
        )->setComment(
            'mygento_base_event Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
