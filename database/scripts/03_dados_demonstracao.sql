SET NAMES utf8mb4;

DELETE FROM Livro_Assunto;
DELETE FROM Livro_Autor;
DELETE FROM Livro;
DELETE FROM Autor;
DELETE FROM Assunto;

INSERT INTO Autor (codAu, Nome) VALUES
    (1, 'Machado de Assis'),
    (2, 'Teste Tes'),
    (3, 'Jorge Amado'),
    (4, 'Ajustado Macedp'),
    (5, 'Carlos Drummond de Andrade'),
    (6, 'Asuus sdsadasd');

INSERT INTO Assunto (codAs, Descricao) VALUES
    (1, 'Romance'),
    (2, 'Poesia'),
    (3, 'Realismo'),
    (4, 'Modernismo'),
    (5, 'Regionalismo'),
    (6, 'Clássico');

INSERT INTO Livro (codl, Titulo, Editora, Edicao, AnoPublicacao, Valor) VALUES
    (1, 'Dom Casmurro', 'Garnier', 1, '1899', 39.90),
    (2, 'Teste teste', 'Saraiva', 1, '1999', 42.50),
    (3, 'A Hora da Rush', 'Cinema', 1, '2000', 34.90),
    (4, 'Atestado', 'José do Teste', 1, '1998', 44.90),
    (5, 'Agora é assim', 'Atemporal', 1, '2025', 36.00),
    (6, 'Bliblia', 'Celestial', 1, '0001', 49.90),
    (7, 'Mais de 500 cbd', 'Financio', 1, '2026', 38.00),
    (8, 'Sol Preciosa', 'Editora As', 2, '2015', 59.90),
    (9, 'Escolhida', 'Editora', 1, '2010', 29.90);

INSERT INTO Livro_Autor (Livro_Codl, Autor_CodAu) VALUES
    (1, 1), (2, 1), (3, 2), (4, 3), (5, 6), (6, 4), (7, 5),
    (8, 4), (8, 5),
    (9, 5);

INSERT INTO Livro_Assunto (Livro_Codl, Assunto_codAs) VALUES
    (1, 1), (1, 3), (1, 6),
    (2, 1), (2, 3), (2, 6),
    (3, 1), (3, 4),
    (4, 1), (4, 4),
    (5, 1), (5, 4), (5, 5),
    (6, 2),
    (7, 2), (7, 4),
    (8, 2), (8, 4);
