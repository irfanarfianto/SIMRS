@include('layouts.partials.datatables')

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            order: [[0, 'asc']],
            columnDefs: [{
                targets: [2],
                className: 'text-end'
            }, {
                targets: [3],
                orderable: false
            }]
        });
        pasangFilter(getData);
        getData()
    });

    function getData() {
        setLoading(true);
        $('#pesan-error').addClass('d-none');
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
                    var html = `<a href="{{url('kategori-items/view/')}}/` + item.id + `" class="btn btn-primary btn-sm">View</a>`
                    dataTableObj.row.add([
                        escapeHtml(item.kode),
                        escapeHtml(item.nama),
                        item.items_count,
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
