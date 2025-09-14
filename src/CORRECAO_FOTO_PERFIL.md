# 🔧 CORREÇÃO: Foto de Perfil no Cardápio

## ❌ **Problema Identificado:**
A foto de perfil não estava mudando na página de cardápio

## 🔍 **Causa Raiz:**
1. **Imagem fixa**: Página usava `./images/usuário.jpeg` em vez da sessão
2. **Sessão não carregada**: Para usuários já logados, a variável `$_SESSION['foto_perfil']` não estava definida

## ✅ **Correções Aplicadas:**

### 1. **Corrigida exibição da foto no cardápio:**
```php
// ANTES:
<img src="./images/usuário.jpeg" alt="Usuário">

// DEPOIS:
<img src="<?= !empty($_SESSION['foto_perfil']) ? './images/usuarios/' . $_SESSION['foto_perfil'] : './images/usuário.jpeg' ?>" alt="Usuário">
```

### 2. **Adicionado carregamento automático da foto nas páginas:**
```php
// Carregar foto de perfil na sessão se não estiver definida
if (!isset($_SESSION['foto_perfil'])) {
    try {
        require_once './controllers/usuario/Crud_usuario.php';
        $crudUsuario = new Crud_usuario();
        $crudUsuario->setId_usuario($_SESSION["id"]);
        $dadosUsuario = $crudUsuario->read();
        if ($dadosUsuario && isset($dadosUsuario['imagem_perfil'])) {
            $_SESSION['foto_perfil'] = $dadosUsuario['imagem_perfil'];
        }
    } catch (Exception $e) {
        // Silenciar erro para não quebrar a página
    }
}
```

## 📄 **Páginas Corrigidas:**
- ✅ `cardapio.php` - Foto dinâmica + carregamento automático
- ✅ `index.php` - Carregamento automático da foto
- ✅ `favoritos.php` - Carregamento automático da foto

## 📄 **Páginas Já Corretas:**
- ✅ `configuracoes.php` - Já usava foto dinâmica
- ✅ Outras páginas não possuem avatar no cabeçalho

## 🎯 **Resultado:**
- ✅ **Foto de perfil aparece corretamente** em todas as páginas
- ✅ **Usuários já logados** terão a foto carregada automaticamente
- ✅ **Novos logins** já recebem a foto na sessão
- ✅ **Fallback funcionando** para usuários sem foto

## 🧪 **Como Testar:**
1. Faça upload de uma nova foto na página de configurações
2. Navegue para o cardápio
3. A foto deve aparecer no avatar do cabeçalho
4. Teste em outras páginas (index, favoritos)

---
**Correção aplicada em:** 14 de setembro de 2025