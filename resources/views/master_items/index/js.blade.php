<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<script>
    var start_date = '';
    var end_date = '';
    var data_per_fetch = 500;
    var data_fetched = 0;

    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [[0, 'desc']],
            columnDefs: [{
                // Harga Beli & Harga Jual: tampil berformat ribuan, sort tetap pakai angka mentah
                targets: [3, 4],
                className: 'text-end',
                render: function(data, type) {
                    return type === 'display' ? 'Rp ' + Number(data).toLocaleString('id-ID') : data;
                }
            }]
        });
        getData()
    });

    $('.btn-get-data').click(function() {
        getData()
    })

    $('.btn-export-excel').click(function(e) {
        e.preventDefault();
        var params = $.param({
            kode: $('#filter-kode').val(),
            nama: $('#filter-nama').val(),
            hargamin: $('#filter-harga-min').val(),
            hargamax: $('#filter-harga-max').val()
        });
        window.location.href = $(this).attr('href') + '?' + params;
    })

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function getData(){
        
        $('#loading-filter').show();
        var dataTableObj = $('#table').DataTable();
        var filter_kode = $('#filter-kode').val()
        var filter_nama = $('#filter-nama').val()
        var filter_harga_min = $('#filter-harga-min').val()
        var filter_harga_max = $('#filter-harga-max').val()
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{url("master-items/search")}}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: {
                kode: filter_kode,
                nama: filter_nama,
                hargamin: filter_harga_min,
                hargamax: filter_harga_max
            },
            success: function(results) {
                var data = results.data

                $.each(data, function(index, item) {
                    var html = `<a href="{{url('master-items/view/')}}/` + encodeURIComponent(item.kode) + `" class="btn btn-primary">View</a>`

                    // Kolom ditulis eksplisit dan teks di-escape: DataTables merender isi sel sebagai HTML
                    var array_temp = [
                        escapeHtml(item.kode),
                        escapeHtml(item.nama),
                        escapeHtml(item.jenis),
                        item.harga_beli,
                        item.harga_jual,
                        escapeHtml(item.supplier),
                        html
                    ];


                    dataTableObj.row.add(array_temp).draw(true);
                });
                $('#loading-filter').hide();
            },
            error: function(xhr, textStatus, errorThrown) {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    $.ajax(this);
                    return;
                }
                alert('Terjadi kesalahan server, tidak dapat mengambil data')
                $('#loading-filter').hide();

                return;
            }
        })
    }
</script>