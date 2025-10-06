<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Formulário de Debug</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Teste de Carga de Formulário</h1>
        <p class="text-gray-600 mb-6">Este formulário testa quantos campos POST o servidor aceita antes de retornar um erro 'Forbidden'. Comece com um número baixo (como 5, similar ao form de livros) e aumente até o erro ocorrer.</p>
        
        <form action="debug_receiver.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="num_campos" class="block text-gray-700 font-medium mb-2">Quantos campos de texto de teste deseja enviar?</label>
                <input type="number" id="num_campos" name="num_campos" value="5" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Enviar também um ficheiro de teste?</label>
                <input type="file" name="ficheiro_teste" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
            </div>

            <div id="campos-container" class="mb-6">
                </div>

            <button type="submit" class="w-full bg-teal-600 text-white font-bold py-3 px-4 rounded-md hover:bg-teal-700">
                Enviar Teste
            </button>
        </form>
    </div>

    <script>
        const numCamposInput = document.getElementById('num_campos');
        const container = document.getElementById('campos-container');

        function gerarCampos() {
            const num = parseInt(numCamposInput.value, 10);
            container.innerHTML = ''; // Limpa os campos existentes
            for (let i = 1; i <= num; i++) {
                const input = document.createElement('input');
                input.type = 'hidden'; // Oculto para não poluir a interface
                input.name = 'campo_teste_' + i;
                input.value = 'valor de teste ' + i;
                container.appendChild(input);
            }
        }

        numCamposInput.addEventListener('input', gerarCampos);
        
        // Gera os campos iniciais
        gerarCampos();
    </script>
</body>
</html>
