<?php
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Obter a lista de pacientes
$pdo = conectar();
$stmt = $pdo->prepare("SELECT id, nome, email, telefone, ativo FROM pacientes ORDER BY nome");
$stmt->execute();
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pacientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4b3271b654.js" crossorigin="anonymous"></script>
    <style>
        .status-ativo {
            color: green;
        }

        .status-inativo {
            color: red;
        }
    </style>
</head>

<body>
    <?php include 'templates/header.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users"></i> Gerenciar Pacientes</h2>
            <button class="btn btn-success" data-toggle="modal" data-target="#modalPaciente" onclick="abrirModalCadastro()"><i class="fas fa-plus"></i> Adicionar Novo Paciente</button>
        </div>

        <div id="alert-placeholder"></div>

        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="listaPacientes">
                <?php foreach ($pacientes as $paciente) : ?>
                    <tr id="paciente-<?= $paciente['id'] ?>">
                        <td><?= htmlspecialchars($paciente['nome']) ?></td>
                        <td><?= htmlspecialchars($paciente['email']) ?></td>
                        <td><?= htmlspecialchars($paciente['telefone']) ?></td>
                        <td>
                            <span class="status-<?= $paciente['ativo'] ? 'ativo' : 'inativo' ?>">
                                <?= $paciente['ativo'] ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </td>
                        <td>
                            <a href="prontuario_paciente?id=<?= $paciente['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-file-medical"></i> Prontuário</a>
                            <button class="btn btn-warning btn-sm" onclick="abrirModalEdicao(<?= $paciente['id'] ?>)"><i class="fas fa-edit"></i> Editar</button>
                            <button class="btn btn-<?= $paciente['ativo'] ? 'secondary' : 'primary' ?> btn-sm" onclick="toggleStatusPaciente(<?= $paciente['id'] ?>, <?= $paciente['ativo'] ?>)">
                                <?= $paciente['ativo'] ? '<i class="fas fa-times-circle"></i> Desativar' : '<i class="fas fa-check-circle"></i> Ativar' ?>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deletarPaciente(<?= $paciente['id'] ?>)"><i class="fas fa-trash"></i> Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modalPaciente" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Adicionar Novo Paciente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formPaciente">
                        <input type="hidden" name="id" id="pacienteId">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone">
                        </div>
                        <div class="form-group">
                            <label for="senha">Senha (deixe em branco para não alterar)</label>
                            <input type="password" class="form-control" id="senha" name="senha">
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formPaciente = document.getElementById('formPaciente');
            const modalPaciente = $('#modalPaciente');
            const modalLabel = document.getElementById('modalLabel');

            function showAlert(message, type = 'success') {
                const alertPlaceholder = document.getElementById('alert-placeholder');
                const wrapper = document.createElement('div');
                wrapper.innerHTML = [
                    `<div class="alert alert-${type} alert-dismissible" role="alert">`,
                    `   <div>${message}</div>`,
                    '   <button type="button" class="close" data-dismiss="alert" aria-label="Close">',
                    '       <span aria-hidden="true">&times;</span>',
                    '   </button>',
                    '</div>'
                ].join('');
                alertPlaceholder.append(wrapper);
            }

            // Limpar formulário ao abrir para cadastro
            window.abrirModalCadastro = function() {
                formPaciente.reset();
                document.getElementById('pacienteId').value = '';
                modalLabel.textContent = 'Adicionar Novo Paciente';
            }

            // Submissão para adicionar/editar paciente
            formPaciente.addEventListener('submit', function(e) {
                e.preventDefault();
                const action = document.getElementById('pacienteId').value ? 'edit' : 'add';
                const url = `processa_paciente?action=${action}`;
                const formData = new FormData(formPaciente);

                formData.delete('action'); // Garante que não haja action duplicada

                fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Paciente salvo com sucesso!');
                            modalPaciente.modal('hide');
                            location.reload(); // Recarrega a página para ver as mudanças
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    }).catch(error => showAlert('Erro ao conectar com o servidor.', 'danger'));
            });

            // Carregar dados do paciente para edição
            window.abrirModalEdicao = function(id) {
                formPaciente.reset();
                fetch(`processa_paciente?action=get_paciente&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('pacienteId').value = data.paciente.id;
                            document.getElementById('nome').value = data.paciente.nome;
                            document.getElementById('email').value = data.paciente.email;
                            document.getElementById('telefone').value = data.paciente.telefone;
                            modalLabel.textContent = 'Editar Paciente';
                            modalPaciente.modal('show');
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    }).catch(error => showAlert('Erro ao carregar dados do paciente.', 'danger'));
            }

            // Deletar paciente
            window.deletarPaciente = function(id) {
                if (confirm('Tem certeza que deseja excluir este paciente? Esta ação não pode ser desfeita.')) {
                    fetch(`processa_paciente?action=delete&id=${id}`, {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert('Paciente excluído com sucesso!');
                                document.getElementById(`paciente-${id}`).remove();
                            } else {
                                showAlert(data.message, 'danger');
                            }
                        }).catch(error => showAlert('Erro ao conectar com o servidor.', 'danger'));
                }
            }

            // Ativar/desativar paciente
            window.toggleStatusPaciente = function(id, statusAtual) {
                const novoStatus = statusAtual == 1 ? 0 : 1;
                fetch(`processa_paciente?action=toggle_status&id=${id}&status=${novoStatus}`, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Status do paciente atualizado com sucesso!');
                            location.reload();
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    }).catch(error => showAlert('Erro ao conectar com o servidor.', 'danger'));
            }
        });
    </script>
</body>

</html>
