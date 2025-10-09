<?php
// Ficheiro de Teste de Diagnóstico para api_agenda.php

header('Content-Type: application/json');

// Se este ficheiro for executado, o erro 500 irá desaparecer
// e o calendário irá simplesmente aparecer vazio.
// Isto irá provar que o ficheiro foi atualizado com sucesso.

echo json_encode([]); // Devolve uma lista de eventos vazia
?>
