<?php
namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MahasiswaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::query();

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        return response()->json($query->get());
    }

    public function create(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim'      => 'required|numeric',
            'nama'     => 'required|string|max:255',
            'jurusan'  => 'required|string|max:255',
            'angkatan' => 'required|numeric',
            'foto'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fotoPath = null;

        if ($request->hasFile('foto')) {

        $foto = $request->file('foto');

        // Path file di Supabase Storage
        $fotoPath = 'mahasiswa/' .
            Str::uuid() . '.' .
            $foto->getClientOriginalExtension();

        $supabaseUrl = config('services.supabase.url');
        $supabaseKey = config('services.supabase.key');
        $bucket      = config('services.supabase.bucket');

        // Upload foto ke Supabase Storage
        $response = Http::withHeaders([
            'apikey'        => $supabaseKey,
            'Authorization' => 'Bearer ' . $supabaseKey,
            'Content-Type'  => $foto->getMimeType(),
        ])
        ->withBody(
            file_get_contents($foto->getRealPath()),
            $foto->getMimeType()
        )
        ->post(
            "{$supabaseUrl}/storage/v1/object/{$bucket}/{$fotoPath}"
        );

        // Jika upload ke Supabase gagal
        if ($response->failed()) {
            return response()->json([
                'message' => 'Foto gagal diunggah ke Supabase Storage',
                'error'   => $response->json(),
            ], $response->status());
        }
    }

    // Simpan data mahasiswa + path foto ke PostgreSQL
    $mhs = Mahasiswa::create([
        'nim'       => $validated['nim'],
        'nama'      => $validated['nama'],
        'jurusan'   => $validated['jurusan'],
        'angkatan'  => $validated['angkatan'],
        'foto_path' => $fotoPath,
    ]);

    return response()->json([
        'message' => 'Data mahasiswa berhasil ditambahkan',
        'data'    => $mhs,
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Mahasiswa::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        //
    }

    public function updateHobi(Request $request, $id)
    {
        $request->validate([
            'hobi' => 'required|string'
        ]);

        $mahasiswa = Mahasiswa::findOrFail($id);

        $mahasiswa->hobi = $request->hobi;
        $mahasiswa->save();

        return response()->json([
            'message' => 'Hobi berhasil diperbarui',
            'data' => $mahasiswa
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Mahasiswa::destroy($id);
        return response()->json(["message"=>"deleted"]);
    }
}
