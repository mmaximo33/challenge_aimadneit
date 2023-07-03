<?php declare(strict_types=1);

namespace Tm\Provider\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public const TABLE_NAME = 'tm_provider_interaction';

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable(self::TABLE_NAME);
        if ($installer->getConnection()->isTableExists($tableName) !== true) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'type',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Type operation'
                )
                ->addColumn(
                    'summary',
                    Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true],
                    'Summary'
                )
                ->setComment('Tabla de ejemplo');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
