<?php
// Verificando se o formulário foi enviado corretamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperando os dados do formulário
    $metodo = $_POST['metodo'];
    $num_variaveis = $_POST['variaveis'];
    $num_restricoes = $_POST['restricoes'];

    // Validando se os valores são positivos
    if ($num_variaveis > 0 && $num_restricoes > 0) {
        // Redirecionar para a página de configuração
        header("Location: configuracao.php?variaveis=$num_variaveis&restricoes=$num_restricoes&metodo=$metodo");
        exit; //exit para garantir que o script seja interrompido após o redirecionamento
    } else {
        echo "Valores inválidos. Por favor, insira números positivos.";
    }
} else {
    // Caso a página seja acessada sem formulário
    echo "Por favor, preencha o formulário corretamente.";
}
?>
