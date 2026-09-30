<?php

namespace App\Http\Controllers;

use App\Models\KategoriItem;
use App\Models\MasterItem;
use App\Support\SimpleXlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
        $data_items = $this->filterQuery($request)->with('kategori')->orderBy('id')->get();

        $rows = [];
        foreach ($data_items as $index => $item) {
            $rows[] = [
                $index + 1,
                $item->kategori->pluck('nama')->implode(', '),
                $item->nama,
                $item->supplier,
                (int) $item->harga_beli,
                (int) $item->laba,
                (int) round($item->harga_beli + $item->harga_beli * $item->laba / 100),
            ];
        }

        $header = ['No', 'Nama Kategori', 'Nama Items', 'Nama Supplier', 'Harga', 'Laba (%)', 'Harga Jual'];
        $path = SimpleXlsx::build('Master Items', $header, $rows, [4, 6]);

        return response()->download($path, 'master-items-' . now()->timezone('Asia/Jakarta')->format('Ymd-His') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
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
            $kode = MasterItem::count('id');
            $kode = $kode + 1;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);
            sleep(3);
        } else {
            $data_item = MasterItem::find($id);
            $kode = $data_item->kode;
        }

        $data_item->nama = $request->nama;
        $data_item->harga_beli = $request->harga_beli;
        $data_item->laba = $request->laba;
        $data_item->kode = $kode;
        $data_item->supplier = $request->supplier;
        $data_item->jenis = $request->jenis;

        if ($request->hasFile('foto')) {
            $foto_lama = $data_item->foto;
            $data_item->foto = $this->uploadFoto($request->file('foto'));
            if (!empty($foto_lama) && File::exists(public_path($foto_lama))) File::delete(public_path($foto_lama));
        }

        $data_item->save();
        $data_item->kategori()->sync($request->input('kategori', []));

        return redirect('master-items');
    }

    public function delete($id)
    {
        MasterItem::find($id)->delete();
        return redirect('master-items');
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
