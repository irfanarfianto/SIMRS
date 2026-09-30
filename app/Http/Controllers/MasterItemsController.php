<?php

namespace App\Http\Controllers;

use App\Exports\MasterItemsExport;
use App\Models\KategoriItem;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class MasterItemsController extends Controller
{
    public function index()
    {
        return view('master_items.index.index');
    }

    public function search(Request $request)
    {
        $data_search = $this->filterQuery($request)->select('kode', 'nama', 'jenis', 'harga_beli', 'laba', 'supplier')->orderBy('id')->get();

        return json_encode([
            'status' => 200,
            'data' => $data_search
        ]);
    }

    public function exportExcel(Request $request)
    {
        $nama_file = 'master-items-' . now()->timezone('Asia/Jakarta')->format('Ymd-His') . '.xlsx';
        return Excel::download(new MasterItemsExport($this->filterQuery($request)), $nama_file);
    }

    private function filterQuery(Request $request)
    {
        $kode = $request->kode;
        $nama = $request->nama;
        $hargamin = $request->hargamin;
        $hargamax = $request->hargamax;

        $data_search = MasterItem::query();

        if (!empty($kode)) $data_search = $data_search->where('kode', $kode);
        if (!empty($nama)) $data_search = $data_search->where('nama', 'LIKE', '%' . $nama . '%');
        if (is_numeric($hargamin)) $data_search = $data_search->where('harga_beli', '>=', $hargamin);
        if (is_numeric($hargamax)) $data_search = $data_search->where('harga_beli', '<=', $hargamax);

        return $data_search;
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = [];
        } else {
            $item = MasterItem::with('kategori')->find($id);
        }
        $data['item'] = $item;
        $data['method'] = $method;
        $data['list_kategori'] = KategoriItem::orderBy('nama')->get();
        $data['kategori_terpilih'] = old('kategori', $item ? $item->kategori->pluck('id')->all() : []);
        return view('master_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = MasterItem::with('kategori')->where('kode', $kode)->first();
        if (!$data['data']) return redirect('master-items')->with('error', 'Item dengan kode ' . $kode . ' tidak ditemukan.');
        return view('master_items.single.index', $data);
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori' => 'nullable|array',
            'kategori.*' => 'exists:kategori_items,id',
        ]);

        if ($method == 'new') {
            $data_item = new MasterItem;
            // sementara; kode final diturunkan dari id setelah insert
            $data_item->kode = '';
        } else {
            $data_item = MasterItem::find($id);
            if (!$data_item) return redirect('master-items')->with('error', 'Item tidak ditemukan, mungkin sudah dihapus.');
        }

        $data_item->nama = $request->nama;
        $data_item->harga_beli = $request->harga_beli;
        $data_item->laba = $request->laba;
        $data_item->supplier = $request->supplier;
        $data_item->jenis = $request->jenis;

        if ($request->hasFile('foto')) {
            $foto_lama = $data_item->foto;
            $data_item->foto = $this->uploadFoto($request->file('foto'));
            if (!empty($foto_lama) && File::exists(public_path($foto_lama))) File::delete(public_path($foto_lama));
        }

        DB::transaction(function () use ($data_item, $request) {
            $data_item->save();

            // Kode dari id auto-increment: unik walau ada item yang di-soft-delete atau submit bersamaan
            // (sebelumnya count + 1, yang menghasilkan kode kembar setelah ada item terhapus).
            if ($data_item->kode === '') {
                $data_item->kode = str_pad($data_item->id, 5, '0', STR_PAD_LEFT);
                $data_item->save();
            }

            $data_item->kategori()->sync($request->input('kategori', []));
        });

        $pesan = $method == 'new' ? 'Item ' . $data_item->nama . ' berhasil ditambahkan.' : 'Item ' . $data_item->nama . ' berhasil diperbarui.';
        return redirect('master-items')->with('success', $pesan);
    }

    public function delete($id)
    {
        $data_item = MasterItem::find($id);
        if (!$data_item) return redirect('master-items')->with('error', 'Item tidak ditemukan, mungkin sudah dihapus.');

        $data_item->delete();
        return redirect('master-items')->with('success', 'Item ' . $data_item->nama . ' berhasil dihapus.');
    }

    public function updateRandomData()
    {
        $data = MasterItem::get();
        foreach($data as $item)
        {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->harga_beli = rand(100,1000000);
            $item->laba = rand(10,99);
            $item->kode = $kode;
            $item->supplier = $this->getRandomSupplier();
            $item->jenis = $this->getRandomJenis();
            $item->save();
        }
    }

    private function uploadFoto($file)
    {
        $folder = 'uploads/master-items';
        $nama_file = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($folder), $nama_file);
        return $folder . '/' . $nama_file;
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi','Bukulapuk','TokoBagas','E Commurz','Blublu'];
        $random = rand(0,4);
        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat','Alkes','Matkes','Umum','ATK'];
        $random = rand(0,4);
        return $array[$random];
    }
}
