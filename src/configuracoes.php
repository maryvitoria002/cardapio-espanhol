<?php 
$titulo = "configuracoes";
include_once "./components/_base-header.php";
require_once "./controllers/usuario/Crud_usuario.php";

if (!isset($_SESSION['id'])) {
    header("Location: ./login.php");
    exit();
}

$usuario = new Crud_usuario();
$usuario->setId_usuario($_SESSION['id']);
$dadosUsuario = $usuario->read();
?>

<div class="config-container">
    <div class="retangulo_branco">
        <div class="fotoperfil">
            <img src="./assets/perfil.png" alt="foto de perfil">
            <button class="btn-change-photo">
                <i class="fas fa-camera"></i>
            </button>
        </div>
        <div class="nomeperfil">
           <div class="nome">
               <h2><?= htmlspecialchars($dadosUsuario['primeiro_nome'] . ' ' . $dadosUsuario['segundo_nome']) ?></h2>
           </div>
           <div class="cargo">Cliente</div>
        </div>

        <nav class="menu-config">
            <a href="#" class="menu-item active" data-section="informacoes">
                <img src="./assets/avatar.png" alt="">
                <span>Informações Pessoais</span>
            </a>

            <a href="#" class="menu-item" data-section="seguranca">
                <img src="./assets/cadeado.png" alt="">
                <span>Login e Senha</span>
            </a>

            <a href="#" class="menu-item" data-section="favoritos">
                <img src="./assets/estrela.png" alt="">
                <span>Itens Favoritos</span>
            </a>
        </nav>
    </div>

    <div class="retangulo_branco2">
        <!-- Seção pra atualizar as informações Pessoais -->
        <div class="config-section active" id="informacoes">
            <h1>Informações Pessoais</h1>
            <form class="form-config">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="primeiro_nome" value="<?= htmlspecialchars($dadosUsuario['primeiro_nome']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Sobrenome</label>
                        <input type="text" name="segundo_nome" value="<?= htmlspecialchars($dadosUsuario['segundo_nome']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?= htmlspecialchars($dadosUsuario['email']) ?>" readonly>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="tel" name="telefone" value="<?= htmlspecialchars($dadosUsuario['telefone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Data de Nascimento</label>
                        <input type="date" name="data_nascimento" value="<?= htmlspecialchars($dadosUsuario['data_nascimento'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Endereço</label>
                    <input type="text" name="endereco" value="<?= htmlspecialchars($dadosUsuario['endereco'] ?? '') ?>" placeholder="Ex: Rua dos caba la, 123">
                </div>

                <button type="submit" class="btn-save">Salvar Alterações</button>
            </form>
        </div>

        <!-- Seção de Segurança -->
        <div class="config-section" id="seguranca">
            <h1>Login e Senha</h1>
            <form class="form-config">
                <div class="form-group">
                    <label>Senha Atual</label>
                    <input type="password">
                </div>
                <div class="form-group">
                    <label>Nova Senha</label>
                    <input type="password">
                </div>
                <div class="form-group">
                    <label>Confirmar Nova Senha</label>
                    <input type="password">
                </div>
                <button type="submit" class="btn-save">Alterar Senha</button>
            </form>
        </div>

        <!-- Seção de Favoritos -->
        <div class="config-section" id="favoritos">
            <h1>Itens Favoritos</h1>
            <div class="favorites-grid">
                <!-- Aqui você pode adicionar uma grid de itens favoritos -->
                <p>Você ainda não tem itens favoritos.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Remove active class from all items
        document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.config-section').forEach(s => s.classList.remove('active'));
        
        // Add active class to clicked item
        item.classList.add('active');
        
        // Show corresponding section
        const section = item.dataset.section;
        document.getElementById(section).classList.add('active');
    });
});
</script>

<?php 
include_once "./components/_base-footer.php";
?>

