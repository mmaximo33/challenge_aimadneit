<?php declare(strict_types=1);
namespace Tm\BestOfferSeller\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public const COLUMN_NAME = "tm_best_offer_seller";

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $tables = ['quote_item', 'sales_order_item'];
            $opts = [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Data Best offer seller'
            ];

            foreach ($tables as $table) {
                $tableItems = $setup->getTable($table);
                $setup->getConnection()
                    ->addColumn(
                        $tableItems,
                        self::COLUMN_NAME,
                        $opts
                    );
            }
        }

        $setup->endSetup();
    }
}
