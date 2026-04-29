<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\TarifPembayaran;
use App\Models\Pengguna;
use App\Services\AdminAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\SiswaAuthService;


class AdminController extends Controller
{
    public function __construct(
        private SiswaAuthService $siswaAuthService,
        private AdminAuthService $adminAuthService,
    ) {}


    public function dashboard()
    {
        if (!$this->adminAuthService->check()) {
            return redirect()->route('admin.login');
        }

        $totalSiswa      = Siswa::count();
        $tarif           = TarifPembayaran::value('nominal');
        $tarifNumber     = (int) str_replace(['Rp', '.', ',', ' '], '', $tarif);

        $pembayaranTerbaru = Siswa::leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'siswa.id_pengguna')
            ->leftJoin('pembayaran', 'pembayaran.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'pengguna.nama_pengguna as nama', 'pembayaran.tanggal_bayar', 'pembayaran.status')
            ->orderBy('pengguna.nama_pengguna', 'asc')
            ->get()
            ->map(function ($p) use ($tarif) {
                $p->jumlah = $tarif;
                $p->status = $p->status ?? 'Belum Lunas';
                return $p;
            });

        $lunas       = Pembayaran::where('status', 'Lunas')->count();
        $belumLunas  = $totalSiswa - $lunas;
        $totalTagihan = $belumLunas * $tarifNumber;

        return view('admin.dashboard', compact(
            'pembayaranTerbaru', 
            'totalSiswa', 
            'lunas',
            'belumLunas',
            'tarifNumber',
            'totalTagihan'
        ));
    }

    public function dataSiswa()
    {
        $siswa = Siswa::leftJoin('kelas', 'kelas.id', '=', 'siswa.id_kelas')
            ->select('siswa.*', 'kelas.nama_kelas as nama_kelas')
            ->orderBy('siswa.nama', 'asc')
            ->get();

        $kelas = Kelas::all();

        return view('admin.data-siswa', compact('siswa', 'kelas'));
    }


    public function storeSiswa(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'nis'           => 'required|string|unique:siswa,nis',
            'tanggal_lahir' => 'nullable|date',
            'kelas'         => 'required|exists:kelas,id',
            'email'         => 'nullable|email',
            'telepon'       => 'nullable|string',
            'password'      => 'required|string|min:6',
        ]);

        $siswa = $this->siswaAuthService->register([
            'name'          => $request->name,
            'nis'           => $request->nis,
            'tanggal_lahir' => $request->tanggal_lahir,
            'kelas'         => $request->kelas,
            'email'         => $request->email,
            'telepon'       => $request->telepon,
            'password'      => $request->password,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $kelasModel = Kelas::find($request->kelas);
            return response()->json([
                'nis'           => $siswa->nis,
                'nama'          => $siswa->nama,
                'nama_kelas'    => $kelasModel?->nama_kelas,
                'nomor_telepon' => $siswa->nomor_telepon,
                'email'         => $siswa->email,
            ], 201);
        }

        return redirect()->route('admin.dataSiswa')->with('success', 'Siswa berhasil ditambahkan');
    }


    public function editSiswa($nis)
    {
        $siswa = Siswa::with('kelas')->where('nis', $nis)->firstOrFail();

        return response()->json([
            'nis'           => $siswa->nis,
            'nama'          => $siswa->nama,
            'tanggal_lahir' => $siswa->tanggal_lahir,
            'id_kelas'      => $siswa->id_kelas,
            'email'         => $siswa->email,
            'telepon'       => $siswa->nomor_telepon,
        ]);
    }

    public function updateSiswa(Request $request, $nis)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'kelas'         => 'nullable|exists:kelas,id',
            'email'         => 'nullable|email',
            'telepon'       => 'nullable|string',
            'password'      => 'nullable|string|min:6',
        ]);

        $siswa = Siswa::where('nis', $nis)->firstOrFail();

        $data = [
            'nama'          => $request->name,
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_kelas'      => $request->kelas,
            'nomor_telepon' => $request->telepon,
            'email'         => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $siswa->update($data);

        if ($siswa->id_pengguna) {
            $penggunaData = ['nama_pengguna' => $request->name];
            if ($request->filled('password')) {
                $penggunaData['password'] = bcrypt($request->password);
            }
            Pengguna::where('id_pengguna', $siswa->id_pengguna)->update($penggunaData);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $kelasModel = Kelas::find($request->kelas);
            return response()->json([
                'nis'           => $siswa->nis,
                'nama'          => $siswa->nama,
                'nama_kelas'    => $kelasModel?->nama_kelas,
                'nomor_telepon' => $siswa->nomor_telepon,
                'email'         => $siswa->email,
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
            'tanggal'    => date('d-m-Y H:i:s')
        ];

        $pdf = Pdf::loadView('admin.pdf.pembayaran', $data);
        return $pdf->download('data_pembayaran_' . date('Y-m-d_His') . '.pdf');
    }
}