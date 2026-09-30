@php
    $toasts = [];
    if (session('success')) $toasts[] = ['bg' => 'success', 'judul' => 'Berhasil', 'pesan' => session('success')];
    if (session('error')) $toasts[] = ['bg' => 'danger', 'judul' => 'Gagal', 'pesan' => session('error')];
    if ($errors->any()) $toasts[] = ['bg' => 'danger', 'judul' => 'Gagal', 'pesan' => 'Data gagal disimpan, periksa kembali isian form.'];
@endphp

@if(count($toasts))
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
    @foreach($toasts as $toast)
    <div class="toast show align-items-center text-bg-{{ $toast['bg'] }} border-0" role="{{ $toast['bg'] == 'danger' ? 'alert' : 'status' }}" aria-live="{{ $toast['bg'] == 'danger' ? 'assertive' : 'polite' }}" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <strong>{{ $toast['judul'] }}.</strong> {{ $toast['pesan'] }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
    @endforeach
</div>
<script>
    // Toast dirender sudah tampil; sembunyikan otomatis setelah 5 detik.
    setTimeout(function() {
        document.querySelectorAll('.toast-container .toast.show').forEach(function(el) {
            el.classList.remove('show');
        });
    }, 5000);
</script>
@endif
