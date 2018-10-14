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
    public function getDescription() : string
    {
        return "Author, Genre, Series (AuthorBundle, GenreBundle), SeriesBundle))";
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

        // genre
        $genre = $schema->createTable('genres');
        $genre->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $genre->addColumn('parent_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $genre->addColumn('name', 'string', array('length' => 50, 'notnull' => true));
        $genre->addColumn('slug', 'string', array('length' => 50, 'notnull' => true));
        $genre->addColumn('created_at', 'datetime', array('notnull' => true));
        $genre->addColumn('is_active', 'boolean', array('notnull' => true));
        $genre->setPrimaryKey(array('id'));
        $genre->addUniqueIndex(array('slug'));
        $genre->addForeignKeyConstraint($schema->getTable('genres'), array('parent_id'), array('id'), array('onDelete' => 'set null'));

        // series
        $series = $schema->createTable('series');
        $series->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $series->addColumn('title', 'string', array('length' => 150, 'notnull' => true));
        $series->addColumn('slug', 'string', array('length' => 150, 'notnull' => true));
        $series->addColumn('created_at', 'datetime', array('notnull' => true));
        $series->addColumn('is_active', 'boolean', array('notnull' => true));
        $series->setPrimaryKey(array('id'));
        $series->addUniqueIndex(array('slug'));

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
        $schema->dropTable('series');
        $schema->dropTable('genres');
    }
}
