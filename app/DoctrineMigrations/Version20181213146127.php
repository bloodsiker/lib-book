<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181213146127
 */
final class Version20181213146127 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Add type series (SeriesBundle))";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        $series = $schema->getTable('series');
        $series->addColumn('type', 'integer', ['unsigned' => true, 'notnull' => false, 'length' => 1, 'default' => 1]);

        $book = $schema->getTable('books');
        $book->addColumn('series_publishing_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $book->addIndex(['series_publishing_id']);
        $book->addForeignKeyConstraint($series, ['series_publishing_id'], ['id'], ['onDelete' => 'set null']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
    }
}
