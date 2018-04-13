<?php
/**
 * @author Mygento
 * @package Mygento_Base
 */
namespace Mygento\Base\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    const LOG_TABLE = 'mygento_base_log';

    /**
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $this->createLogTable($installer);

        $installer->endSetup();
    }

    /**
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    private function createLogTable($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(self::LOG_TABLE)
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'identity' => true],
            'Log ID'
        )->addColumn(
            'instance',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Server'
        )->addColumn(
            'channel',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Channel'
        )->addColumn(
            'level',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'level'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'log message'
        )->addColumn(
            'logged_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'log datetime'
        )->addColumn(
            'context',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'context'
        )->addColumn(
            'extra',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Extra data'
        )->addIndex(
            $installer->getIdxName($installer->getTable(self::LOG_TABLE), ['instance']),
            ['instance']
        )->addIndex(
            $installer->getIdxName($installer->getTable(self::LOG_TABLE), ['channel']),
            ['channel']
        )->addIndex(
            $installer->getIdxName($installer->getTable(self::LOG_TABLE), ['level']),
            ['level']
        )->setComment(
            'Modules log table'
        );
        $installer->getConnection()->createTable($table);
    }
}
