@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-group mb-2">
                <a href="{{url('kategori-items')}}" class="btn btn-secondary">Kembali ke Daftar Kategori</a>
            </div>
            <div class="card">
                <div class="card-header">Kategori Item</div>

                <div class="card-body">
                    <table class="mb-3">
                        <tr>
                            <th class="pe-2">Nama</th>
                            <td class="pe-2">:</td>
                            <td>{{$data->nama}}</td>
                        </tr>
                        <tr>
                            <th class="pe-2">Kode</th>
                            <td class="pe-2">:</td>
                            <td>{{$data->kode}}</td>
                        </tr>
                    </table>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-info" href="{{url('kategori-items/form/edit')}}/{{$data->id}}">Edit</a>
                        <a class="btn btn-success" href="{{url('kategori-items/pdf')}}/{{$data->id}}">Download PDF</a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-konfirmasi-hapus" data-url="{{url('kategori-items/delete')}}/{{$data->id}}" data-pesan="Yakin ingin menghapus kategori {{ $data->nama }}? Item tidak ikut terhapus, hanya dilepas dari kategori ini.">Delete</button>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 mb-2">
                        <h5 class="mb-0">Daftar Item ({{ $data->items->count() }})</h5>
                        <a href="{{ url('master-items/form/new') }}?kategori={{ $data->id }}" class="btn btn-outline-primary btn-sm">+ Item di Kategori Ini</a>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Supplier</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data->items as $item)
                            <tr>
                                <td>{{$item->kode}}</td>
                                <td>{{$item->nama}}</td>
                                <td>{{$item->jenis}}</td>
                                <td>{{$item->supplier}}</td>
                                <td><a href="{{url('master-items/view')}}/{{$item->kode}}" class="btn btn-primary btn-sm">View</a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada item dengan kategori ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@endsection
