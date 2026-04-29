<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\TarifPembayaran;
use App\Models\Pengguna;
use App\Models\DataSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function index()
    {
        return Admin::with('pengguna')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna'
        ]);

        return Admin::create($validated);
    }

    public function show($id)
    {
        return Admin::with('pengguna')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $admin->update($request->all());
        return $admin;
    }

    public function destroy($id)
    {
        return Admin::destroy($id);
    }

    public function loginSubmit(Request $request)
    {
        $credentials = $request->validate([
            'nis' => 'required',
            'password' => 'required'
        ]);

        if ($request->nis === 'admin' && $request->password === 'admin123') {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Username atau password salah.');
    }

    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $totalSiswa = Siswa::count();

        $tarif = TarifPembayaran::value('nominal');
        $tarifNumber = (int) str_replace(['Rp', '.', ',', ' '], '', $tarif);
        $totalTarifPerSiswa = $tarifNumber;
        // $totalTagihan = $totalSiswa * $totalTarifPerSiswa;

        $pembayaranTerbaru = Siswa::leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'siswa.id_pengguna')
            ->leftJoin('pembayaran', 'pembayaran.nis', '=', 'siswa.nis')
            ->select(
                'siswa.nis',
                'pengguna.nama_pengguna as nama',
                'pembayaran.tanggal_bayar',
                'pembayaran.status'
            )
            ->orderBy('pengguna.nama_pengguna', 'asc')
            ->get()
            ->map(function ($p) use ($tarif) {
                $p->jumlah = $tarif;
                if (!$p->status) {
                    $p->status = 'Belum Lunas';
                }
                return $p;
            });

        $lunas = Pembayaran::where('status', 'Lunas')->count();
        $belumLunas = $totalSiswa - $lunas;
        $totalTagihan = $belumLunas * $totalTarifPerSiswa;

        return view('admin.dashboard', compact(
            'pembayaranTerbaru',
            'totalSiswa',
            'lunas',
            'belumLunas',
            'totalTarifPerSiswa',
            'totalTagihan'
        ));
    }

    public function dataSiswa()
    {
        $siswa = Siswa::leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'siswa.id_pengguna')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.id_kelas')
            ->select('siswa.*', 'pengguna.nama_pengguna as nama_pengguna', 'kelas.nama_kelas as nama_kelas')
            ->orderBy('nama_pengguna', 'asc')
            ->get();

        $kelas = Kelas::all();

        return view('admin.data-siswa', compact('siswa', 'kelas'));
    }

    public function storeSiswa(Request $request)
    {
        Log::info('storeSiswa called', $request->all());

        $request->validate([
            'name' => 'required|string|max:100',
            'nis' => 'required|string|unique:siswa,nis',
            'tanggal_lahir' => 'nullable|date',
            'kelas' => 'required|exists:kelas,id',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
            'password' => 'required|string|min:6'
        ]);

        $pengguna = Pengguna::create([
            'nama_pengguna' => $request->name,
            'username' => $request->nis,
            'password' => bcrypt($request->password),
            'role' => 'siswa'
        ]);

        Siswa::create([
            'nis' => $request->nis,
            'id_pengguna' => $pengguna->id_pengguna,
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_kelas' => $request->kelas,
            'nomor_telepon' => $request->telepon,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $dataSiswa = DataSiswa::create([
            'nis' => $request->nis,
            'nama' => $request->name,
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_kelas' => $request->kelas,
            'nomor_telepon' => $request->telepon,
            'email' => $request->email,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $kelasModel = Kelas::find($request->kelas);
            return response()->json([
                'nis' => $request->nis,
                'nama' => $request->name,
                'nama_kelas' => $kelasModel ? $kelasModel->nama_kelas : null,
                'nomor_telepon' => $request->telepon,
                'email' => $request->email
            ], 201);
        }

        return redirect()->route('admin.dataSiswa')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function editSiswa($nis)
    {
        $siswa = Siswa::with(['pengguna', 'kelas'])->where('nis', $nis)->firstOrFail();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'nis' => $siswa->nis,
                'nama' => $siswa->pengguna->nama_pengguna ?? '',
                'tanggal_lahir' => $siswa->tanggal_lahir,
                'id_kelas' => $siswa->id_kelas,
                'email' => $siswa->email,
                'telepon' => $siswa->nomor_telepon,
                'id_pengguna' => $siswa->id_pengguna
            ]);
        }

        return response()->json($siswa);
    }

    public function updateSiswa(Request $request, $nis)
    {
        Log::info('updateSiswa called', ['nis' => $nis, 'data' => $request->all()]);

        $request->validate([
            'name' => 'required|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'kelas' => 'required|string',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
            'password' => 'nullable|string|min:6'
        ]);

        $siswa = Siswa::where('nis', $nis)->firstOrFail();

        // Update Pengguna
        if ($siswa->id_pengguna) {
            $penggunaData = [
                'nama_pengguna' => $request->name,
            ];

            if ($request->filled('password')) {
                $penggunaData['password'] = bcrypt($request->password);
            }

            Pengguna::where('id_pengguna', $siswa->id_pengguna)->update($penggunaData);
        }

        // Update Siswa
        $siswaData = [
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_kelas' => $request->kelas,
            'nomor_telepon' => $request->telepon,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $siswaData['password'] = bcrypt($request->password);
        }

        $siswa->update($siswaData);

        // Update DataSiswa
        DataSiswa::where('nis', $nis)->update([
            'nama' => $request->name,
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_kelas' => $request->kelas,
            'nomor_telepon' => $request->telepon,
            'email' => $request->email,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $kelasModel = Kelas::where('id', $request->kelas)->first();
            return response()->json([
                'nis' => $nis,
                'nama' => $request->name,
                'nama_kelas' => $kelasModel ? $kelasModel->nama_kelas : null,
                'nomor_telepon' => $request->telepon,
                'email' => $request->email
            ]);
        }

        return redirect()->route('admin.dataSiswa')->with('success', 'Data siswa berhasil diperbarui');
    }

    public function dataPembayaran()
    {
        $pembayaran = Siswa::leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'siswa.id_pengguna')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.id_kelas')
            ->leftJoin('pembayaran', 'pembayaran.nis', '=', 'siswa.nis')
            ->select(
                'siswa.nis',
                'pengguna.nama_pengguna as nama',
                'kelas.nama_kelas',
                'pembayaran.tanggal_bayar',
                'pembayaran.jumlah',
                'pembayaran.status'
            )
            ->orderBy('pengguna.nama_pengguna', 'asc')
            ->get()
            ->map(function ($p) {
                if (!$p->status) {
                    $p->status = 'Belum Lunas';
                    $p->tanggal_bayar = '-';
                }
                if (!$p->jumlah) {
                    $tarif = TarifPembayaran::value('nominal');
                    $tarifNumber = (int) str_replace(['Rp', '.', ',', ' '], '', $tarif);
                    $p->jumlah = 'Rp' . number_format($tarifNumber, 0, ',', '.');
                } else {
                    $p->jumlah = 'Rp' . number_format($p->jumlah, 0, ',', '.');
                }
                return $p;
            });

        $kelas = Kelas::all();
        return view('admin.data-pembayaran', compact('pembayaran', 'kelas'));
    }

    public function exportPembayaranXlsx()
    {
        $pembayaran = Siswa::leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'siswa.id_pengguna')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.id_kelas')
            ->leftJoin('pembayaran', 'pembayaran.nis', '=', 'siswa.nis')
            ->select(
                'siswa.nis',
                'pengguna.nama_pengguna as nama',
                'kelas.nama_kelas',
                'pembayaran.tanggal_bayar',
                'pembayaran.jumlah',
                'pembayaran.status'
            )
            ->orderBy('pengguna.nama_pengguna', 'asc')
            ->get();

        $csv = "NIS,Nama,Kelas,Tanggal,Jumlah,Status\n";
        foreach ($pembayaran as $p) {
            $status = $p->status ?? 'Belum Lunas';
            $tanggal = $p->tanggal_bayar ?? '-';
            $jumlah = $p->jumlah ?? TarifPembayaran::value('nominal');
            $csv .= "\"{$p->nis}\",\"{$p->nama}\",\"{$p->nama_kelas}\",\"{$tanggal}\",\"{$jumlah}\",\"{$status}\"\n";
        }

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="data_pembayaran_' . date('Y-m-d_His') . '.csv"');
    }

    public function exportPembayaranPdf()
    {
        $pembayaran = Siswa::leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'siswa.id_pengguna')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.id_kelas')
            ->leftJoin('pembayaran', 'pembayaran.nis', '=', 'siswa.nis')
            ->select(
                'siswa.nis',
                'pengguna.nama_pengguna as nama',
                'kelas.nama_kelas',
                'pembayaran.tanggal_bayar',
                'pembayaran.jumlah',
                'pembayaran.status'
            )
            ->orderBy('pengguna.nama_pengguna', 'asc')
            ->get()
            ->map(function ($p) {
                $p->status = $p->status ?? 'Belum Lunas';
                $p->tanggal_bayar = $p->tanggal_bayar ?? '-';
                $p->jumlah = $p->jumlah ?? TarifPembayaran::value('nominal');
                return $p;
            });

        $data = [
            'pembayaran' => $pembayaran,
            'tanggal' => date('d-m-Y H:i:s')
        ];

        $pdf = Pdf::loadView('admin.pdf.pembayaran', $data);
        return $pdf->download('data_pembayaran_' . date('Y-m-d_His') . '.pdf');
    }
}
