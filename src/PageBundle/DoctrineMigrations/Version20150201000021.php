<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150201000021 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Site variables (PageBundle)';
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $placement = $schema->createTable('page_site_variable_placement');
        $placement->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $placement->addColumn('alias', 'string', ['length' => 255, 'notnull' => true]);
        $placement->setPrimaryKey(['id']);
        $placement->addUniqueIndex(['alias']);

        $variable = $schema->createTable('page_site_variable');
        $variable->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $variable->addColumn('site_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $variable->addColumn('name', 'string', ['length' => 255, 'notnull' => true]);
        $variable->addColumn('value', 'text', ['length' => 65535, 'notnull' => false]);
        $variable->addColumn('placement_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $variable->addColumn('created_by', 'integer', ['unsigned' => true, 'notnull' => false]);
        $variable->addColumn('created_at', 'datetime', ['notnull' => true]);
        $variable->addColumn('modified_at', 'datetime', ['notnull' => true]);
        $variable->setPrimaryKey(['id']);
        $variable->addUniqueIndex(['site_id', 'name'], 'idx_unique_site_name');
        $variable->addForeignKeyConstraint($schema->getTable('page_site'), ['site_id'], ['id'], ['onDelete' => 'set null']);
        $variable->addForeignKeyConstraint($schema->getTable('user_users'), ['created_by'], ['id'], ['onDelete' => 'set null']);
        $variable->addForeignKeyConstraint($placement, ['placement_id'], ['id'], ['onDelete' => 'set null']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('page_site_variable');
        $schema->dropTable('page_site_variable_placement');
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postUp(Schema $schema)
    {
        $this->connection->executeQuery('
            INSERT INTO `page_site_variable_placement` (`id`, `alias`)
            VALUES (1, \'head\'),
                   (2, \'body-begin\'),
                   (3, \'body-end\'),
                   (4, \'branding\'),
                   (5, \'banner-top\'),
                   (6, \'banner-right\'),
                   (7, \'banner-content\'),
                   (8, \'banner-content-amp\'),
                   (9, \'footer-counters\'),
                   (10, \'about-widgets\'),
                   (11, \'banner-content-roll\')
   ');
    }
}
