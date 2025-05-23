<?php
function time_ago($datetime, $full = false) {
    // Define o fuso horário, caso seja necessário
    date_default_timezone_set('America/Sao_Paulo');
    
    // Configura o local para português do Brasil
    setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil.1252');

    // Calcula a diferença entre a data atual e a data passada
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Vetores de rótulos de tempo
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'ano',
        'm' => 'mês',
        'w' => 'semana',
        'd' => 'dia',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    ];

    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);

    // Se a data for hoje, retorna o tempo aproximado
    if ($diff->days == 0) {
        return $string ? 'há ' . implode(', ', $string) : 'agora';
    }

    // Se for no último mês, retorna o tempo aproximado
    if ($diff->m == 0 && $diff->y == 0) {
        return 'há ' . implode(', ', $string);
    }

    // Se tiver mais de um mês, retorna o mês e o dia em português
    if ($diff->y == 0) {
        return strftime('%d de %B', strtotime($datetime));
    }

    // Se for de um ano diferente, retorna a data completa
    return strftime('%d/%m/%Y %H:%M', strtotime($datetime));
}
?>
