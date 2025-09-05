Write-Host "==========================="
Write-Host "   Git Helper Script"
Write-Host "==========================="
Write-Host "1) Subir alterações locais -> GitHub"
Write-Host "2) Trazer alterações do GitHub -> Local"
Write-Host "3) Mostrar status"
Write-Host "4) Sair"
$opcao = Read-Host "Escolha uma opção"

switch ($opcao) {
    1 {
        $msg = Read-Host "Mensagem do commit"
        if (-not $msg) { $msg = "Atualização automática" }

        git pull --rebase
        if ($LASTEXITCODE -ne 0) {
            Write-Host "⚠️ Houve conflito durante o pull!"
            Write-Host "Arquivos em conflito:"
            git diff --name-only --diff-filter=U
            Write-Host "Resolva manualmente os conflitos (<<<<<<< >>>>>>>) e depois rode:"
            Write-Host "git add . ; git rebase --continue"
            break
        }

        git add .
        git commit -m "$msg"
        git push
    }
    2 {
        Write-Host "⚠️ Isso pode sobrescrever alterações locais não commitadas!"
        $confirm = Read-Host "Continuar mesmo assim? (s/n)"
        if ($confirm -eq "s") {
            git fetch --all
            git reset --hard origin/main
        } else {
            Write-Host "Operação cancelada."
        }
    }
    3 {
        git status
    }
    4 {
        Write-Host "Saindo..."
    }
    Default {
        Write-Host "Opção inválida!"
    }
}
