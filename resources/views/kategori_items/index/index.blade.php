@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="d-flex flex-wrap gap-2 mb-2">
                <a href="{{url('kategori-items/form/new')}}" class="btn btn-secondary">+ Kategori Items Baru</a>
            </div>
            <div class="card">
                <div class="card-header">Daftar Kategori Items</div>

                <div class="card-body">
                    @include('kategori_items.index.filter')
                    @include('kategori_items.index.table')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@include('kategori_items.index.js')
@endsection
