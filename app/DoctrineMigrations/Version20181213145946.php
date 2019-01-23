<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181213145946
 */
final class Version20181213145946 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Add photo to Author (ShareBundle))";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        $author = $schema->getTable('share_authors');
        $author->addColumn('photo_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $author->addIndex(['photo_id']);
        $author->addForeignKeyConstraint($schema->getTable('media_image'), ['photo_id'], ['id'], ['onDelete' => 'set null']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
    }
}
