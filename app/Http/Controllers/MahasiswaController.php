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

        MahasiswaLog::create([
            'action'     => 'create',
            'data'       => $mhs->toArray(),
            'created_at' => now(),
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

    // public function vectorSearch(Request $request)
    // {
    //     $request->validate([
    //         'text' => 'required|string',
    //     ]);

    //     $url = env('SUPABASE_URL') . '/functions/v1/swift-api';
    //     $key = env('SUPABASE_KEY');

    //     $response = Http::withHeaders([
    //         'apikey' => $key,
    //     ])->post($url, [
    //         'text' => $request->text,
    //     ]);

    //     return response()->json(
    //         $response->json(),
    //         $response->status()
    //     );
    // }


// public function vectorSearch(Request $request)
// {
//     $request->validate([
//         'text' => 'required|string',
//     ]);

//     $baseUrl = rtrim(env('SUPABASE_URL'), '/');
//     $key = env('SUPABASE_KEY');
//     $pythonUrl = rtrim(env('PYTHON_API_URL'), '/');

//     // 1. Ubah pertanyaan menjadi embedding melalui Python
//     $python = Http::post($pythonUrl . '/embed', [
//         'text' => $request->text,
//     ]);

//     if ($python->failed()) {
//         return response()->json([
//             'error' => $python->body(),
//         ], $python->status());
//     }

//     $embedding = $python->json('embedding');
//     $vector = '[' . implode(',', $embedding) . ']';

//     // 2. Cari mahasiswa melalui fungsi SQL Supabase
//     $search = Http::withHeaders([
//         'apikey' => $key,
//         'Authorization' => 'Bearer ' . $key,
//     ])->post(
//         $baseUrl . '/rest/v1/rpc/match_mahasiswa',
//         [
//             'query_embedding' => $vector,
//             'match_threshold' => 0.3,
//             'match_count' => 3,
//         ]
//     );

//     if ($search->failed()) {
//         return response()->json([
//             'error' => $search->body(),
//         ], $search->status());
//     }

//     return response()->json([
//         'query' => $request->text,
//         'results' => $search->json(),
//     ]);
// }


public function vectorSearch(Request $request)
{
    $request->validate([
        'text' => 'required|string',
    ]);

    $baseUrl = rtrim(env('SUPABASE_URL'), '/');
    $key = env('SUPABASE_KEY');
    $pythonUrl = rtrim(env('PYTHON_API_URL'), '/');

    // 1. Generate embedding pertanyaan melalui Python
    $python = Http::post($pythonUrl . '/embed', [
        'text' => $request->text,
    ]);

    if ($python->failed()) {
        return response()->json([
            'error' => 'Gagal menghasilkan embedding',
            'details' => $python->body(),
        ], $python->status());
    }

    $embedding = $python->json('embedding');

    if (!is_array($embedding) || count($embedding) !== 384) {
        return response()->json([
            'error' => 'Embedding tidak valid atau dimensinya bukan 384',
        ], 500);
    }

    $vector = '[' . implode(',', $embedding) . ']';

    // 2. Jalankan similarity search di Supabase
    $search = Http::withHeaders([
        'apikey' => $key,
        'Authorization' => 'Bearer ' . $key,
    ])->post(
        $baseUrl . '/rest/v1/rpc/match_mahasiswa',
        [
            'query_embedding' => $vector,
            'match_threshold' => -1,
            'match_count' => 3,
        ]
    );

    if ($search->failed()) {
        return response()->json([
            'error' => 'Gagal melakukan similarity search',
            'details' => $search->body(),
        ], $search->status());
    }

    // 3. Kembalikan hasil pencarian
    return response()->json([
        'query' => $request->text,
        'results' => $search->json(),
    ]);
}


    // public function generateEmbedding()
    // {
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
    //         'apikey' => env('SUPABASE_KEY'),
    //     ])->post(
    //         env('SUPABASE_URL') . '/functions/v1/rapid-responder'
    //     );

    //     return response()->json($response->json(), $response->status());
    // }

    
public function generateEmbedding()
{
    $baseUrl = rtrim(env('SUPABASE_URL'), '/');
    $key = env('SUPABASE_KEY');
    $pythonUrl = rtrim(env('PYTHON_API_URL'), '/');

    $response = Http::withHeaders([
        'apikey' => $key,
        'Authorization' => 'Bearer ' . $key,
    ])->get($baseUrl . '/rest/v1/mahasiswas', [
        'select' => 'id,nama,hobi',
        'embedding' => 'is.null',
        'hobi' => 'not.is.null',
        'limit' => 100,
    ]);

    if ($response->failed()) {
        return response()->json([
            'error' => $response->body(),
        ], $response->status());
    }

    $results = [];

    foreach ($response->json() as $mhs) {
        $text = trim($mhs['hobi'] ?? '');

        if ($text === '') {
            continue;
        }

        $python = Http::post($pythonUrl . '/embed', [
            'text' => $text,
        ]);

        if ($python->failed()) {
            $results[] = [
                'id' => $mhs['id'],
                'status' => 'gagal generate',
            ];
            continue;
        }

        $embedding = $python->json('embedding');
        $vector = '[' . implode(',', $embedding) . ']';

        $update = Http::withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer ' . $key,
            'Prefer' => 'return=minimal',
        ])->patch(
            $baseUrl . '/rest/v1/mahasiswas?id=eq.'
                . urlencode((string) $mhs['id']),
            ['embedding' => $vector]
        );

        $results[] = [
            'id' => $mhs['id'],
            'nama' => $mhs['nama'],
            'status' => $update->successful()
                ? 'berhasil'
                : 'gagal menyimpan',
        ];
    }

    return response()->json([
        'message' => 'Proses embedding selesai',
        'results' => $results,
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
