<footer style="background-color: var(--cor-footer-bg);" class="text-gray-300 py-8">
    <div class="container mx-auto px-6 text-center">
        <p>&copy; <?php echo date('Y'); ?> AnimaPsique. Todos os direitos reservados.</p>
        <p class="text-sm mt-2">Desenvolvido com carinho para a sua jornada de autoconhecimento.</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleciona todos os links que apontam para WhatsApp
    const whatsappLinks = document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp.com"]');
    
    whatsappLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Permite que o link abra em nova aba (comportamento padrão se tiver target="_blank")
            
            // Redireciona a página atual para a confirmação após um breve delay
            setTimeout(() => {
                window.location.href = 'confirmacao.php?origem=whatsapp';
            }, 1000); // 1 segundo de delay para garantir que a nova aba abra
        });
    });
});
</script>

</body>
</html>
