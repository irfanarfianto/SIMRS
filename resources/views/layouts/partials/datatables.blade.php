{{-- DataTables bersama untuk halaman index: skrip, CSS Bootstrap 5, dan bawaan tampilan --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<script>
    $.extend(true, $.fn.dataTable.defaults, {
        searching: false,
        // tabel dibungkus table-responsive: di layar sempit tabel yang digeser, bukan seluruh halaman
        dom: "<'row'<'col-12'l>>" +
            "<'table-responsive'tr>" +
            "<'row align-items-center'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
        language: {
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            emptyTable: 'Tidak ada data yang cocok dengan filter',
            zeroRecords: 'Tidak ada data yang cocok dengan filter',
            paginate: { previous: '‹', next: '›' }
        }
    });

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    // Filter: berjalan otomatis 400 md setelah berhenti mengetik; Enter/tombol Filter langsung;
    // Reset mengosongkan lalu memuat ulang
    function pasangFilter(getData) {
        var jeda;
        $('#filter-form').on('input', 'input', function() {
            clearTimeout(jeda);
            jeda = setTimeout(getData, 400);
        });
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            clearTimeout(jeda);
            getData();
        });
        $('#filter-reset').on('click', function() {
            clearTimeout(jeda);
            $('#filter-form')[0].reset();
            getData();
        });
    }

    // Request filter sebelumnya dibatalkan: tanpa ini respons lama yang tiba belakangan
    // menimpa tabel dengan hasil ketikan sebelumnya
    var xhrFilter = null;
    function kirimFilter(opsi) {
        if (xhrFilter) xhrFilter.abort();
        xhrFilter = $.ajax(opsi);
    }

    function setLoading(aktif) {
        $('#loading-filter').toggleClass('d-none', !aktif);
        $('#filter-form button[type=submit]').prop('disabled', aktif);
    }
</script>
