{{-- Pemakaian: <button type="button" data-bs-toggle="modal" data-bs-target="#modal-konfirmasi-hapus" data-url="..." data-pesan="...">Delete</button> --}}
<div class="modal fade" id="modal-konfirmasi-hapus" tabindex="-1" aria-labelledby="modal-konfirmasi-hapus-judul" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="#" id="modal-konfirmasi-hapus-form" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="modal-konfirmasi-hapus-judul">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="modal-konfirmasi-hapus-pesan">
                Yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.getElementById('modal-konfirmasi-hapus').addEventListener('show.bs.modal', function(event) {
        var pemicu = event.relatedTarget;
        document.getElementById('modal-konfirmasi-hapus-pesan').textContent = pemicu.getAttribute('data-pesan') || 'Yakin ingin menghapus data ini?';
        document.getElementById('modal-konfirmasi-hapus-form').setAttribute('action', pemicu.getAttribute('data-url'));
    });
</script>
