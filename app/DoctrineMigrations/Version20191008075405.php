<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191008075405 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return "Add ArticleHasBook (ArticleBundle))";
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema) : void
    {
        // articleHasBook
        $articleHasBook = $schema->createTable('article_article_has_book');
        $articleHasBook->addColumn('id', 'integer', array('unsigned' => true, 'notnull' => true, 'autoincrement' => true));
        $articleHasBook->addColumn('book_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $articleHasBook->addColumn('article_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $articleHasBook->addColumn('order_num', 'integer', array('length' => 11, 'notnull' => true, 'default' => 1));
        $articleHasBook->setPrimaryKey(['id']);
        $articleHasBook->addIndex(['book_id', 'article_id']);
        $articleHasBook->addForeignKeyConstraint($schema->getTable('books'), ['book_id'], ['id'], ['onDelete' => 'restrict']);
        $articleHasBook->addForeignKeyConstraint($schema->getTable('article_article'), ['article_id'], ['id'], ['onDelete' => 'restrict']);

        // articleGenres
        $articleGenres = $schema->createTable('article_article_genres');
        $articleGenres->addColumn('article_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $articleGenres->addColumn('genre_id', 'integer', array('unsigned' => true, 'notnull' => true));
        $articleGenres->addIndex(['article_id', 'genre_id']);
        $articleGenres->addForeignKeyConstraint($schema->getTable('article_article'), ['article_id'], ['id'], ['onDelete' => 'cascade']);
        $articleGenres->addForeignKeyConstraint($schema->getTable('genres'), ['genre_id'], ['id'], ['onDelete' => 'cascade']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $schema->dropTable('article_article_has_book');
        $schema->dropTable('article_article_genres');
    }
}
