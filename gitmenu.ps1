# Script Git Inteligente
# Salva este arquivo como git_auto.ps1

# Função para verificar se há alterações locais
function Check-Changes {
    $status = git status --porcelain
    return $status
}

# Função para resolver stash, pull e re-aplicar
function Safe-Pull {
    $changes = Check-Changes
    if ($changes) {
        Write-Host "Alterações locais detectadas, aplicando stash..."
        git stash
        $stashApplied = $true
    } else {
        $stashApplied = $false
    }

    Write-Host "Fazendo pull do repositório remoto..."
    git pull --rebase origin main

    if ($stashApplied) {
        Write-Host "Reaplicando alterações locais..."
        git stash pop
    }
}

# Função para commit inteligente
function Smart-Commit {
    param([string]$msg)
    if (-not $msg) {
        $msg = Read-Host "Mensagem do commit"
    }
    git add .
    git commit -m $msg
}

# Menu principal
Write-Host "Escolha uma opção:"
Write-Host "1 - Commit + Push"
Write-Host "2 - Pull seguro"
Write-Host "3 - Status"
$opcao = Read-Host "Opção"

switch ($opcao) {
    "1" {
        $msg = Read-Host "Mensagem do commit"
        Smart-Commit -msg $msg
        Safe-Pull  # Garante que está atualizado antes do push
        git push origin main
    }
    "2" {
        Safe-Pull
    }
    "3" {
        git status
    }
    default {
        Write-Host "Opção inválida"
    }
}
