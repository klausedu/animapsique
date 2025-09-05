<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$page_title = 'Criar Novo Quiz';
require_once 'templates/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <form action="processa_quiz.php" method="POST" id="quizForm">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Detalhes do Quiz</h2>
            
            <!-- Título e Descrição -->
            <div class="space-y-4 mb-8">
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título do Quiz</label>
                    <input type="text" name="titulo" id="titulo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="descricao" id="descricao" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mb-4">Perguntas</h2>
            
            <!-- Container para as Perguntas -->
            <div id="perguntasContainer" class="space-y-6">
                <!-- As perguntas serão adicionadas aqui via JS -->
            </div>

            <!-- Botões de Ação -->
            <div class="mt-6 border-t pt-6 flex items-center justify-between">
                <button type="button" id="addPerguntaBtn" class="rounded-md bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-100">
                    + Adicionar Pergunta
                </button>
                <button type="submit" class="rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">
                    Salvar Quiz
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const perguntasContainer = document.getElementById('perguntasContainer');
    const addPerguntaBtn = document.getElementById('addPerguntaBtn');
    let perguntaIndex = 0;

    addPerguntaBtn.addEventListener('click', function() {
        const perguntaId = `pergunta_${perguntaIndex}`;
        const perguntaHTML = `
            <div class="border p-4 rounded-md bg-gray-50" id="${perguntaId}">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-semibold text-gray-700">Pergunta ${perguntaIndex + 1}</h4>
                    <button type="button" class="text-sm font-medium text-red-600 hover:text-red-800" onclick="removerElemento('${perguntaId}')">Remover Pergunta</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Texto da Pergunta</label>
                        <input type="text" name="perguntas[${perguntaIndex}][texto]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de Resposta</label>
                        <select name="perguntas[${perguntaIndex}][tipo]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm tipo-pergunta" data-index="${perguntaIndex}">
                            <option value="texto">Texto (Dissertativa)</option>
                            <option value="unica_escolha">Única Escolha</option>
                            <option value="multipla_escolha">Múltipla Escolha</option>
                        </select>
                    </div>
                </div>

                <div id="opcoesContainer_${perguntaIndex}" class="mt-4 space-y-2 hidden">
                    <label class="block text-sm font-medium text-gray-700">Opções de Resposta</label>
                    <!-- Opções serão adicionadas aqui -->
                    <button type="button" class="text-sm font-medium text-teal-600 hover:text-teal-800" onclick="addOpcao(${perguntaIndex})">+ Adicionar Opção</button>
                </div>
            </div>
        `;
        perguntasContainer.insertAdjacentHTML('beforeend', perguntaHTML);
        perguntaIndex++;
    });

    // Delegação de evento para lidar com a mudança no tipo de pergunta
    perguntasContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('tipo-pergunta')) {
            const index = e.target.dataset.index;
            const opcoesContainer = document.getElementById(`opcoesContainer_${index}`);
            if (e.target.value === 'unica_escolha' || e.target.value === 'multipla_escolha') {
                opcoesContainer.classList.remove('hidden');
            } else {
                opcoesContainer.classList.add('hidden');
                opcoesContainer.innerHTML = '<label class="block text-sm font-medium text-gray-700">Opções de Resposta</label><button type="button" class="text-sm font-medium text-teal-600 hover:text-teal-800" onclick="addOpcao(' + index + ')">+ Adicionar Opção</button>'; // Limpa opções antigas
            }
        }
    });
});

function addOpcao(perguntaIndex) {
    const opcoesContainer = document.getElementById(`opcoesContainer_${perguntaIndex}`);
    const opcaoIndex = opcoesContainer.querySelectorAll('input').length;
    const opcaoHTML = `
        <div class="flex items-center" id="opcao_${perguntaIndex}_${opcaoIndex}">
            <input type="text" name="perguntas[${perguntaIndex}][opcoes][]" required class="block w-full sm:text-sm rounded-md border-gray-300 shadow-sm">
            <button type="button" class="ml-2 text-red-500" onclick="removerElemento('opcao_${perguntaIndex}_${opcaoIndex}')">X</button>
        </div>
    `;
    opcoesContainer.insertAdjacentHTML('beforeend', opcaoHTML);
}

function removerElemento(elementId) {
    document.getElementById(elementId).remove();
}
</script>

<?php require_once 'templates/footer.php'; ?>
