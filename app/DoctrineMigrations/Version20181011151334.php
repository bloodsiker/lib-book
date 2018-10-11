<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181011151334
 */
final class Version20181011151334 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Author (AuthorBundle)";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        // author
        $author = $schema->createTable('authors');
        $author->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $author->addColumn('name', 'string', array('length' => 100, 'notnull' => true));
        $author->addColumn('slug', 'string', array('length' => 100, 'notnull' => true));
        $author->addColumn('created_at', 'datetime', array('notnull' => true));
        $author->addColumn('is_active', 'boolean', array('notnull' => true));
        $author->setPrimaryKey(array('id'));
        $author->addUniqueIndex(array('slug'));
//        $author->addForeignKeyConstraint($schema->getTable('user_users'), array('created_by'), array('id'), array('onDelete' => 'set null'));
//        $author->addForeignKeyConstraint($schema->getTable('user_users'), array('modified_by'), array('id'), array('onDelete' => 'set null'));

        // tvVideo's data
//        $tvVideoTranslation = $schema->createTable('tv_video_translation');
//        $tvVideoTranslation->addColumn('id', 'integer', array('notnull' => true, 'autoincrement' => true));
//        $tvVideoTranslation->addColumn('translatable_id', 'integer', array('unsigned' => true, 'notnull' => false));
//        $tvVideoTranslation->addColumn('title', 'string', array('length' => 255, 'notnull' => false));
//        $tvVideoTranslation->addColumn('header', 'text', array('length' => 65535, 'notnull' => false));
//        $tvVideoTranslation->addColumn('description', 'text', array('length' => 16777215, 'notnull' => false));
//        $tvVideoTranslation->addColumn('meta_title', 'string', array('length' => 255, 'notnull' => false));
//        $tvVideoTranslation->addColumn('meta_keywords', 'text', array('length' => 65535, 'notnull' => false));
//        $tvVideoTranslation->addColumn('meta_description', 'text', array('length' => 16777215, 'notnull' => false));
//        $tvVideoTranslation->addColumn('views', 'integer', array('unsigned' => true, 'notnull' => false, 'default' => 0));
//        $tvVideoTranslation->addColumn('locale', 'string', array('length' => 2, 'notnull' => true));
//        $tvVideoTranslation->setPrimaryKey(array('id'));
//        $tvVideoTranslation->addUniqueIndex(array('translatable_id', 'locale'));
//        $tvVideoTranslation->addForeignKeyConstraint($tvVideo, array('translatable_id'), array('id'), array('onDelete' => 'cascade'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable('authors');
    }
}
