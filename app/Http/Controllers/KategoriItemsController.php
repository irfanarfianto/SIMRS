<?php

namespace App\Http\Controllers;

use App\Models\KategoriItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KategoriItemsController extends Controller
{
    public function index()
    {
        return view('kategori_items.index.index');
    }

    public function search(Request $request)
    {
        $kode = $request->kode;
        $nama = $request->nama;

        $data_search = KategoriItem::query();

        if (!empty($kode)) $data_search = $data_search->where('kode', 'LIKE', '%' . $kode . '%');
        if (!empty($nama)) $data_search = $data_search->where('nama', 'LIKE', '%' . $nama . '%');

        $data_search = $data_search->withCount('items')->select('id', 'kode', 'nama')->orderBy('id')->get();

        return json_encode([
            'status' => 200,
            'data' => $data_search
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $kategori = [];
        } else {
            $kategori = KategoriItem::findOrFail($id);
        }
        $data['kategori'] = $kategori;
        $data['method'] = $method;
        return view('kategori_items.form.index', $data);
    }

    public function singleView($id)
    {
        $data['data'] = KategoriItem::with(['items' => fn($query) => $query->orderBy('kode')])->findOrFail($id);
        return view('kategori_items.single.index', $data);
    }

    public function downloadPdf($id)
    {
        $data['data'] = KategoriItem::with(['items' => fn($query) => $query->orderBy('kode')])->findOrFail($id);
        $data['dicetak_pada'] = now()->timezone('Asia/Jakarta');

        return Pdf::loadView('kategori_items.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->download('kategori-' . Str::slug($data['data']->kode) . '.pdf');
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
        $data_kategori = $method == 'new' ? new KategoriItem : KategoriItem::findOrFail($id);

        $request->validate([
            'kode' => ['required', 'max:50', Rule::unique('kategori_items', 'kode')->ignore($data_kategori->id)],
            'nama' => 'required|max:255',
        ]);

        $data_kategori->kode = $request->kode;
        $data_kategori->nama = $request->nama;
        $data_kategori->save();

        return redirect('kategori-items/view/' . $data_kategori->id);
    }

    public function delete($id)
    {
        KategoriItem::findOrFail($id)->delete();
        return redirect('kategori-items');
    }
}
