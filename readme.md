Esta é uma API simples desenvolvida em PHP puro que permite realizar operações CRUD (Criar, Ler, Atualizar e Deletar) em um arquivo produtos.json. Ela funciona como um sistema de gerenciamento de produtos.

🔧 Requisitos
PHP 7.0 ou superior

Servidor local (XAMPP, WAMP, ou o servidor embutido do PHP)

🚀 Como iniciar
Salve o código PHP em um arquivo chamado api.php

Crie um arquivo vazio chamado produtos.json no mesmo diretório

Inicie um servidor PHP:

bash
Copiar
Editar
php -S localhost:8000
Acesse a API via http://localhost:8000/api.php/produtos

📌 Endpoints disponíveis
Método	Endpoint	Descrição
GET	/api.php/produtos	Lista todos os produtos
GET	/api.php/produtos?nome=xxx	Busca produtos por nome
GET	/api.php/produtos/{id}	Busca um produto pelo ID
POST	/api.php/produtos	Cria um novo produto
PUT	/api.php/produtos?id={id}	Atualiza um produto pelo ID
DELETE	/api.php/produtos?id={id}	Remove um produto pelo ID
📬 Como usar via Postman
▶️ 1. Listar todos os produtos
Método: GET

URL: http://localhost:8000/api.php/produtos

Corpo: não necessário

🔍 2. Buscar produto por nome
Método: GET

URL: http://localhost:8000/api.php/produtos?nome=cafe

🔍 3. Buscar produto por ID
Método: GET

URL: http://localhost:8000/api.php/produtos/1

➕ 4. Criar um novo produto
Método: POST

URL: http://localhost:8000/api.php/produtos

Headers:

Content-Type: application/json

Body (raw → JSON):

json
Copiar
Editar
{
  "nome": "Café",
  "preco": 9.99,
  "quantidade": 100
}
✏️ 5. Atualizar um produto
Método: PUT

URL: http://localhost:8000/api.php/produtos?id=1

Headers:

Content-Type: application/json

Body (raw → JSON):

json
Copiar
Editar
{
  "nome": "Café Premium",
  "preco": 14.99
}
Obs: Você pode atualizar qualquer campo (nome, preco ou quantidade), e os demais permanecem como estavam.

❌ 6. Deletar um produto
Método: DELETE

URL: http://localhost:8000/api.php/produtos?id=1

⚠️ Observações
A API armazena os dados em um arquivo produtos.json, então os dados são persistentes apenas localmente.

Requisições PUT e DELETE usam parâmetros na URL (id), e o corpo da requisição deve ser em JSON (para PUT).

Se o ID fornecido não existir, a API retorna erro 404.