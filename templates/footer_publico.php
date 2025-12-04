<footer style="background-color: var(--cor-footer-bg);" class="text-gray-300 py-8">
    <div class="container mx-auto px-6 text-center">
        <div class="flex justify-center space-x-6 mb-4">
            <a href="https://www.linkedin.com/in/narahelenalopes/" target="_blank" class="text-gray-400 hover:text-[var(--cor-primaria)] transition-colors">
                <span class="sr-only">LinkedIn</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
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
