<?php

// Define o cabeçalho para JSON e permite requisições de outros domínios (CORS)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$produtosFile = 'produtos.json';

// Função para carregar os produtos do arquivo
function carregarProdutos() {
    global $produtosFile;
    if (!file_exists($produtosFile)) {
        file_put_contents($produtosFile, json_encode([]));
    }
    $data = file_get_contents($produtosFile);
    return json_decode($data, true);
}

// Função para salvar os produtos no arquivo
function salvarProdutos($produtos) {
    global $produtosFile;
    file_put_contents($produtosFile, json_encode($produtos, JSON_PRETTY_PRINT));
}

// Função para obter o próximo ID disponível
function proximoId($produtos) {
    $ids = array_column($produtos, 'id');
    return empty($ids) ? 1 : max($ids) + 1;
}

// Rota e lógica principal
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('?', $uri)[0]; // Remove parâmetros da URL

if (preg_match('/\/produtos(\?.*)?$/', $uri)) {
    $produtos = carregarProdutos();

    switch ($method) {
        case 'GET':
            // Para /produtos/{id} ou /produtos
            $partes = explode('/', trim($uri, '/'));

            // Se o caminho for /api.php/produtos/{id}
            if (count($partes) === 3 && is_numeric($partes[2])) {
                $id = (int) $partes[2];
                $produto = array_filter($produtos, fn($p) => $p['id'] === $id);

                if (empty($produto)) {
                    http_response_code(404);
                    echo json_encode(['erro' => 'Produto não encontrado']);
                } else {
                    echo json_encode(array_values($produto)[0]);
                }
            }
            // Se for /produtos?nome=xxx
            elseif (isset($_GET['nome'])) {
                $nomeBusca = strtolower($_GET['nome']);
                $filtrados = array_filter($produtos, function($produto) use ($nomeBusca) {
                    return strpos(strtolower($produto['nome']), $nomeBusca) !== false;
                });
                echo json_encode(array_values($filtrados));
            }
            // Se for apenas /produtos
            else {
                echo json_encode($produtos);
            }
            break;

        case 'POST':
            // Verifica se os campos 'nome', 'preco' e 'quantidade' estão presentes
            if (!isset($input['nome']) || !isset($input['preco']) || !isset($input['quantidade'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Nome, preço e quantidade são obrigatórios']);
                exit;
            }
            $novoProduto = [
                'id' => proximoId($produtos),
                'nome' => $input['nome'],
                'preco' => $input['preco'],
                'quantidade' => $input['quantidade'],
            ];
            $produtos[] = $novoProduto;
            salvarProdutos($produtos);
            echo json_encode($novoProduto);
            break;

        case 'PUT':
            parse_str($_SERVER['QUERY_STRING'], $params);
            if (!isset($params['id'])) {
                http_response_code(400);
                echo json_encode(['erro' => 'ID é obrigatório para atualização']);
                exit;
            }
            $id = (int) $params['id'];
            $encontrado = false;

            foreach ($produtos as &$p) {
                if ($p['id'] === $id) {
                    $p['nome'] = $input['nome'] ?? $p['nome'];
                    $p['preco'] = $input['preco'] ?? $p['preco'];
                    $p['quantidade'] = $input['quantidade'] ?? $p['quantidade'];
                    $encontrado = true;
                    break;
                }
            }

            if (!$encontrado) {
                http_response_code(404);
                echo json_encode(['erro' => 'Produto não encontrado']);
                exit;
            }

            salvarProdutos($produtos);
            echo json_encode(['mensagem' => 'Produto atualizado com sucesso']);
            break;

        case 'DELETE':
            parse_str($_SERVER['QUERY_STRING'], $params);
            if (!isset($params['id'])) {
                http_response_code(400);
                echo json_encode(['erro' => 'ID é obrigatório para exclusão']);
                exit;
            }
            $id = (int) $params['id'];
            $novoArray = array_filter($produtos, fn($p) => $p['id'] !== $id);

            if (count($novoArray) === count($produtos)) {
                http_response_code(404);
                echo json_encode(['erro' => 'Produto não encontrado']);
                exit;
            }

            salvarProdutos(array_values($novoArray));
            echo json_encode(['mensagem' => 'Produto excluído com sucesso']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido']);
    }
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Endpoint não encontrado']);
}

?>
