<?php

function idadeSerie($turmaIdade, $idade) {
    if ($turmaIdade == 99) { // EJA stages have no expected age
        return 0;
    }
    if ($idade == "-" || $idade < 0) { // Handle invalid ages
        return 0;
    }
    $diferenca = $idade - $turmaIdade;
    return $diferenca;
}

?>