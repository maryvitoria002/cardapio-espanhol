# Sistema de Restaurante Écoute Saveur
## Documentação Técnica e Funcional

---

## I. Introdução e Fundamentação

### Motivação e Descrição do Problema

O sistema **Écoute Saveur** foi desenvolvido para resolver os desafios enfrentados por restaurantes na gestão de pedidos online e experiência do cliente. Os principais problemas identificados incluem:

- **Dificuldade na gestão de pedidos**: Muitos restaurantes ainda dependem de métodos manuais ou sistemas fragmentados para gerenciar pedidos
- **Experiência do cliente fragmentada**: Falta de integração entre visualização do cardápio, pedidos e acompanhamento
- **Gestão administrativa complexa**: Necessidade de controle centralizado de produtos, categorias, funcionários e avaliações
- **Falta de feedback estruturado**: Ausência de sistema organizado para coleta e gestão de avaliações dos clientes

### Público-alvo e Contexto de Uso

**Público Principal:**
- **Clientes finais**: Usuários que desejam fazer pedidos online de forma prática e intuitiva
- **Funcionários do restaurante**: Equipe responsável pelo atendimento e processamento de pedidos
- **Administradores**: Gestores que precisam controlar estoque, cardápio, funcionários e relatórios

**Contexto de Uso:**
- Restaurantes de médio porte que buscam digitalizar seu processo de pedidos
- Estabelecimentos que querem oferecer experiência online integrada
- Negócios que necessitam de controle administrativo centralizado

---

## II. Metodologia de Desenvolvimento

### Organização do Time e Papéis

**Estrutura do Projeto:**
- **Desenvolvimento Full-Stack**: Implementação tanto do frontend quanto backend
- **Arquitetura MVC**: Organização clara entre Models (dados), Views (apresentação) e Controllers (lógica)
- **Desenvolvimento Iterativo**: Implementação por módulos funcionais

### Ferramentas Utilizadas

**Backend:**
- **PHP 8+**: Linguagem principal para lógica de negócio
- **MySQL**: Banco de dados relacional para persistência
- **PDO**: Interface de acesso ao banco de dados
- **Sessions**: Gerenciamento de autenticação e estado

**Frontend:**
- **HTML5**: Estrutura das páginas
- **CSS3**: Estilização e responsividade
- **JavaScript**: Interatividade e validações
- **Font Awesome**: Ícones e elementos visuais

**Infraestrutura:**
- **XAMPP**: Ambiente de desenvolvimento local
- **Apache**: Servidor web
- **Git**: Controle de versão

### Padrões de Desenvolvimento

- **Arquitetura MVC**: Separação clara de responsabilidades
- **CRUD Operations**: Operações básicas em todas as entidades
- **Orientação a Objetos**: Classes bem definidas para cada entidade
- **Helpers**: Funções auxiliares para tarefas específicas (ex: manipulação de imagens)

---

## III. Modelagem UML

### Diagrama de Caso de Uso

#### Principais Atores:

1. **Cliente**
2. **Funcionário** 
3. **Administrador**

#### Casos de Uso por Ator:

**Cliente:**
- Realizar cadastro
- Fazer login/logout
- Visualizar cardápio
- Filtrar produtos por categoria
- Adicionar produtos ao carrinho
- Gerenciar carrinho (adicionar, remover, alterar quantidade)
- Finalizar pedido (checkout)
- Acompanhar histórico de pedidos
- Avaliar pedidos entregues
- Gerenciar favoritos
- Atualizar configurações pessoais
- Fazer upload de foto de perfil

**Funcionário:**
- Visualizar pedidos em andamento
- Atualizar status de pedidos
- Visualizar detalhes de pedidos
- Gerenciar entregas

**Administrador:**
- **Gestão de Produtos:**
  - Criar, editar, visualizar, excluir produtos
  - Upload de imagens de produtos
  - Gerenciar categorias
- **Gestão de Funcionários:**
  - Cadastrar novos funcionários
  - Editar informações de funcionários
  - Controlar acesso ao sistema
- **Gestão de Pedidos:**
  - Visualizar todos os pedidos
  - Atualizar status de pedidos
  - Excluir pedidos quando necessário
- **Gestão de Usuários:**
  - Visualizar clientes cadastrados
  - Editar informações de usuários
- **Gestão de Avaliações:**
  - Visualizar feedback dos clientes
  - Monitorar satisfação

#### Relacionamentos:
- **Herança**: Funcionário e Administrador herdam de Usuário
- **Inclusão**: "Fazer Pedido" inclui "Adicionar ao Carrinho"
- **Extensão**: "Finalizar Pedido" pode estender para "Avaliar Pedido"

---

## IV. Modelagem de Dados

### Diagrama Entidade-Relacionamento

#### Entidades Principais:

**1. USUARIO**
- id_usuario (PK)
- primeiro_nome
- segundo_nome  
- email
- senha
- telefone
- foto_perfil
- data_criacao

**2. FUNCIONARIO**
- id_funcionario (PK)
- nome
- email
- senha
- telefone
- cargo
- data_criacao

**3. CATEGORIA**
- id_categoria (PK)
- nome_categoria
- imagem
- descricao

**4. PRODUTO**
- id_produto (PK)
- nome_produto
- descricao
- preco
- imagem
- id_categoria (FK)
- disponivel
- data_criacao

**5. PEDIDO**
- id_pedido (PK)
- id_usuario (FK)
- data_pedido
- status_pedido
- valor_total
- endereco_entrega
- telefone_contato

**6. PRODUTO_PEDIDO**
- id_produto_pedido (PK)
- id_pedido (FK)
- id_produto (FK)
- quantidade
- preco_unitario
- subtotal

**7. AVALIACAO**
- id_avaliacao (PK)
- id_pedido (FK)
- id_usuario (FK)
- nota
- comentario
- data_avaliacao

**8. FAVORITO**
- id_favorito (PK)
- id_usuario (FK)
- id_produto (FK)
- data_adicao

#### Relacionamentos:

- **USUARIO** 1:N **PEDIDO** (Um usuário pode ter vários pedidos)
- **PEDIDO** 1:N **PRODUTO_PEDIDO** (Um pedido pode ter vários produtos)
- **PRODUTO** 1:N **PRODUTO_PEDIDO** (Um produto pode estar em vários pedidos)
- **CATEGORIA** 1:N **PRODUTO** (Uma categoria pode ter vários produtos)
- **PEDIDO** 1:1 **AVALIACAO** (Um pedido pode ter uma avaliação)
- **USUARIO** 1:N **AVALIACAO** (Um usuário pode fazer várias avaliações)
- **USUARIO** N:M **PRODUTO** (através de FAVORITO)

### Integridade Referencial:
- Todas as foreign keys com constraints de integridade
- Cascata em exclusões onde apropriado
- Índices em campos de busca frequente

---

## V. Demonstração Funcional do Sistema

### Módulo Cliente

#### 1. **Autenticação e Cadastro**
```
Fluxo de Cadastro:
1. Acesso à página de cadastro
2. Preenchimento de dados pessoais (nome, email, telefone)
3. Criação de senha segura
4. Validação de email único
5. Criação de conta e redirecionamento para login
```

#### 2. **Navegação no Cardápio**
```
Funcionalidades:
- Visualização de produtos por categoria
- Sistema de busca com sugestões automáticas
- Filtros por categoria (Entradas, Massas, Carnes, etc.)
- Visualização detalhada de produtos
- Sistema de favoritos
```

#### 3. **Gestão de Carrinho**
```
Operações Disponíveis:
- Adicionar produtos com quantidade
- Alterar quantidades
- Remover itens
- Visualizar subtotais e total
- Limpar carrinho
- Continuar comprando
```

#### 4. **Processo de Checkout**
```
Etapas:
1. Revisão dos itens do carrinho
2. Preenchimento de dados de entrega
3. Confirmação do pedido
4. Processamento e geração de ID do pedido
5. Redirecionamento para acompanhamento
```

#### 5. **Histórico e Acompanhamento**
```
Informações Disponíveis:
- Lista de todos os pedidos
- Status atual (Pendente, Preparando, Entregue, Cancelado)
- Detalhes de cada pedido
- Opção de cancelamento (para pedidos pendentes)
- Sistema de avaliação pós-entrega
```

### Módulo Administrativo

#### 1. **Dashboard Administrativo**
```
Recursos:
- Painel de controle centralizado
- Navegação por módulos
- Estatísticas gerais
- Acesso rápido às principais funcionalidades
```

#### 2. **Gestão de Produtos**
```
Funcionalidades CRUD:
- Cadastro de novos produtos
- Upload de imagens
- Edição de informações (nome, preço, descrição)
- Associação com categorias
- Controle de disponibilidade
- Exclusão de produtos
```

#### 3. **Gestão de Categorias**
```
Operações:
- Criação de novas categorias
- Upload de imagens representativas
- Edição de nomes e descrições
- Exclusão de categorias (com verificação de dependências)
```

#### 4. **Gestão de Funcionários**
```
Controles:
- Cadastro de novos funcionários
- Definição de cargos e permissões
- Edição de informações pessoais
- Controle de acesso ao sistema
- Remoção de funcionários
```

#### 5. **Gestão de Pedidos**
```
Funcionalidades:
- Visualização de todos os pedidos
- Filtros por status e data
- Atualização de status em tempo real
- Visualização detalhada de pedidos
- Exclusão de pedidos (quando necessário)
```

#### 6. **Gestão de Usuários**
```
Recursos:
- Lista de todos os clientes
- Visualização de perfis
- Edição de informações (quando necessário)
- Histórico de pedidos por usuário
```

#### 7. **Sistema de Avaliações**
```
Monitoramento:
- Visualização de todas as avaliações
- Notas e comentários dos clientes
- Filtros por produto ou período
- Análise de satisfação
```

### Recursos Técnicos

#### 1. **Sistema de Upload de Imagens**
```php
Características:
- Validação de tipos de arquivo (JPG, PNG, GIF)
- Redimensionamento automático
- Armazenamento organizado por categorias
- Fallback para imagens padrão
- URLs dinâmicas do banco de dados
```

#### 2. **Segurança**
```php
Implementações:
- Hash de senhas com password_hash()
- Proteção contra SQL Injection (PDO prepared statements)
- Validação de sessões
- Sanitização de inputs
- Controle de acesso por roles
```

#### 3. **Sistema de Busca**
```javascript
Funcionalidades:
- Busca em tempo real
- Sugestões automáticas
- Busca por nome e descrição
- Resultados paginados
- Cache de resultados
```

#### 4. **Responsividade**
```css
Características:
- Design mobile-first
- Breakpoints para diferentes dispositivos
- Grid system flexível
- Imagens responsivas
- Navegação adaptativa
```

### Fluxo Completo de Uso

#### Cenário: Cliente realizando um pedido

1. **Acesso inicial**: Cliente acessa o sistema
2. **Autenticação**: Login ou cadastro no sistema
3. **Navegação**: Exploração do cardápio por categorias
4. **Seleção**: Escolha de produtos e adição ao carrinho
5. **Revisão**: Verificação do carrinho e ajustes
6. **Checkout**: Preenchimento de dados de entrega
7. **Confirmação**: Finalização do pedido
8. **Acompanhamento**: Monitoramento do status via histórico
9. **Avaliação**: Feedback após recebimento do pedido

#### Cenário: Administrador gerenciando o sistema

1. **Login administrativo**: Acesso ao painel admin
2. **Gestão de produtos**: Cadastro de novos itens do cardápio
3. **Processamento de pedidos**: Atualização de status dos pedidos
4. **Monitoramento**: Visualização de relatórios e avaliações
5. **Manutenção**: Gestão de funcionários e categorias

---

## Considerações Técnicas

### Arquitetura MVC Implementada

```
/src
├── /models          # Operações CRUD (Crud_*.php)
├── /views           # Interface do usuário (*.php)
├── /controllers     # Lógica de negócio (classes de entidade)
├── /components      # Componentes reutilizáveis
├── /helpers         # Funções auxiliares
├── /db             # Conexão com banco de dados
└── /Admin          # Área administrativa
```

### Padrões de Código

- **Nomenclatura consistente**: Seguindo convenções PHP
- **Documentação inline**: Comentários explicativos
- **Tratamento de erros**: Try-catch em operações críticas
- **Validação de dados**: Sanitização de inputs
- **Modularidade**: Código reutilizável e organizado

### Performance e Otimização

- **Consultas otimizadas**: Uso de índices e joins eficientes
- **Cache de sessão**: Armazenamento de dados frequentes
- **Lazy loading**: Carregamento sob demanda
- **Compressão de imagens**: Otimização automática

---

## Conclusão

O sistema **Écoute Saveur** representa uma solução completa para gestão de restaurantes, oferecendo:

- **Interface intuitiva** para clientes
- **Painel administrativo robusto** para gestão
- **Arquitetura escalável** e bem organizada
- **Segurança implementada** em múltiplas camadas
- **Experiência responsiva** em todos os dispositivos

O projeto demonstra aplicação prática de conceitos de engenharia de software, desenvolvimento web e experiência do usuário, resultando em um sistema funcional e profissional para o mercado de food service.