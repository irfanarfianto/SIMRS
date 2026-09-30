@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @php $url_kembali = $method == 'edit' ? url('master-items/view/' . $item->kode) : url('master-items'); @endphp
            <div class="form-group mb-2">
                <a href="{{ $url_kembali }}" class="btn btn-secondary">{{ $method == 'edit' ? 'Kembali ke Detail Item' : 'Kembali ke Daftar Item' }}</a>
            </div>
            <div class="card">

                @if($method == 'new')
                <div class="card-header">Buat Master Item Baru</div>
                @else
                <div class="card-header">Edit Master Item</div>
                @endif

                <div class="card-body">
                    @include('master_items.form.form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@endsection