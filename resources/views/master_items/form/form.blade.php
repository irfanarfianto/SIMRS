<form method="POST" enctype="multipart/form-data">
    @csrf
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if($method == 'edit')
    <div class="form-group">
        <label for="kode_barang">Kode Barang</label>
        <input type="text" class="form-control" id="kode_barang" name="kode_barang" readonly value="{{$item->kode ?? ''}}">
    </div>
    @endif

    <div class="form-group">
        <label for="nama">Nama</label>
        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" required maxlength="255" value="{{ old('nama', $item->nama ?? '') }}">
        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="harga_beli">Harga Beli</label>
        <input type="number" class="form-control @error('harga_beli') is-invalid @enderror" id="harga_beli" name="harga_beli" required min="0" max="2000000000" step="1" value="{{ old('harga_beli', $item->harga_beli ?? '') }}">
        @error('harga_beli')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="laba">Laba (dalam persen)</label>
        <input type="number" class="form-control @error('laba') is-invalid @enderror" id="laba" name="laba" required min="0" max="1000" step="1" value="{{ old('laba', $item->laba ?? '') }}">
        @error('laba')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @php $selected = old('supplier', $item->supplier ?? ''); @endphp
    <div class="form-group">
        <label for="supplier">Supplier</label>
        <select class="form-control @error('supplier') is-invalid @enderror" required id="supplier" name="supplier">
            <option @if($selected == '') selected @endif value="">--Pilih--</option>
            @foreach($daftar_supplier as $supplier)
            <option @if($selected == $supplier) selected @endif>{{ $supplier }}</option>
            @endforeach
        </select>
        @error('supplier')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @php $selected = old('jenis', $item->jenis ?? ''); @endphp
    <div class="form-group">
        <label for="jenis">Jenis</label>
        <select class="form-control @error('jenis') is-invalid @enderror" required id="jenis" name="jenis">
            <option @if($selected == '') selected @endif value="">--Pilih--</option>
            @foreach($daftar_jenis as $jenis)
            <option @if($selected == $jenis) selected @endif>{{ $jenis }}</option>
            @endforeach
        </select>
        @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label>Kategori</label>
        <div>
            @forelse($list_kategori as $kategori)
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="kategori[]" id="kategori-{{ $kategori->id }}" value="{{ $kategori->id }}" @if(in_array($kategori->id, $kategori_terpilih)) checked @endif>
                <label class="form-check-label" for="kategori-{{ $kategori->id }}">{{ $kategori->nama }}</label>
            </div>
            @empty
            <small class="text-muted">Belum ada kategori. <a href="{{ url('kategori-items/form/new') }}">Buat kategori baru</a></small>
            @endforelse
        </div>
        @error('kategori.*')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="foto">Foto</label>
        @if(!empty($item->foto))
        <div class="mb-2">
            <img src="{{ asset($item->foto) }}" alt="Foto {{ $item->nama }}" class="img-thumbnail" style="max-height: 150px;">
        </div>
        @endif
        <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/png, image/jpeg, image/webp">
        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted">Format JPG, PNG, atau WEBP, maksimal 2 MB.@if(!empty($item->foto)) Kosongkan bila tidak ingin mengganti foto.@endif</small>
    </div>

    <button class="btn btn-primary mt-3">Submit</button>

</form>
