# RestauranteSIS - Sistema de Administração

Sistema de administração completo inspirado no Django Admin para gerenciamento do RestauranteSIS.

## 📋 Funcionalidades

### 🔐 Autenticação e Autorização
- Sistema de login seguro para funcionários
- Controle de acesso baseado em níveis (Superadmin, Admin, Entregador, Esperante)
- Sessões seguras e logout automático

### 📊 Dashboard
- Visão geral com estatísticas em tempo real
- Cards com totais de usuários, produtos, pedidos e avaliações
- Lista dos pedidos mais recentes
- Interface responsiva e intuitiva

### 👥 Gestão de Usuários
- Listar, criar, editar e excluir usuários
- Upload de fotos de perfil
- Busca e filtros avançados
- Paginação automática

### 👨‍💼 Gestão de Funcionários
- Controle completo da equipe
- Níveis de acesso hierárquicos
- Proteção contra auto-exclusão
- Somente Superadmin pode gerenciar funcionários

### 🏷️ Gestão de Categorias
- CRUD completo de categorias
- Proteção contra exclusão de categorias com produtos
- Interface inline para criação/edição rápida
- Contador de produtos por categoria

### 📦 Gestão de Produtos
- CRUD completo com upload de imagens
- Filtros por categoria e status
- Controle de estoque visual
- Marcação de produtos populares
- Busca por nome e descrição

### 🛒 Gestão de Pedidos
- Visualização completa dos pedidos
- Atualização de status em tempo real
- Filtros por status e busca
- Cálculo automático de totais
- Função de impressão de pedidos

### ⭐ Gestão de Avaliações
- Moderação de avaliações de clientes
- Sistema de aprovação/rejeição
- Respostas da empresa
- Filtros por nota e status
- Interface de cards para melhor visualização

## 🎨 Design

### Características do Design
- **Inspirado no Django Admin**: Interface limpa e profissional
- **Responsivo**: Funciona perfeitamente em desktop, tablet e mobile
- **Tema Consistente**: Cores e tipografia alinhadas com o sistema principal
- **Navegação Intuitiva**: Sidebar com menu hierárquico
- **Feedback Visual**: Alertas, badges e estados visuais claros

### Cores do Sistema
- **Azul Primário**: #1155CC (ações principais)
- **Amarelo Secundário**: #FFC400 (destaques)
- **Verde Sucesso**: #22C55E
- **Vermelho Erro**: #EF4444
- **Laranja Aviso**: #F59E0B

## 🗂️ Estrutura de Arquivos

```
Admin/
├── index.php                 # Dashboard principal
├── login.php                 # Página de login
├── logout.php                # Script de logout
├── css/
│   ├── admin.css             # Estilos principais
│   └── login.css             # Estilos do login
├── js/
│   └── admin.js              # JavaScript principal
├── includes/
│   ├── sidebar.php           # Menu lateral
│   └── header.php            # Cabeçalho
├── usuarios/
│   ├── index.php             # Lista de usuários
│   ├── create.php            # Criar usuário
│   ├── edit.php              # Editar usuário
│   ├── view.php              # Visualizar usuário
│   └── css/crud.css          # Estilos CRUD
├── funcionarios/
│   └── index.php             # Gestão de funcionários
├── categorias/
│   └── index.php             # Gestão de categorias
├── produtos/
│   └── index.php             # Gestão de produtos
├── pedidos/
│   └── index.php             # Gestão de pedidos
└── avaliacoes/
    └── index.php             # Gestão de avaliações
```

## 🚀 Instalação e Configuração

### 1. Configuração do Banco de Dados
Execute o script SQL fornecido para criar as tabelas necessárias:

```sql
-- Execute o arquivo src/db/script.sql
```

### 2. Configuração Inicial
1. Certifique-se que o arquivo `src/db/conection.php` está configurado corretamente
2. Verifique as permissões de upload nas pastas de imagens
3. Configure um usuário administrador inicial no banco

### 3. Primeiro Acesso
- **URL**: `http://seudominio.com/src/Admin/`
- **Login**: Use as credenciais do funcionário cadastrado no banco
- **Teste**: maryvitoria054@gmail.com / 123

## 🔧 Funcionalidades Técnicas

### Segurança
- ✅ Proteção contra SQL Injection (PDO com prepared statements)
- ✅ Validação de sessões
- ✅ Controle de acesso por níveis
- ✅ Proteção contra upload de arquivos maliciosos
- ✅ Escape de dados de saída (XSS protection)

### Performance
- ✅ Paginação automática
- ✅ Busca otimizada com LIKE
- ✅ Carregamento lazy de imagens
- ✅ CSS e JS minificados
- ✅ Queries otimizadas com JOINs

### Usabilidade
- ✅ Interface responsiva
- ✅ Feedback visual em tempo real
- ✅ Busca em tempo real
- ✅ Filtros múltiplos
- ✅ Confirmações de ações destrutivas
- ✅ Breadcrumbs e navegação clara

## 📱 Responsividade

O sistema é totalmente responsivo com breakpoints:
- **Desktop**: > 1024px
- **Tablet**: 768px - 1024px  
- **Mobile**: < 768px

### Adaptações Mobile
- Menu lateral retrátil
- Tabelas com scroll horizontal
- Formulários em coluna única
- Botões e campos otimizados para toque

## 🔄 Estados e Feedback

### Status de Pedidos
- **Pendente**: Amarelo
- **Processando**: Azul
- **A caminho**: Roxo
- **Concluído**: Verde
- **Cancelado**: Vermelho

### Status de Avaliações
- **Pendente**: Aguardando moderação
- **Aprovado**: Visível publicamente
- **Rejeitado**: Rejeitado pela moderação

### Níveis de Acesso
- **Superadmin**: Acesso total (vermelho)
- **Admin**: Gestão geral (roxo)
- **Entregador**: Foco em entregas (verde)
- **Esperante**: Acesso limitado (laranja)

## 🤝 Contribuição

Para contribuir com o sistema:

1. Mantenha o padrão de código estabelecido
2. Use comentários em português
3. Siga a estrutura de arquivos existente
4. Teste todas as funcionalidades antes de commitar
5. Mantenha a consistência visual e UX

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o sistema de administração, entre em contato com a equipe de desenvolvimento.

---

**RestauranteSIS Admin** - Sistema de gestão completo para restaurantes 🍽️
