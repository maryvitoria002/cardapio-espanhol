# CHANGELOG - Sistema de Restaurante

## [1.1.0] - 2025-09-14

### ✅ Adicionado
- **Nova Tabela `favoritos`**: Sistema completo de favoritos para usuários
  - Campo `id_favorito` (PRIMARY KEY, AUTO_INCREMENT)
  - Campo `id_usuario` (FOREIGN KEY para `usuario.id_usuario`)
  - Campo `id_produto` (FOREIGN KEY para `produto.id_produto`) 
  - Campo `data_criacao` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
  - Chave única `(id_usuario, id_produto)` para evitar duplicatas
  - Foreign keys com CASCADE para manter integridade

- **Funcionalidades de Favoritos**:
  - Classe `Favorito.php` - Model para favoritos
  - Classe `Crud_favorito.php` - Operações CRUD para favoritos
  - Página `favoritos.php` - Interface completa de favoritos
  - Arquivo `process_favorito.php` - Processamento AJAX
  - CSS `favoritos.css` - Estilos para página de favoritos

- **Sistema de Upload de Foto de Perfil**:
  - Arquivo `upload_foto_perfil.php` - Processamento de upload
  - Validação de tipos de arquivo (JPG, PNG, GIF)
  - Validação de tamanho (máximo 5MB)
  - Atualização automática em todas as páginas

### 🔄 Modificado
- **index.php**: 
  - Botão de estrela agora leva para página de favoritos
  - Correção na exibição do nome do usuário
  - Atualização da foto de perfil dinâmica

- **configuracoes.php**: 
  - Interface de upload de foto implementada
  - Seção de favoritos redirecionando para página completa
  - JavaScript para upload via AJAX

- **process_login.php**: 
  - Inclusão do campo `imagem_perfil` na sessão
  - Correção da query para tabela `usuario`

- **Crud_usuario.php**: 
  - Método `update()` atualizado para incluir `imagem_perfil`
  - Suporte condicional para atualização de foto

### 🗄️ Banco de Dados
- **Criado**: Arquivo `database_schema.sql` completo com todas as tabelas
- **Criado**: Scripts de migração em `/src/db/`
- **Adicionado**: Tabela `favoritos` com relacionamentos adequados

### 📁 Novos Arquivos
```
src/
├── controllers/favorito/
│   ├── Favorito.php
│   └── Crud_favorito.php
├── db/
│   ├── setup_database.php
│   ├── check_structure.php
│   ├── create_favorites_table.sql
│   └── add_foto_perfil_column.sql
├── styles/
│   └── favoritos.css
├── favoritos.php
├── process_favorito.php
└── upload_foto_perfil.php

database_schema.sql (raiz do projeto)
INSTRUCOES_IMPLEMENTACAO.md
```

### 🔧 Correções
- ❌ **Corrigido**: Nome do usuário não aparecia no index.php
- ❌ **Corrigido**: Referências incorretas para tabela de usuários
- ❌ **Corrigido**: Caminhos de imagem de perfil não funcionavam

### 🎯 Funcionalidades Implementadas
1. ✅ **Sistema de Favoritos Completo**
   - Adicionar/remover produtos dos favoritos
   - Página dedicada com interface moderna
   - Integração com carrinho de compras
   - Contadores e notificações

2. ✅ **Upload de Foto de Perfil**
   - Interface intuitiva na página de configurações
   - Validação completa de arquivos
   - Atualização em tempo real
   - Sincronização entre páginas

3. ✅ **Melhorias na UX**
   - Notificações visuais
   - Interface responsiva
   - Navegação intuitiva
   - Feedback imediato

### 📋 Próximas Versões (Sugestões)
- [ ] Implementar botões de favorito em outras páginas
- [ ] Adicionar contador de favoritos no ícone
- [ ] Sistema de categorias de favoritos
- [ ] Redimensionamento automático de imagens
- [ ] Sistema de notificações avançado
- [ ] Migração de senhas MD5 para password_hash()

---

## [1.0.0] - Anterior
- Sistema base do restaurante
- Tabelas: usuario, categoria, produto, carrinho, pedido, produto_pedido, avaliacao, funcionario
- Sistema de login/cadastro
- Carrinho de compras
- Sistema de pedidos
- Painel administrativo