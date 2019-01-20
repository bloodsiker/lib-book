<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181213145846
 */
final class Version20181213145846 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Add parent (SeriesBundle))";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        $series = $schema->getTable('series');
        $series->addColumn('parent_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $series->addIndex(['parent_id']);
        $series->addForeignKeyConstraint($schema->getTable('series'), ['parent_id'], ['id'], ['onDelete' => 'set null']);

        $book = $schema->getTable('series');
        $book->addColumn('isbn', 'string', array('length' => 30, 'notnull' => false));

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
    }
}
