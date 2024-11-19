<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração do Problema Simplex</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 content-wrapper">
    <h1 class="text-center">Configuração do Problema Simplex</h1>
    <?php
        if (isset($_GET['variaveis']) && isset($_GET['restricoes']) && isset($_GET['metodo'])) {
            $num_variaveis = (int) $_GET['variaveis'];
            $num_restricoes = (int) $_GET['restricoes'];
            $metodo = $_GET['metodo'];
        } else {
            echo "<div class='alert alert-danger'>Dados insuficientes. Volte para a página anterior e preencha o formulário corretamente.</div>";
            exit;
        }

        echo "<p><strong>Método selecionado:</strong> " . ucfirst($metodo) . "</p>";
        echo "<p><strong>Número de Variáveis:</strong> $num_variaveis</p>";
        echo "<p><strong>Número de Restrições:</strong> $num_restricoes</p>";
        ?>
        <form action="processa_simplex.php" method="post" class="mt-4">
            <h2>Função Objetivo</h2>
            <p>Insira os coeficientes da função objetivo:</p>
            <?php
            for ($i = 1; $i <= $num_variaveis; $i++) {
                echo "<div class='form-group'>";
                echo "<label for='x$i'>Coeficiente de X$i:</label>";
                echo "<input type='number' id='x$i' name='objetivo[]' class='form-control' step='any' required>";
                echo "</div>";
            }
            ?>
            <h2>Restrições</h2>
            <p>Insira os coeficientes das variáveis para cada restrição e o valor à direita (lado direito da equação):
            </p>
            <?php
            for ($j = 1; $j <= $num_restricoes; $j++) {
                echo "<h3>Restrição $j</h3>";
                for ($i = 1; $i <= $num_variaveis; $i++) {
                    echo "<div class='form-group'>";
                    echo "<label for='r{$j}_x$i'>Coeficiente de X$i:</label>";
                    echo "<input type='number' id='r{$j}_x$i' name='restricoes[$j][]' class='form-control' step='any' required>";
                    echo "</div>";
                }
                echo "<div class='form-group'>";
                echo "<label for='r{$j}_valor'>Valor da restrição:</label>";
                echo "<input type='number' id='r{$j}_valor' name='valores_restricoes[$j]' class='form-control' step='any' required>";
                echo "</div>";
            }
            ?>
            <input type="hidden" name="num_variaveis" value="<?php echo $num_variaveis; ?>">
            <input type="hidden" name="num_restricoes" value="<?php echo $num_restricoes; ?>">
            <input type="hidden" name="metodo" value="<?php echo $metodo; ?>">
            <button type="submit" class="btn btn-primary btn-block">Calcular Simplex</button>
        </form>
    </div>
</body>

</html>
