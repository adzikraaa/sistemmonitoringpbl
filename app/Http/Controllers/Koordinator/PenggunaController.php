<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Koordinator;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    /**
     * Tampilkan daftar master data pengguna dengan filter peran, pencarian, dan status akun.
     */
    public function index(Request $request)
    {
        $roleFilter   = $request->get('role', 'semua');
        $statusFilter = $request->get('status', 'semua');
        $search       = $request->get('search');

        // ── 1. Query Mahasiswa ──
        $mhsQuery = Mahasiswa::with('kelas');
        if ($search) {
            $mhsQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($statusFilter !== 'semua') {
            $mhsQuery->where('is_aktif', $statusFilter === 'aktif');
        }
        $mahasiswaList = in_array($roleFilter, ['dosen', 'admin']) ? collect() : $mhsQuery->get();

        // ── 2. Query Dosen ──
        $dsnQuery = Dosen::with(['dosenPengampu.mataKuliah', 'kelompok']);
        if ($search) {
            $dsnQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nidn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($statusFilter !== 'semua') {
            $dsnQuery->where('is_aktif', $statusFilter === 'aktif');
        }
        $dosenList = in_array($roleFilter, ['mahasiswa', 'admin']) ? collect() : $dsnQuery->get();

        // ── 3. Query Koordinator (Admin) ──
        $krdQuery = Koordinator::query();
        if ($search) {
            $krdQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nidn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($statusFilter === 'nonaktif') {
            $koordinatorList = collect();
        } else {
            $koordinatorList = in_array($roleFilter, ['mahasiswa', 'dosen']) ? collect() : $krdQuery->get();
        }

        // ── 4. Standardisasi Objek Pengguna ──
        $allUsers = collect();

        foreach ($mahasiswaList as $mhs) {
            $allUsers->push((object)[
                'id'         => $mhs->id,
                'identifier' => $mhs->nim,
                'nama'       => $mhs->nama,
                'email'      => $mhs->email,
                'role_key'   => 'mahasiswa',
                'role_label' => 'Mahasiswa',
                'konteks'    => $mhs->kelas ? 'Kelas ' . $mhs->kelas->nama_kelas : 'Belum Ada Kelas',
                'kelas_id'   => $mhs->kelas_id,
                'is_aktif'   => (bool) $mhs->is_aktif,
                'created_at' => $mhs->created_at,
            ]);
        }

        foreach ($dosenList as $dsn) {
            $mkCount  = $dsn->dosenPengampu->pluck('mata_kuliah_id')->unique()->count();
            $klpCount = $dsn->kelompok->count();
            $konteks  = $mkCount > 0 ? "Pengampu {$mkCount} MK" : 'Dosen Pembimbing';
            if ($klpCount > 0) {
                $konteks .= " · {$klpCount} Kelompok";
            }

            $allUsers->push((object)[
                'id'         => $dsn->id,
                'identifier' => $dsn->nidn ?? ('DSN-' . str_pad($dsn->id, 3, '0', STR_PAD_LEFT)),
                'nama'       => $dsn->nama,
                'email'      => $dsn->email,
                'role_key'   => 'dosen',
                'role_label' => 'Dosen',
                'konteks'    => $konteks,
                'kelas_id'   => null,
                'is_aktif'   => (bool) $dsn->is_aktif,
                'created_at' => $dsn->created_at,
            ]);
        }

        foreach ($koordinatorList as $krd) {
            $allUsers->push((object)[
                'id'         => $krd->id,
                'identifier' => $krd->nidn ?? ('ADM-' . str_pad($krd->id, 3, '0', STR_PAD_LEFT)),
                'nama'       => $krd->nama,
                'email'      => $krd->email,
                'role_key'   => 'admin',
                'role_label' => 'Admin',
                'konteks'    => 'Administrator Program',
                'kelas_id'   => null,
                'is_aktif'   => true,
                'created_at' => $krd->created_at,
            ]);
        }

        // Urutkan berdasarkan nama
        $allUsers = $allUsers->sortBy('nama')->values();

        // ── 5. Pagination Manual untuk Koleksi Terpadu ──
        $perPage     = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemsSlice  = $allUsers->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $users = new LengthAwarePaginator($itemsSlice, $allUsers->count(), $perPage, $currentPage, [
            'path'  => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        // Hitung total per peran untuk badge tab filter
        $countSemua     = Mahasiswa::count() + Dosen::count() + Koordinator::count();
        $countMahasiswa = Mahasiswa::count();
        $countDosen     = Dosen::count();
        $countAdmin     = Koordinator::count();

        // Ambil daftar kelas untuk form penempatan mahasiswa
        $kelasList = Kelas::where('is_aktif', true)->orderBy('nama_kelas')->get();

        $allUsersForSelect = $allUsers;

        return view('koordinator.master-data.pengguna.index', compact(
            'users',
            'roleFilter',
            'statusFilter',
            'countSemua',
            'countMahasiswa',
            'countDosen',
            'countAdmin',
            'kelasList',
            'allUsersForSelect'
        ));
    }

    /**
     * Simpan pengguna baru berdasarkan peran.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role'       => 'required|in:mahasiswa,dosen,admin',
            'nama'       => 'required|string|max:100',
            'identifier' => 'required|string|max:30',
            'email'      => 'required|email|max:100',
            'password'   => 'required|string|min:6',
            'kelas_id'   => 'nullable|exists:kelas,id',
        ]);

        $role = $request->role;

        if ($role === 'mahasiswa') {
            if (Mahasiswa::where('nim', $request->identifier)->orWhere('email', $request->email)->exists()) {
                return back()->withInput()->with('error', 'NIM atau Email mahasiswa sudah terdaftar.');
            }

            Mahasiswa::create([
                'nama'     => $request->nama,
                'nim'      => $request->identifier,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'kelas_id' => $request->kelas_id,
                'is_aktif' => true,
            ]);
        } elseif ($role === 'dosen') {
            if (Dosen::where('nidn', $request->identifier)->orWhere('email', $request->email)->exists()) {
                return back()->withInput()->with('error', 'NIDN atau Email dosen sudah terdaftar.');
            }

            Dosen::create([
                'nama'     => $request->nama,
                'nidn'     => $request->identifier,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'is_aktif' => true,
            ]);
        } else {
            if (Koordinator::where('email', $request->email)->orWhere('nidn', $request->identifier)->exists()) {
                return back()->withInput()->with('error', 'NIDN atau Email admin sudah terdaftar.');
            }

            Koordinator::create([
                'nama'     => $request->nama,
                'nidn'     => $request->identifier,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('koordinator.master-data.pengguna')
            ->with('success', "Pengguna {$request->nama} ({$role}) berhasil ditambahkan.");
    }

    /**
     * Update data pengguna.
     */
    public function update(Request $request, string $role, int $id)
    {
        $request->validate([
            'nama'       => 'required|string|max:100',
            'identifier' => 'required|string|max:30',
            'email'      => 'required|email|max:100',
            'kelas_id'   => 'nullable|exists:kelas,id',
        ]);

        if ($role === 'mahasiswa') {
            $user = Mahasiswa::findOrFail($id);
            $user->update([
                'nama'     => $request->nama,
                'nim'      => $request->identifier,
                'email'    => $request->email,
                'kelas_id' => $request->kelas_id,
            ]);
        } elseif ($role === 'dosen') {
            $user = Dosen::findOrFail($id);
            $user->update([
                'nama'  => $request->nama,
                'nidn'  => $request->identifier,
                'email' => $request->email,
            ]);
        } elseif ($role === 'admin') {
            $user = Koordinator::findOrFail($id);
            $user->update([
                'nama'  => $request->nama,
                'nidn'  => $request->identifier,
                'email' => $request->email,
            ]);
        } else {
            abort(404);
        }

        return redirect()->route('koordinator.master-data.pengguna')
            ->with('success', "Data pengguna {$user->nama} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / nonaktif akun.
     */
    public function toggleStatus(string $role, int $id)
    {
        if ($role === 'mahasiswa') {
            $user = Mahasiswa::findOrFail($id);
            $user->is_aktif = !$user->is_aktif;
            $user->save();
        } elseif ($role === 'dosen') {
            $user = Dosen::findOrFail($id);
            $user->is_aktif = !$user->is_aktif;
            $user->save();
        } else {
            return back()->with('error', 'Status akun Admin Koordinator tidak dapat dinonaktifkan.');
        }

        $statusStr = $user->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('koordinator.master-data.pengguna')
            ->with('success', "Akun {$user->nama} berhasil {$statusStr}.");
    }

    /**
     * Reset kata sandi pengguna ke default atau input baru.
     */
    public function resetPassword(Request $request, string $role, int $id)
    {
        $newPass = $request->input('password', 'password123');

        if ($role === 'mahasiswa') {
            $user = Mahasiswa::findOrFail($id);
        } elseif ($role === 'dosen') {
            $user = Dosen::findOrFail($id);
        } elseif ($role === 'admin') {
            $user = Koordinator::findOrFail($id);
        } else {
            abort(404);
        }

        $user->password = Hash::make($newPass);
        $user->save();

        return redirect()->route('koordinator.master-data.pengguna')
            ->with('success', "Kata sandi untuk {$user->nama} berhasil direset.");
    }

    /**
     * Reset kata sandi umum dari dropdown pilihan akun.
     */
    public function resetPasswordGeneral(Request $request)
    {
        $request->validate([
            'target_user' => 'required|string',
            'password'    => 'required|min:6',
        ]);

        $parts = explode(':', $request->input('target_user'));
        if (count($parts) !== 2) {
            return back()->with('error', 'Format pengguna tidak valid.');
        }

        [$role, $id] = $parts;
        return $this->resetPassword($request, $role, (int) $id);
    }

    /**
     * Simulasi undang pengguna melalui email kampus.
     */
    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role'  => 'required|in:mahasiswa,dosen,admin',
        ]);

        return redirect()->route('koordinator.master-data.pengguna')
            ->with('success', "Undangan aktivasi akun berhasil dikirim ke {$request->email} sebagai {$request->role}.");
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(string $role, int $id)
    {
        if ($role === 'mahasiswa') {
            $user = Mahasiswa::findOrFail($id);
            $nama = $user->nama;
            $user->delete();
        } elseif ($role === 'dosen') {
            $user = Dosen::findOrFail($id);
            $nama = $user->nama;
            $user->delete();
        } elseif ($role === 'admin') {
            $user = Koordinator::findOrFail($id);
            if (Koordinator::count() <= 1) {
                return back()->with('error', 'Tidak dapat menghapus satu-satunya akun Koordinator.');
            }
            $nama = $user->nama;
            $user->delete();
        } else {
            abort(404);
        }

        return redirect()->route('koordinator.master-data.pengguna')
            ->with('success', "Pengguna {$nama} berhasil dihapus dari sistem.");
    }
}