# Sistema Écoute Saveur - Casos de Uso e Implementação Técnica

## Introdução Técnica

O sistema **Écoute Saveur** foi desenvolvido integralmente em **PHP** utilizando os princípios da **Programação Orientada a Objetos (POO)**, seguindo o padrão arquitetural **MVC (Model-View-Controller)**. A aplicação representa uma solução completa para gestão de restaurantes, abrangendo desde o atendimento ao cliente até a administração completa do estabelecimento.

### Stack Tecnológica Utilizada
- **Backend**: PHP 8+ com POO
- **Banco de Dados**: MySQL com PDO
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Arquitetura**: MVC Pattern
- **Segurança**: Password hashing, sanitização de inputs, prepared statements
- **Servidor**: Apache (XAMPP)

---

## Arquitetura e Organização do Código

### Estrutura MVC Implementada

```
/src
├── /models/           # Operações CRUD e lógica de dados
├── /views/            # Interface do usuário e páginas
├── /controllers/      # Classes de entidade e lógica de negócio
├── /components/       # Componentes reutilizáveis (header, footer)
├── /helpers/          # Funções auxiliares
├── /db/              # Conexão com banco de dados
└── /Admin/           # Área administrativa completa
```

### Implementação POO
- **Classes de Entidade**: `Usuario.php`, `Produto.php`, `Categoria.php`, `Pedido.php`
- **Classes CRUD**: `Crud_usuario.php`, `Crud_produto.php`, `Crud_categoria.php`
- **Padrão Singleton**: Para conexão com banco de dados
- **Encapsulamento**: Propriedades privadas com getters/setters
- **Herança**: Relacionamentos entre entidades

---

## Casos de Uso e Funcionalidades por Módulo

## 1. MÓDULO DE AUTENTICAÇÃO

### Casos de Uso Implementados

#### 1.1 Cadastro de Usuário
**Funcionalidade**: Sistema de registro multi-etapas para novos clientes.

**Implementação Técnica**:
```php
// Arquivo: controllers/process_cadastro.php
class ProcessoCadastro {
    private $crudUsuario;
    
    public function __construct() {
        $this->crudUsuario = new Crud_usuario();
    }
    
    public function processarCadastro($dados) {
        // Validação de dados
        $this->validarDados($dados);
        
        // Hash da senha
        $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);
        
        // Criação do usuário
        $usuario = new Usuario();
        $usuario->setPrimeiro_nome($dados['primeiro_nome']);
        $usuario->setSegundo_nome($dados['segundo_nome']);
        $usuario->setEmail($dados['email']);
        $usuario->setSenha($senhaHash);
        
        return $this->crudUsuario->create($usuario);
    }
}
```

**Características**:
- Formulário multi-step com validação em tempo real
- Validação de email único no banco
- Hash seguro de senhas com `password_hash()`
- Sanitização de todos os inputs
- Feedback visual de erros/sucessos

#### 1.2 Login de Cliente
**Funcionalidade**: Autenticação segura com sessões.

**Implementação Técnica**:
```php
// Arquivo: controllers/process_login.php
public function autenticarUsuario($email, $senha) {
    $stmt = $this->conexao->prepare("SELECT * FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id'] = $usuario['id_usuario'];
        $_SESSION['primeiro_nome'] = $usuario['primeiro_nome'];
        $_SESSION['email'] = $usuario['email'];
        return true;
    }
    return false;
}
```

#### 1.3 Login Administrativo
**Funcionalidade**: Acesso restrito para funcionários.

**Implementação Técnica**:
```php
// Arquivo: Admin/login.php
public function autenticarAdmin($email, $senha) {
    $stmt = $this->conexao->prepare("SELECT * FROM funcionario WHERE email = ?");
    $stmt->execute([$email]);
    $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($funcionario && password_verify($senha, $funcionario['senha'])) {
        $_SESSION['admin_logado'] = true;
        $_SESSION['admin_id'] = $funcionario['id_funcionario'];
        $_SESSION['admin_nome'] = $funcionario['nome'];
        return true;
    }
    return false;
}
```

---

## 2. MÓDULO DE CARDÁPIO E PRODUTOS

### Casos de Uso Implementados

#### 2.1 Visualização do Cardápio
**Funcionalidade**: Exibição organizada de produtos por categoria.

**Implementação Técnica**:
```php
// Arquivo: views/cardapio.php
class GerenciadorCardapio {
    private $crudProduto;
    private $crudCategoria;
    
    public function obterProdutosPorCategoria() {
        $categorias = $this->crudCategoria->readAll();
        $produtos_por_categoria = [];
        
        foreach ($categorias as $categoria) {
            $produtos = $this->crudProduto->readByCategoria($categoria['id_categoria']);
            if (!empty($produtos)) {
                $produtos_por_categoria[$categoria['nome_categoria']] = $produtos;
            }
        }
        
        return $produtos_por_categoria;
    }
}
```

#### 2.2 Sistema de Busca Inteligente
**Funcionalidade**: Busca em tempo real com sugestões automáticas.

**Implementação Técnica**:
```javascript
// Arquivo: js/search.js
function buscarProdutos(termo) {
    fetch('api/search.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({termo: termo})
    })
    .then(response => response.json())
    .then(data => {
        exibirSugestoes(data.produtos);
    });
}

function exibirSugestoes(produtos) {
    const container = document.getElementById('suggestions');
    container.innerHTML = '';
    
    produtos.forEach(produto => {
        const item = criarItemSugestao(produto);
        container.appendChild(item);
    });
}
```

#### 2.3 Sistema de Favoritos
**Funcionalidade**: Salvar produtos preferidos do usuário.

**Implementação Técnica**:
```php
// Arquivo: models/Crud_favorito.php
class Crud_favorito {
    public function adicionarFavorito($id_usuario, $id_produto) {
        // Verificar se já existe
        if ($this->verificarFavorito($id_usuario, $id_produto)) {
            return ['success' => false, 'message' => 'Produto já está nos favoritos'];
        }
        
        $stmt = $this->conexao->prepare("
            INSERT INTO favorito (id_usuario, id_produto, data_adicao) 
            VALUES (?, ?, NOW())
        ");
        
        return $stmt->execute([$id_usuario, $id_produto]);
    }
    
    public function removerFavorito($id_usuario, $id_produto) {
        $stmt = $this->conexao->prepare("
            DELETE FROM favorito 
            WHERE id_usuario = ? AND id_produto = ?
        ");
        
        return $stmt->execute([$id_usuario, $id_produto]);
    }
}
```

---

## 3. MÓDULO DE CARRINHO E PEDIDOS

### Casos de Uso Implementados

#### 3.1 Gestão de Carrinho
**Funcionalidade**: Adicionar, remover e alterar quantidades de produtos.

**Implementação Técnica**:
```php
// Arquivo: controllers/Carrinho.php
class Carrinho {
    public function adicionarProduto($id_produto, $quantidade = 1) {
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
        
        if (isset($_SESSION['carrinho'][$id_produto])) {
            $_SESSION['carrinho'][$id_produto] += $quantidade;
        } else {
            $_SESSION['carrinho'][$id_produto] = $quantidade;
        }
        
        return ['success' => true, 'message' => 'Produto adicionado ao carrinho'];
    }
    
    public function atualizarQuantidade($id_produto, $nova_quantidade) {
        if ($nova_quantidade <= 0) {
            unset($_SESSION['carrinho'][$id_produto]);
        } else {
            $_SESSION['carrinho'][$id_produto] = $nova_quantidade;
        }
        
        return $this->calcularTotal();
    }
    
    public function calcularTotal() {
        $total = 0;
        $crudProduto = new Crud_produto();
        
        foreach ($_SESSION['carrinho'] as $id_produto => $quantidade) {
            $produto = $crudProduto->readById($id_produto);
            $total += $produto['preco'] * $quantidade;
        }
        
        return $total;
    }
}
```

#### 3.2 Processo de Checkout
**Funcionalidade**: Finalização de pedidos com dados de entrega.

**Implementação Técnica**:
```php
// Arquivo: views/checkout_processar.php
class ProcessadorCheckout {
    public function processarPedido($dados_entrega) {
        try {
            $this->conexao->beginTransaction();
            
            // Criar pedido principal
            $pedido = new Pedido();
            $pedido->setId_usuario($_SESSION['id']);
            $pedido->setEndereco_entrega($dados_entrega['endereco']);
            $pedido->setTelefone_contato($dados_entrega['telefone']);
            $pedido->setValor_total($this->calcularTotal());
            $pedido->setStatus_pedido('Pendente');
            
            $id_pedido = $this->crudPedido->create($pedido);
            
            // Adicionar produtos do pedido
            foreach ($_SESSION['carrinho'] as $id_produto => $quantidade) {
                $produto_pedido = new Produto_pedido();
                $produto_pedido->setId_pedido($id_pedido);
                $produto_pedido->setId_produto($id_produto);
                $produto_pedido->setQuantidade($quantidade);
                
                $this->crudProdutoPedido->create($produto_pedido);
            }
            
            $this->conexao->commit();
            unset($_SESSION['carrinho']);
            
            return ['success' => true, 'pedido_id' => $id_pedido];
            
        } catch (Exception $e) {
            $this->conexao->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
```

---

## 4. MÓDULO ADMINISTRATIVO

### Casos de Uso Implementados

#### 4.1 Gestão de Produtos
**Funcionalidade**: CRUD completo de produtos com upload de imagens.

**Implementação Técnica**:
```php
// Arquivo: Admin/produtos/create.php
class GerenciadorProdutos {
    public function criarProduto($dados, $arquivo_imagem) {
        // Upload da imagem
        $nome_imagem = $this->processarUploadImagem($arquivo_imagem);
        
        // Criar produto
        $produto = new Produto();
        $produto->setNome_produto($dados['nome']);
        $produto->setDescricao($dados['descricao']);
        $produto->setPreco($dados['preco']);
        $produto->setId_categoria($dados['categoria']);
        $produto->setImagem($nome_imagem);
        $produto->setDisponivel(true);
        
        return $this->crudProduto->create($produto);
    }
    
    private function processarUploadImagem($arquivo) {
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extensao, $extensoes_permitidas)) {
            throw new Exception('Formato de imagem não permitido');
        }
        
        $nome_arquivo = uniqid() . '.' . $extensao;
        $caminho_destino = '../images/comidas/' . $nome_arquivo;
        
        if (move_uploaded_file($arquivo['tmp_name'], $caminho_destino)) {
            return $nome_arquivo;
        }
        
        throw new Exception('Erro no upload da imagem');
    }
}
```

#### 4.2 Gestão de Funcionários
**Funcionalidade**: Controle de acesso e funcionários.

**Implementação Técnica**:
```php
// Arquivo: Admin/funcionarios/create.php
public function criarFuncionario($dados) {
    // Validar email único
    if ($this->emailJaExiste($dados['email'])) {
        throw new Exception('Email já cadastrado no sistema');
    }
    
    $funcionario = new Funcionario();
    $funcionario->setNome($dados['nome']);
    $funcionario->setEmail($dados['email']);
    $funcionario->setSenha(password_hash($dados['senha'], PASSWORD_DEFAULT));
    $funcionario->setCargo($dados['cargo']);
    $funcionario->setTelefone($dados['telefone']);
    
    return $this->crudFuncionario->create($funcionario);
}
```

#### 4.3 Gestão de Pedidos
**Funcionalidade**: Acompanhamento e atualização de status de pedidos.

**Implementação Técnica**:
```php
// Arquivo: Admin/pedidos/update_status.php
public function atualizarStatusPedido($id_pedido, $novo_status) {
    $status_validos = ['Pendente', 'Preparando', 'Pronto', 'Entregue', 'Cancelado'];
    
    if (!in_array($novo_status, $status_validos)) {
        throw new Exception('Status inválido');
    }
    
    $stmt = $this->conexao->prepare("
        UPDATE pedido 
        SET status_pedido = ?, data_atualizacao = NOW() 
        WHERE id_pedido = ?
    ");
    
    return $stmt->execute([$novo_status, $id_pedido]);
}
```

---

## 5. FUNCIONALIDADES AVANÇADAS

### 5.1 Sistema de Avaliações
**Funcionalidade**: Coleta e gestão de feedback dos clientes.

**Implementação Técnica**:
```php
// Arquivo: views/avaliar_pedido.php
public function processarAvaliacao($id_pedido, $nota, $comentario) {
    // Verificar se o pedido foi entregue
    $pedido = $this->crudPedido->readById($id_pedido);
    if ($pedido['status_pedido'] !== 'Entregue') {
        throw new Exception('Só é possível avaliar pedidos entregues');
    }
    
    // Verificar se já foi avaliado
    if ($this->jaFoiAvaliado($id_pedido)) {
        throw new Exception('Este pedido já foi avaliado');
    }
    
    $avaliacao = new Avaliacao();
    $avaliacao->setId_pedido($id_pedido);
    $avaliacao->setId_usuario($_SESSION['id']);
    $avaliacao->setNota($nota);
    $avaliacao->setComentario($comentario);
    
    return $this->crudAvaliacao->create($avaliacao);
}
```

### 5.2 Sistema de Upload de Imagens
**Funcionalidade**: Gerenciamento inteligente de imagens com URLs dinâmicas.

**Implementação Técnica**:
```php
// Arquivo: helpers/image_helper.php
function processarImagemProduto($imagem_path) {
    // Verificar se é URL externa
    if (filter_var($imagem_path, FILTER_VALIDATE_URL)) {
        return $imagem_path;
    }
    
    // Verificar se arquivo existe localmente
    $caminho_local = "./images/comidas/" . $imagem_path;
    if (file_exists($caminho_local)) {
        return $caminho_local;
    }
    
    // Retornar imagem padrão
    return "./assets/default-food.png";
}

function redimensionarImagem($arquivo_origem, $largura_max = 800) {
    $info = getimagesize($arquivo_origem);
    $tipo = $info[2];
    
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagem = imagecreatefromjpeg($arquivo_origem);
            break;
        case IMAGETYPE_PNG:
            $imagem = imagecreatefrompng($arquivo_origem);
            break;
        default:
            throw new Exception('Formato não suportado');
    }
    
    // Calcular nova dimensão mantendo proporção
    $largura_original = imagesx($imagem);
    $altura_original = imagesy($imagem);
    
    if ($largura_original > $largura_max) {
        $nova_largura = $largura_max;
        $nova_altura = ($altura_original * $largura_max) / $largura_original;
        
        $nova_imagem = imagecreatetruecolor($nova_largura, $nova_altura);
        imagecopyresampled($nova_imagem, $imagem, 0, 0, 0, 0, 
                          $nova_largura, $nova_altura, $largura_original, $altura_original);
        
        return $nova_imagem;
    }
    
    return $imagem;
}
```

---

## 6. SEGURANÇA E VALIDAÇÃO

### Implementações de Segurança

#### 6.1 Proteção contra SQL Injection
```php
// Uso consistente de prepared statements
public function buscarProduto($id) {
    $stmt = $this->conexao->prepare("SELECT * FROM produto WHERE id_produto = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

#### 6.2 Sanitização de Inputs
```php
public function sanitizarInput($dados) {
    return array_map(function($valor) {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }, $dados);
}
```

#### 6.3 Controle de Sessões
```php
// Verificação de autenticação em páginas protegidas
if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

// Verificação de admin
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit();
}
```

---

## 7. INTERFACE E EXPERIÊNCIA DO USUÁRIO

### 7.1 Design Responsivo
**Implementação CSS**:
```css
/* Mobile-first approach */
@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .category-list {
        overflow-x: auto;
        padding: 10px 0;
    }
}

@media (min-width: 1024px) {
    .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }
}
```

### 7.2 Interatividade JavaScript
```javascript
// Adicionar ao carrinho com feedback visual
function adicionarAoCarrinho(idProduto) {
    const botao = event.target;
    const textoOriginal = botao.innerHTML;
    
    // Feedback visual
    botao.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adicionando...';
    botao.disabled = true;
    
    fetch('./carrinho.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=add&produto_id=${idProduto}&quantidade=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            botao.innerHTML = '<i class="fas fa-check"></i> Adicionado!';
            botao.classList.add('success');
            showNotification('Produto adicionado ao carrinho!', 'success');
        } else {
            botao.innerHTML = textoOriginal;
            showNotification(data.message, 'error');
        }
        
        setTimeout(() => {
            botao.innerHTML = textoOriginal;
            botao.disabled = false;
            botao.classList.remove('success');
        }, 2000);
    });
}
```

---

## 8. ESTRUTURA DO BANCO DE DADOS

### Relacionamentos Implementados

```sql
-- Estrutura principal implementada
CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    primeiro_nome VARCHAR(50) NOT NULL,
    segundo_nome VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    foto_perfil VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categoria (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nome_categoria VARCHAR(100) NOT NULL,
    imagem VARCHAR(255),
    descricao TEXT
);

CREATE TABLE produto (
    id_produto INT PRIMARY KEY AUTO_INCREMENT,
    nome_produto VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255),
    id_categoria INT,
    disponivel BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
);

CREATE TABLE pedido (
    id_pedido INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_pedido ENUM('Pendente', 'Preparando', 'Pronto', 'Entregue', 'Cancelado') DEFAULT 'Pendente',
    valor_total DECIMAL(10,2) NOT NULL,
    endereco_entrega TEXT NOT NULL,
    telefone_contato VARCHAR(20),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE produto_pedido (
    id_produto_pedido INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido),
    FOREIGN KEY (id_produto) REFERENCES produto(id_produto)
);

CREATE TABLE favorito (
    id_favorito INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_produto INT NOT NULL,
    data_adicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_produto) REFERENCES produto(id_produto),
    UNIQUE KEY unique_favorito (id_usuario, id_produto)
);

CREATE TABLE avaliacao (
    id_avaliacao INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    id_usuario INT NOT NULL,
    nota INT NOT NULL CHECK (nota >= 1 AND nota <= 5),
    comentario TEXT,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE funcionario (
    id_funcionario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cargo VARCHAR(50) NOT NULL,
    telefone VARCHAR(20),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 9. CONCLUSÃO E DIFERENCIAIS TÉCNICOS

### Principais Diferenciais Implementados

1. **Arquitetura Robusta**: Implementação completa do padrão MVC com separação clara de responsabilidades.

2. **POO Avançada**: Uso extensivo de classes, herança, encapsulamento e polimorfismo.

3. **Segurança Multi-layer**: 
   - Prepared statements para todas as consultas
   - Hash seguro de senhas
   - Sanitização completa de inputs
   - Controle rigoroso de sessões

4. **UX Avançada**: 
   - Interface responsiva
   - Feedback visual em tempo real
   - Sistema de busca inteligente
   - Notificações interativas

5. **Gestão Inteligente de Recursos**:
   - Upload otimizado de imagens
   - URLs dinâmicas para recursos
   - Cache de sessão otimizado

6. **Modularidade e Manutenibilidade**:
   - Código bem organizado e documentado
   - Classes reutilizáveis
   - Helpers para funcionalidades específicas

### Métricas do Projeto

- **Total de Classes PHP**: 15+ classes principais
- **Linhas de Código**: 8000+ linhas
- **Páginas Funcionais**: 25+ páginas completas
- **Endpoints API**: 10+ endpoints para AJAX
- **Tabelas de Banco**: 8 tabelas com relacionamentos complexos
- **Funcionalidades**: 30+ casos de uso implementados

O sistema **Écoute Saveur** representa uma implementação completa e profissional de um sistema de gestão de restaurantes, demonstrando domínio técnico em desenvolvimento web full-stack com PHP orientado a objetos, seguindo as melhores práticas de segurança, usabilidade e arquitetura de software.