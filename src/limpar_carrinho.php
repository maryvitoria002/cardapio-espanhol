<?php
session_start();

// Limpar completamente o carrinho
$_SESSION['carrinho'] = [];

echo "Carrinho limpo com sucesso!";
echo "<br><a href='carrinho.php'>Voltar ao carrinho</a>";
echo "<br><a href='cardapio.php'>Ir ao cardápio</a>";
?>
