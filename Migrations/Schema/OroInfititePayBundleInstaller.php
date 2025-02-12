<?php

namespace Oro\Bundle\InfinitePayBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class OroInfititePayBundleInstaller implements Installation
{
    #[\Override]
    public function getMigrationVersion(): string
    {
        return 'v1_0';
    }

    #[\Override]
    public function up(Schema $schema, QueryBag $queries): void
    {
        /** Tables update */
        $this->updateOroIntegrationTransportTable($schema);

        /** Tables generation **/
        $this->createOroInfinitepayLblTable($schema);
        $this->createOroInfinitepayShortLblTable($schema);

        /** Foreign keys generation **/
        $this->addOroInfinitepayLblForeignKeys($schema);
        $this->addOroInfinitepayShortLblForeignKeys($schema);

        /** Add Vat Id */
        $this->addVatId($schema);
    }

    /**
     * Create oro_infinitepay_lbl table
     */
    private function createOroInfinitepayLblTable(Schema $schema): void
    {
        $table = $schema->createTable('oro_infinitepay_lbl');
        $table->addColumn('transport_id', 'integer');
        $table->addColumn('localized_value_id', 'integer');
        $table->setPrimaryKey(['transport_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'UNIQ_A5EE03E2EB576E89');
        $table->addIndex(['transport_id'], 'IDX_A5EE03E29909C13F');
    }

    /**
     * Create oro_infinitepay_short_lbl table
     */
    private function createOroInfinitepayShortLblTable(Schema $schema): void
    {
        $table = $schema->createTable('oro_infinitepay_short_lbl');
        $table->addColumn('transport_id', 'integer');
        $table->addColumn('localized_value_id', 'integer');
        $table->setPrimaryKey(['transport_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'UNIQ_1C78A0ACEB576E89');
        $table->addIndex(['transport_id'], 'IDX_1C78A0AC9909C13F');
    }

    /**
     * Update oro_integration_transport table
     */
    private function updateOroIntegrationTransportTable(Schema $schema): void
    {
        $table = $schema->getTable('oro_integration_transport');
        $table->addColumn('ipay_client_ref', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('ipay_username', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('ipay_password', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('ipay_secret', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('ipay_auto_capture', 'boolean', ['default' => '0', 'notnull' => false]);
        $table->addColumn('ipay_auto_activate', 'boolean', ['default' => '0', 'notnull' => false]);
        $table->addColumn('ipay_debug_mode', 'boolean', ['default' => '0', 'notnull' => false]);
        $table->addColumn('ipay_invoice_due_period', 'smallint', ['notnull' => false]);
        $table->addColumn('ipay_invoice_shipping_duration', 'smallint', ['notnull' => false]);
        $table->addColumn('ipay_test_mode', 'boolean', ['default' => '0', 'notnull' => false]);
    }

    /**
     * Add oro_infinitepay_lbl foreign keys.
     */
    private function addOroInfinitepayLblForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('oro_infinitepay_lbl');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_transport'),
            ['transport_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Add oro_infinitepay_short_lbl foreign keys.
     */
    private function addOroInfinitepayShortLblForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('oro_infinitepay_short_lbl');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_transport'),
            ['transport_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    private function addVatId(Schema $schema): void
    {
        $table = $schema->getTable('oro_customer');
        $table->addColumn(
            'vat_id',
            'text',
            [
                'oro_options' => [
                    'extend'   => ['owner' => ExtendScope::OWNER_CUSTOM],
                    'datagrid' => ['is_visible' => DatagridScope::IS_VISIBLE_FALSE],
                    'merge'    => ['display' => true],
                ]
            ]
        );
    }
}
