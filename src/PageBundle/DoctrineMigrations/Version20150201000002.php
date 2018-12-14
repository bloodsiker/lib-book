<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150201000002 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $pageTranslation = $schema->createTable('page_page_translation');
        $pageTranslation->addColumn('id', 'integer', array('notnull' => true, 'autoincrement' => true));
        $pageTranslation->addColumn('translatable_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $pageTranslation->addColumn('meta_title', 'string', array('length' => 255, 'notnull' => false));
        $pageTranslation->addColumn('meta_description', 'text', array('length' => 65535, 'notnull' => false));
        $pageTranslation->addColumn('meta_keywords', 'text', array('length' => 65535, 'notnull' => false));
        $pageTranslation->addColumn('html', 'text', ['length' => 16777215, 'notnull' => false, 'comment' => 'Raw HTML code']);
        $pageTranslation->addColumn('locale', 'string', array('length' => 255, 'notnull' => true));
        $pageTranslation->setPrimaryKey(array('id'));
        $pageTranslation->addUniqueIndex(array('translatable_id', 'locale'));
        $pageTranslation->addForeignKeyConstraint($schema->getTable('page_page'), array('translatable_id'), array('id'), array('onDelete' => 'SET NULL'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('page_page_translation');
    }
}
