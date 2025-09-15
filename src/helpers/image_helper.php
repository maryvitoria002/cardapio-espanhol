<?php
/**
 * Função auxiliar para tratar imagens
 * Detecta automaticamente se é uma URL completa ou um arquivo local
 */

function getImageSrc($imagePath) {
    // Se for uma URL completa (começando com http:// ou https://)
    if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
        return $imagePath;
    }
    
    // Se for um arquivo local, adicionar o prefixo do diretório
    return "./images/comidas/" . $imagePath;
}

function imageExists($imagePath) {
    // Se for uma URL, assumir que existe (ou você pode fazer uma verificação mais robusta)
    if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
        return true;
    }
    
    // Se for um arquivo local, verificar se existe
    return file_exists("./images/comidas/" . $imagePath);
}

function getImageSrcAdmin($imagePath) {
    // Se for uma URL completa (começando com http:// ou https://)
    if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
        return $imagePath;
    }
    
    // Se for um arquivo local, adicionar o prefixo do diretório (relativo ao admin)
    return "../../images/comidas/" . $imagePath;
}

function imageExistsAdmin($imagePath) {
    // Se for uma URL, assumir que existe
    if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
        return true;
    }
    
    // Se for um arquivo local, verificar se existe
    return file_exists("../../images/comidas/" . $imagePath);
}
?>