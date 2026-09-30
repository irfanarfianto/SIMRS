<script>
    // Form ber-data-cegah-ganda: tombol simpan dikunci setelah submit agar tidak terkirim dua kali
    document.querySelectorAll('form[data-cegah-ganda]').forEach(function(form) {
        form.addEventListener('submit', function() {
            var tombol = form.querySelector('button[type=submit]');
            if (!tombol) return;
            tombol.disabled = true;
            tombol.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Menyimpan...';
        });
    });
</script>
