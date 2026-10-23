<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261023013220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria a view vw_livros_por_autor usada no relatório';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW vw_livros_por_autor AS
            SELECT
                a.codAu           AS autor_codigo,
                a.Nome            AS autor_nome,
                l.codl            AS livro_codigo,
                l.Titulo          AS titulo,
                l.Editora         AS editora,
                l.Edicao          AS edicao,
                l.AnoPublicacao   AS ano_publicacao,
                l.Valor           AS valor,
                GROUP_CONCAT(s.Descricao ORDER BY s.Descricao SEPARATOR ', ') AS assuntos
            FROM Autor a
            INNER JOIN Livro_Autor la  ON la.Autor_CodAu = a.codAu
            INNER JOIN Livro l         ON l.codl = la.Livro_Codl
            LEFT JOIN Livro_Assunto ls ON ls.Livro_Codl = l.codl
            LEFT JOIN Assunto s        ON s.codAs = ls.Assunto_codAs
            GROUP BY a.codAu, a.Nome, l.codl, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao, l.Valor
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW vw_livros_por_autor');
    }
}
