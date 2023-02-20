<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\PDF;
use App\Models\jenissurat;
use App\Models\Sifatsurat;
use App\Models\Suratkeluar;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Writer\PDF as WriterPDF;

use function PHPUnit\Framework\returnSelf;

// use Cviebrock\EloquentSluggable\Services\SlugService;
// use Illuminate\Validation\Rules\Enum;

class SuratKeluarController extends Controller
{

    /** Index show surat keluar **/
    public function index()
    {
        return view('dashboard.suratkeluar.index', [
            'suratkeluar' => Suratkeluar::where('user_id', auth()->user()->id)->get()
        ]);
    }

    /** Cetak Surat / Cetak Laporan Surat **/
    public function cetakSurat()
    {
        $suratkeluar = Suratkeluar::where('user_id', auth()->user()->id)->get();
        return view('dashboard.suratkeluar.cetak_surat', compact('suratkeluar'));
    }

    /** Tampilkan view buat Surat keluar **/
    public function create()
    {
        return view('dashboard.suratkeluar.create', [
            'jenissurats' => jenissurat::all(),
            'sifat' => Sifatsurat::all()
        ]);
    }
    
    // !-- Buat Surat Keluar --!
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'no_surat_keluar' => 'nullable',
            'tgl_surat_keluar' => 'required|date',
            'penerima_surat' => 'required|max:12|min:3',
            'sifat_id' => 'required',
            'jenissurat_id' => 'required',
            'lampiran' => 'nullable',
            'status' => 'nullable'
        ]);
        $validatedData['user_id'] = auth()->user()->id;
        $validatedData['perihal'] = Str::limit(strip_tags($request->perihal), 225,);
        Suratkeluar::create($validatedData);
        return redirect('/dashboard/suratkeluar')->with('success','Surat Keluar berhasil terbuat !!!');
    }

    /** Show detail surat keluar **/
    public function show(Suratkeluar $suratkeluar)
    {
        return view('dashboard.suratkeluar.show',[
            'surat' => $suratkeluar
        ]);
    }

    /** Edit surat controller **/  
    public function edit(Suratkeluar $suratkeluar)
    {
        return view('dashboard.suratkeluar.edit', [
            'surat' => $suratkeluar,
            'jenissurats' => jenissurat::all(),
            'sifat' => Sifatsurat::all()
        ]);
    }

    /** Update Surat Keluar **/
    public function update(Request $request, Suratkeluar $suratkeluar)
    {
        $rules =[
            'no_surat_keluar' => 'nullable',
            'tgl_surat_keluar' => 'required',
            'lampiran' => 'nullable',
            'penerima_surat' => 'required',
            'sifat_id' => 'required',
            'jenissurat_id' => 'required'
        ];
        $validatedData = $request->validate($rules);
        $validatedData['user_id'] = auth()->user()->id;
        $validatedData['perihal'] = Str::limit(strip_tags($request->perihal), 225);
        Suratkeluar::where('id', $suratkeluar->id)
                    ->update($validatedData);
        return redirect('/dashboard/suratkeluar')->with('warning','Surat Keluar berhasil terbuat !!!');
    }

    /** Hapus surat keluar **/
    public function destroy(Suratkeluar $suratkeluar)
    {
        Suratkeluar::destroy($suratkeluar->id);
        return redirect('/dashboard/suratkeluar')->with('danger','Data surat keluar berhasil terhapus !!!');
    }

    /** Export ke Word **/ 
    public function WordExport(Suratkeluar $suratkeluar)
    {
        // return view('dashboard.suratkeluar.pdf', [
        //     'surat' => $suratkeluar
        // ]);
    }

    /** Export surat ke PDF **/
    public function pdfExport(Suratkeluar $suratkeluar)
    {

        // return view('dashboard.suratkeluar.surat-pdf',[
        //     'surat' => $suratkeluar
        // ]);

        $suratkeluar = Suratkeluar::select("*")->get();

        $data = $suratkeluar->toArray();
        dd($data);
    }
    
    // public function KodeSurat(Request $request)
    // {
    //     $no_surat_keluar = SlugService::createSlug(Suratkeluar::class, 'no_surat_keluar', $request->jenissurat_id );
    //     return response()->json(['no_surat_keluar' => $no_surat_keluar]);
    // }
}