<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181213145836
 */
final class Version20181213145836 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "OrderBoard (OrderBundle))";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        // bookComments
        $bookComment = $schema->createTable('order_board');
        $bookComment->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $bookComment->addColumn('book_title', 'string', array('length' => 255, 'notnull' => true));
        $bookComment->addColumn('user_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $bookComment->addColumn('user_name', 'string', array('length' => 255, 'notnull' => false));
        $bookComment->addColumn('status', 'smallint', array('length' => 1, 'notnull' => true));
        $bookComment->addColumn('vote', 'smallint', array('length' => 6, 'notnull' => true, 'default' => 0));
        $bookComment->addColumn('created_at', 'datetime', array('notnull' => true));
        $bookComment->setPrimaryKey(['id']);
        $bookComment->addIndex(['user_id']);
        $bookComment->addForeignKeyConstraint($schema->getTable('user_users'), ['user_id'], ['id'], ['onDelete' => 'set null']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable('order_board');
    }
}
