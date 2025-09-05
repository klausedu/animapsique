<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$page_title = 'Meus Recibos';
require_once 'templates/header.php';

try {
    $pdo = conectar();
    // Busca todos os recibos do paciente logado
    $stmt = $pdo->prepare("SELECT id, caminho_pdf, data_emissao, valor_recebido, data_recebimento FROM recibos WHERE paciente_id = ? ORDER BY data_emissao DESC");
    $stmt->execute([$paciente_id]);
    $recibos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar recibos do paciente: " . $e->getMessage());
    $recibos = [];
}
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Histórico de Recibos</h2>
        <p class="text-gray-600 mb-6">Aqui você pode visualizar e baixar todos os recibos emitidos para suas sessões.</p>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Emissão</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data do Pagamento</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor (R$)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($recibos)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Nenhum recibo encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recibos as $recibo): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo (new DateTime($recibo['data_emissao']))->format('d/m/Y'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo (new DateTime($recibo['data_recebimento']))->format('d/m/Y'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo number_format($recibo['valor_recebido'], 2, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="../../uploads/recibos/<?php echo htmlspecialchars($recibo['caminho_pdf']); ?>" target="_blank" class="inline-flex items-center rounded-md bg-teal-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-700">
                                        Baixar PDF
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
