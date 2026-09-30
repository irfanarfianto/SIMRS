{{-- Pemakaian: <a href="#" data-bs-toggle="modal" data-bs-target="#modal-konfirmasi-hapus" data-url="..." data-pesan="...">Delete</a> --}}
<div class="modal fade" id="modal-konfirmasi-hapus" tabindex="-1" aria-labelledby="modal-konfirmasi-hapus-judul" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-konfirmasi-hapus-judul">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="modal-konfirmasi-hapus-pesan">
                Yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" class="btn btn-danger" id="modal-konfirmasi-hapus-tombol">Hapus</a>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('modal-konfirmasi-hapus').addEventListener('show.bs.modal', function(event) {
        var pemicu = event.relatedTarget;
        document.getElementById('modal-konfirmasi-hapus-pesan').textContent = pemicu.getAttribute('data-pesan') || 'Yakin ingin menghapus data ini?';
        document.getElementById('modal-konfirmasi-hapus-tombol').setAttribute('href', pemicu.getAttribute('data-url'));
    });
</script>
