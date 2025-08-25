<?php 
$titulo = "inicio";
include_once "./components/_base-header.php";
?>
 <body>
    
 <main class="main-container">
    <header class="main-header">
      <h2 class="user-greeting">Olá, Mary!</h2>
      <input type="text" placeholder="O que você quer comer hoje...Kelvin? Eu também!!" class="search-input">
    </header>

    <section class="banner">
      <h1 class="banner-title">
        OUÇA O SABOR <br>
        VIVA O <span class="highlight">ENCANTO</span>
      </h1>
      <img src="assets/cardapio.png" alt="Banner Comida" class="banner-img">
    </section>

    <section class="categorias-secao">
      <div class="categoria-item">
        <img src="assets/bebidas.png" alt="Bebidas">
        <span>Bebidas</span>
      </div>
      <div class="categoria-item active">
        <img src="assets/bowls.png" alt="Bowls">
        <span>Bowls</span>
      </div>
      <div class="categoria-item">
        <img src="assets/lanches.png" alt="Lanches">
        <span>Lanches</span>
      </div>
      <div class="categoria-item">
        <img src="assets/sobremesa.png" alt="Sobremesa">
        <span>Sobremesa</span>
      </div>
      <div class="categoria-item">
        <img src="assets/carnes.png" alt="Carnes">
        <span>Carnes</span>
      </div>
    </section>

    <section class="mais-pedidos">
      <div class="card-pedido">
        <img src="img/salada.png" alt="Salada Caesar">
        <h3>Salada Caesar</h3>
        <p>R$ 9,99</p>
        <span>22Min | 4,47km</span>
        <button>+</button>
      </div>
      <div class="card-pedido">
        <img src="img/gaspacho.png" alt="Gaspacho Verde">
        <h3>Gaspacho Verde</h3>
        <p>R$ 9,99</p>
        <span>22Min | 4,47km</span>
        <button>+</button>
      </div>
      <div class="card-pedido">
        <img src="img/ramen.png" alt="Ramen Tori Tamago">
        <h3>Ramen Tori Tamago</h3>
        <p>R$ 9,99</p>
        <span>22Min | 4,47km</span>
        <button>+</button>
      </div>
    </section>
  </main>

  <aside class="pedido-lateral">
    <div class="endereco">
      <h4>Seu endereço</h4>
      <p>Rua Joaquim Manuel, 10</p>
      <button>Alterar</button>
    </div>

    <div class="resumo-pedido">
      <h4>Menu de Pedidos</h4>
      <ul>
        <li>Ramen Tori Tamago <span>+R$ 9,99</span></li>
        <li>Gaspacho Verde <span>+R$ 9,99</span></li>
      </ul>
      <p class="servicos">Serviços +R$ 1,00</p>
      <p class="total">TOTAL R$ 20,98</p>
      <button class="cupom">Deseja utilizar um cupom?</button>
      <button class="pagamento">Escolher método de pagamento</button>
    </div>
</body>

<?php 
include_once "./components/_base-footer.php";
?>

