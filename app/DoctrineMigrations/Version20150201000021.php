<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20150201000021
 */
final class Version20150201000021 extends AbstractMigration
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
     */
    public function up(Schema $schema)
    {
        $variable = $schema->createTable('page_site_variable');
        $variable->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
        $variable->addColumn('name', 'string', ['length' => 255, 'notnull' => true]);
        $variable->addColumn('value', 'text', ['length' => 65535, 'notnull' => false]);
        $variable->addColumn('placement', 'integer', ['unsigned' => true, 'notnull' => false]);
        $variable->addColumn('created_at', 'datetime', ['notnull' => true]);
        $variable->addColumn('modified_at', 'datetime', ['notnull' => true]);
        $variable->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('page_site_variable');
    }
}
