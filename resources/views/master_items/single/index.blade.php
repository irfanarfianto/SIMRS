@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-group mb-2">
                <a href="{{url('master-items')}}" class="btn btn-secondary">Kembali ke Daftar Item</a>
            </div>
            <div class="card">
                <div class="card-header">Master Item</div>

                <div class="card-body">
                    @if(!empty($data->foto))
                    <div class="mb-3">
                        <img src="{{ asset($data->foto) }}" alt="Foto {{ $data->nama }}" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                    @endif
                    <table class="mb-3">
                        <tr>
                            <th class="pe-2 text-nowrap align-top">Kode</th>
                            <td class="pe-2 align-top">:</td>
                            <td>{{$data->kode}}</td>
                        </tr>
                        <tr>
                            <th class="pe-2 text-nowrap align-top">Nama</th>
                            <td class="pe-2 align-top">:</td>
                            <td>{{$data->nama}}</td>
                        </tr>
                        <tr>
                            <th class="pe-2 text-nowrap align-top">Harga Beli</th>
                            <td class="pe-2 align-top">:</td>
                            <td>Rp {{ number_format($data->harga_beli, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="pe-2 text-nowrap align-top">Laba</th>
                            <td class="pe-2 align-top">:</td>
                            <td>{{$data->laba}}%</td>
                        </tr>
                        <tr>
                            <th class="pe-2 text-nowrap align-top">Harga Jual</th>
                            <td class="pe-2 align-top">:</td>
                            <td>Rp {{ number_format($data->harga_jual, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="pe-2 text-nowrap align-top">Supplier</th>
                            <td class="pe-2 align-top">:</td>
                            <td>{{$data->supplier}}</td>
                        </tr>
                        <tr>
                            <th class="pe-2 text-nowrap align-top">Jenis</th>
                            <td class="pe-2 align-top">:</td>
                            <td>{{$data->jenis}}</td>
                        </tr>
                        <tr>
                            <th class="pe-2 text-nowrap align-top">Kategori</th>
                            <td class="pe-2 align-top">:</td>
                            <td>
                                @forelse($data->kategori as $kategori)
                                <a href="{{url('kategori-items/view')}}/{{$kategori->id}}" class="badge bg-secondary text-decoration-none">{{$kategori->nama}}</a>
                                @empty
                                -
                                @endforelse
                            </td>
                        </tr>
                    </table>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-info" href="{{url('master-items/form/edit')}}/{{$data->id}}">Edit</a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-konfirmasi-hapus" data-url="{{url('master-items/delete')}}/{{$data->id}}" data-pesan="Yakin ingin menghapus item {{ $data->nama }}?">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@endsection