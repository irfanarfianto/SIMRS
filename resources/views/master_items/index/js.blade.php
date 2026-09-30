@include('layouts.partials.datatables')

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            order: [[0, 'desc']],
            columnDefs: [{
                // Harga Beli & Harga Jual: tampil berformat ribuan, sort tetap pakai angka mentah
                targets: [3, 4],
                className: 'text-end text-nowrap',
                render: function(data, type) {
                    return type === 'display' ? 'Rp ' + Number(data).toLocaleString('id-ID') : data;
                }
            }, {
                targets: [6],
                orderable: false
            }]
        });
        pasangFilter(getData);
        getData()
    });

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

    function getData(){
        setLoading(true);
        $('#pesan-error').addClass('d-none');
        var dataTableObj = $('#table').DataTable();
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{url("master-items/search")}}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: {
                kode: $('#filter-kode').val(),
                nama: $('#filter-nama').val(),
                hargamin: $('#filter-harga-min').val(),
                hargamax: $('#filter-harga-max').val()
            },
            success: function(results) {
                $.each(results.data, function(index, item) {
                    var html = `<a href="{{url('master-items/view/')}}/` + encodeURIComponent(item.kode) + `" class="btn btn-primary btn-sm">View</a>`

                    // Kolom ditulis eksplisit dan teks di-escape: DataTables merender isi sel sebagai HTML
                    dataTableObj.row.add([
                        escapeHtml(item.kode),
                        escapeHtml(item.nama),
                        escapeHtml(item.jenis),
                        item.harga_beli,
                        item.harga_jual,
                        escapeHtml(item.supplier),
                        html
                    ]);
                });
                dataTableObj.draw();
                setLoading(false);
            },
            error: function(xhr, textStatus, errorThrown) {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    $.ajax(this);
                    return;
                }
                $('#pesan-error').removeClass('d-none');
                setLoading(false);
            }
        })
    }
</script>
