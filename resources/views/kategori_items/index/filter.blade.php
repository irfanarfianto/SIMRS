<form id="filter-form" class="mb-3" novalidate>
    <h4>Filter</h4>
    <div class="row g-2">
        <div class="col-12 col-sm-6">
            <label for="filter-nama" class="form-label mb-1">Nama</label>
            <input type="text" class="form-control" id="filter-nama" autocomplete="off">
        </div>
        <div class="col-12 col-sm-6">
            <label for="filter-kode" class="form-label mb-1">Kode</label>
            <input type="text" class="form-control" id="filter-kode" autocomplete="off">
        </div>
    </div>
    @include('layouts.partials.filter-aksi')
</form>
