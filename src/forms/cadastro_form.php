<?php 
require_once "../models/Crud_usuario.php";

if(isset($_POST["cadastrar"])){
    if(empty($_POST["primeiro_nome"]) || empty($_POST["segundo_nome"]) || empty($_POST["email"]) || empty($_POST["senha"]) || empty($_POST["telefone"])) {
        echo "<script>alert('Todos os campos são obrigatórios!');</script>";
    } else {
        $usuario = new Crud_usuario();
        $primeiro_nome = $_POST["primeiro_nome"];
        $segundo_nome = $_POST["segundo_nome"];
        $email = $_POST["email"];
        $senha = md5($_POST["senha"]);
        $telefone = $_POST["telefone"];

        try {
            $usuario->setPrimeiro_nome($primeiro_nome);
            $usuario->setSegundo_nome($segundo_nome);
            $usuario->setEmail($email);
            $usuario->setSenha($senha);
            $usuario->setTelefone($telefone);
            $usuario->setData_criacao();
            $usuario->setData_atualizacao();
            $usuario->setImagem_perfil(NULL);
            $usuario->create();
        } catch (Exception $e) {
            echo "<script>alert('Erro no cadastro: " . addslashes($e->getMessage()) . "');</script>";
            header ("Location: ../cadastro.php");
        }
    }
}
