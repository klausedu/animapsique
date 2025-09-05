param(
    [string]$msg = "Atualização automática"
)

git status
git add .
git commit -m "$msg"
git push
