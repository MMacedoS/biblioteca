# Biblioteca — Cadastro de Livros

Sistema web de cadastro de livros, autores e assuntos, com relatório de livros
agrupados por autor (em tela e em PDF). Desenvolvido como desafio técnico.

## Tecnologias

| Ferramenta                | Versão  |
| ------------------------- | ------- |
| PHP                       | 8.3     |
| Symfony                   | 7.4 LTS |
| Doctrine ORM / Migrations | 3.x     |
| MySQL                     | 8.4 LTS |
| Nginx                     | 1.27    |
| Bootstrap                 | 5.3     |
| Dompdf                    | 3.x     |
| PHPUnit                   | 12      |

## Requisitos

- Docker e Docker Compose
- `make`
- Git

Em Windows, use o WSL2.

## Instalação

```bash
git clone https://github.com/MMacedoS/biblioteca
cd biblioteca
make setup
make seed
```

Acesse **http://localhost:8080**.

O `make setup` monta os containers, instala as dependências e cria as tabelas
e a view nos bancos de desenvolvimento e de testes.

| Serviço   | Endereço                                                          |
| --------- | ----------------------------------------------------------------- |
| Aplicação | http://localhost:8080                                             |
| MySQL     | `localhost:3307` (usuário `app`, senha `app`, banco `biblioteca`) |

## Comandos

| Comando                   | O que faz                                                |
| ------------------------- | -------------------------------------------------------- |
| `make setup`              | Prepara o projeto do zero                                |
| `make up` / `make down`   | Liga / desliga os containers                             |
| `make seed`               | Carrega os dados de demonstração (apaga os dados atuais) |
| `make migrate`            | Executa as migrations                                    |
| `make test`               | Executa os testes automatizados                          |
| `make cs` / `make cs-fix` | Verifica / corrige o padrão de código                    |
| `make sh`                 | Abre um terminal no container PHP                        |
| `make console c="..."`    | Executa um comando do Symfony                            |

## Testes

```bash
make test
```

- **Unitários** (`tests/Unit`): regras do relatório, como o agrupamento por autor,
  a soma em centavos e o total geral sem duplicar livros.
- **Funcionais** (`tests/Functional`): fluxos completos das telas, incluindo
  validações e a exclusão bloqueada de autor com livros.

Os testes usam o banco `biblioteca_test`. Cada teste roda dentro de uma transação
desfeita ao final, então os dados nunca se acumulam.

## Implantação manual (sem Symfony)

Os scripts em `database/scripts/` criam o banco em qualquer cliente MySQL 8,
na ordem:

1. `01_tabelas.sql` — tabelas e chaves estrangeiras
2. `02_view.sql` — view `vw_livros_por_autor`, usada pelo relatório
3. `03_dados_demonstracao.sql` — dados de exemplo (opcional)

## Arquitetura

```
src/
|-- Controller/            Recebe as requisições e devolve as páginas
|-- DTO/                   Dados dos formulários, com as validações
|-- Entity/                Entidades mapeadas conforme o modelo de dados
|-- Exception/             Exceções de domínio (regras de negócio)
|-- Form/                  Definição dos formulários
|-- Observer/              Entity Listeners: normalização de texto e log
|-- Repository/
|   |-- Contract/          Interfaces dos repositórios
|   |-- Doctrine/          Implementações com Doctrine
|-- Service/               Regras de negócio
```

Fluxo de um cadastro: **Controller -> Form -> DTO (validação) -> Service ->
Repository (contract) -> banco**, com o **Observer** reagindo à gravação.

Os services dependem das interfaces dos repositórios, não do Doctrine
(inversão de dependência). Isso permite testá-los com repositórios falsos.

## Modelo de dados

Cinco tabelas, conforme o modelo fornecido: `Livro`, `Autor`, `Assunto` e as
tabelas de ligação `Livro_Autor` e `Livro_Assunto`. O campo `Valor`
(`DECIMAL(10,2)`) foi acrescentado à tabela `Livro`, como solicitado.

- Excluir um livro remove seus vínculos (`ON DELETE CASCADE`).
- Excluir um autor ou assunto vinculado a livros é bloqueado pelo banco, e o
  sistema exibe uma mensagem explicando o motivo.

## Tratamento de erros

Erros de banco são convertidos em exceções de domínio específicas
(`AutorPossuiLivrosException`, `AssuntoPossuiLivrosException`,
`RegistroNaoEncontradoException`), sem captura genérica de exceções. A exclusão
é validada pela chave estrangeira no próprio banco, o que evita inconsistências
entre verificar e excluir.

## Principais decisões técnicas

- **MySQL**, para manter os nomes com maiúsculas do modelo sem aspas.
- **Valores monetários** como `DECIMAL` no banco, `string` no PHP e somas em
  centavos inteiros, sem erros de arredondamento.
- **Carregamento antecipado** dos autores e assuntos na listagem de livros,
  evitando o problema N+1 (1 consulta em vez de 1 + 2N).
- **Relatório lendo a view pelo DBAL**, já que a view não tem chave única.
- **Exclusões apenas por POST, com token CSRF.**
- **Padrão de código Symfony**, garantido pelo PHP-CS-Fixer.
