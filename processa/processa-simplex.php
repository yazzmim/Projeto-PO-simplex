<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado Simplex</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Resultado do Método Simplex</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['num_variaveis'], $_POST['num_restricoes'], $_POST['objetivo'], $_POST['restricoes'], $_POST['valores_restricoes'])) {
            $num_variaveis = (int) $_POST['num_variaveis'];
            $num_restricoes = (int) $_POST['num_restricoes'];
            $objetivo = $_POST['objetivo'];
            $restricoes = $_POST['restricoes'];
            $valores_restricoes = $_POST['valores_restricoes'];

            echo "<h2>Função Objetivo</h2>";
            echo "<p>Z = ";
            for ($i = 0; $i < $num_variaveis; $i++) {
                echo ($i > 0 ? " + " : "") . htmlspecialchars($objetivo[$i]) . "X" . ($i + 1);
            }
            echo "</p>";

            echo "<h2>Restrições</h2>";
            for ($j = 1; $j <= $num_restricoes; $j++) {
                echo "<p>Restrição $j: ";
                for ($i = 0; $i < $num_variaveis; $i++) {
                    echo ($i > 0 ? " + " : "") . htmlspecialchars($restricoes[$j][$i]) . "X" . ($i + 1);
                }
                echo " <= " . htmlspecialchars($valores_restricoes[$j]) . "</p>";
            }

            // Inicializando variáveis básicas
            $variaveisBase = ["z"];
            for ($j = 1; $j <= $num_restricoes; $j++) {
                $variaveisBase[] = "SX" . ($num_variaveis + $j);
            }

            // Construindo a tabela inicial Simplex
            $tabelaSimplex = [];

            // Linha Z (função objetivo)
            $tabelaSimplex[0] = array_merge(
                array_map(function ($c) {
                    return -$c; }, $objetivo),
                array_fill(0, $num_restricoes, 0), // Colunas de folga
                [0] // Coluna "Solution" da função objetivo
            );

            // Restrições
            for ($j = 1; $j <= $num_restricoes; $j++) {
                $linhaRestricao = array_merge(
                    $restricoes[$j],
                    array_fill(0, $num_restricoes, 0) // Inicializa as colunas de folga com zeros
                );
                $linhaRestricao[$num_variaveis + $j - 1] = 1; // Marca a variável de folga correta
                $linhaRestricao[] = $valores_restricoes[$j]; // Adiciona valor da solução
                $tabelaSimplex[$j] = $linhaRestricao;
            }
            $tabelaSimplex = [];

            // Linha Z (função objetivo)
            $tabelaSimplex[0] = array_merge(
                array_map(function ($c) {
                    return -$c;
                }, $objetivo),
                array_fill(0, $num_restricoes, 0), // Colunas de folga
                [0] // Coluna "Solution" da função objetivo
            );

            // Restrições
            for ($j = 1; $j <= $num_restricoes; $j++) {
                $linhaRestricao = array_merge(
                    $restricoes[$j],
                    array_fill(0, $num_restricoes, 0) // Inicializa as colunas de folga com zeros
                );
                $linhaRestricao[$num_variaveis + $j - 1] = 1; // Marca a variável de folga correta
                $linhaRestricao[] = $valores_restricoes[$j]; // Adiciona valor da solução
                $tabelaSimplex[$j] = $linhaRestricao;
            }

            // Função para exibir a tabela Simplex
            function construirTabelaSimplex($variaveisBase, $tabela, $numVariaveis, $numRestricoes, $linhaPivot = null, $colunaPivot = null)
            {
                echo "<table class='table table-bordered mt-4'>";
                echo "<thead>";
                echo "<tr><th>Basic</th>";

                // Cabeçalhos das variáveis de decisão
                for ($i = 1; $i <= $numVariaveis; $i++) {
                    echo "<th>X$i</th>";
                }

                // Cabeçalhos das variáveis de folga (consistentes com a inicialização)
                for ($i = 1; $i <= $numRestricoes; $i++) {
                    echo "<th>SX" . ($numVariaveis + $i) . "</th>";
                }

                echo "<th>Solution</th>";
                echo "</tr></thead><tbody>";

                // Exibição das linhas
                foreach ($tabela as $index => $linha) {
                    $rowClass = ($index === $linhaPivot) ? "class='table-warning'" : "";
                    echo "<tr $rowClass>";
                    echo "<td>" . htmlspecialchars($variaveisBase[$index]) . "</td>";

                    foreach ($linha as $j => $valor) {
                        $cellClass = ($j === $colunaPivot) ? "class='table-info'" : "";
                        echo "<td $cellClass>" . number_format($valor, 2) . "</td>";
                    }

                    echo "</tr>";
                }

                echo "</tbody></table>";
            }

            // Funções para operar o Simplex
            function pivot(&$tabela, $linhaPivot, $colunaPivot)
            {
                $pivo = $tabela[$linhaPivot][$colunaPivot];
                foreach ($tabela[$linhaPivot] as $j => &$valor) {
                    $valor /= $pivo;
                }

                foreach ($tabela as $i => &$linha) {
                    if ($i !== $linhaPivot) {
                        $coef = $linha[$colunaPivot];
                        foreach ($linha as $j => &$valor) {
                            $valor -= $coef * $tabela[$linhaPivot][$j];
                        }
                    }
                }
            }

            function escolherColunaPivot($tabela)
            {
                $ultimaLinha = $tabela[0];
                $colunaPivot = null;
                foreach ($ultimaLinha as $index => $valor) {
                    if ($valor < 0 && (is_null($colunaPivot) || $valor < $ultimaLinha[$colunaPivot])) {
                        $colunaPivot = $index;
                    }
                }
                return $colunaPivot;
            }

            function escolherLinhaPivot($tabela, $colunaPivot)
            {
                $linhaPivot = null;
                $menorRazao = PHP_INT_MAX;

                for ($i = 1; $i < count($tabela); $i++) {
                    $solucao = end($tabela[$i]);
                    $coeficiente = $tabela[$i][$colunaPivot];
                    if ($coeficiente > 0) {
                        $razao = $solucao / $coeficiente;
                        if ($razao < $menorRazao) {
                            $menorRazao = $razao;
                            $linhaPivot = $i;
                        }
                    }
                }
                return $linhaPivot;
            }

            function simplex(&$tabela, &$variaveisBase, $numVariaveis, $numRestricoes)
            {
                $iteracoes = [];
                $iteracao = 0;

                while (($colunaPivot = escolherColunaPivot($tabela)) !== null) {
                    $linhaPivot = escolherLinhaPivot($tabela, $colunaPivot);
                    if (is_null($linhaPivot)) {
                        echo "<p>Solução ilimitada detectada.</p>";
                        return;
                    }

                    // Atualize a variável básica para a linha pivot
                    $novaVariaveisBase = $variaveisBase;
                    $novaVariaveisBase[$linhaPivot] = ($colunaPivot < $numVariaveis)
                        ? "X" . ($colunaPivot + 1)  // Variável de decisão
                        : "SX" . ($colunaPivot - $numVariaveis + 1); // Variável de folga consistente
        
                    // Registrar o estado atual da tabela e variáveis básicas
                    $iteracoes[] = [
                        'iteracao' => $iteracao,
                        'tabela' => array_map('array_values', $tabela), // Copia a tabela para manter o estado
                        'variaveisBase' => $novaVariaveisBase, // Mantém o estado das variáveis básicas
                        'linhaPivot' => $linhaPivot,
                        'colunaPivot' => $colunaPivot
                    ];

                    pivot($tabela, $linhaPivot, $colunaPivot);
                    $variaveisBase = $novaVariaveisBase; // Atualiza para a próxima iteração
                    $iteracao++;
                }

                // Registrar a última iteração final
                $iteracoes[] = [
                    'iteracao' => $iteracao,
                    'tabela' => array_map('array_values', $tabela),
                    'variaveisBase' => $variaveisBase,
                    'linhaPivot' => null,
                    'colunaPivot' => null
                ];

                foreach ($iteracoes as $data) {
                    echo "<h3>Iteração " . ($data['iteracao'] + 1) . "</h3>";
                    construirTabelaSimplex(
                        $data['variaveisBase'],  // Use as variáveis básicas daquela iteração
                        $data['tabela'],
                        $numVariaveis,
                        $numRestricoes,
                        $data['linhaPivot'],
                        $data['colunaPivot']
                    );
                }

                echo "<p>Solução ótima alcançada.</p>";
            }

            echo "<h3>Tabela Inicial</h3>";
            construirTabelaSimplex($variaveisBase, $tabelaSimplex, $num_variaveis, $num_restricoes);
            simplex($tabelaSimplex, $variaveisBase, $num_variaveis, $num_restricoes);
        } else {
            echo "<p>Erro: Dados inválidos ou não enviados.</p>";
        }
        ?>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
