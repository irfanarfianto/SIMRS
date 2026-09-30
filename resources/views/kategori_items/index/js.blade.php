<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [[0, 'asc']],
        });
        getData()
    });

    $('.btn-get-data').click(function() {
        getData()
    })

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function getData() {
        $('#loading-filter').show();
        var dataTableObj = $('#table').DataTable();
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{url("kategori-items/search")}}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: {
                kode: $('#filter-kode').val(),
                nama: $('#filter-nama').val()
            },
            success: function(results) {
                $.each(results.data, function(index, item) {
                    var html = `<a href="{{url('kategori-items/view/')}}/` + item.id + `" class="btn btn-primary">View</a>`
                    dataTableObj.row.add([
                        escapeHtml(item.kode),
                        escapeHtml(item.nama),
                        item.items_count,
                        html
                    ]);
                });
                dataTableObj.draw();
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
            }
        })
    }
</script>
