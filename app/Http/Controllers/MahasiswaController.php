<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Data kelompok & user (hardcoded untuk prototype).
     * Saat backend lengkap, data ini diambil dari DB/session.
     */
    private function sharedViewData(): array
    {
        return [
            'userName'  => 'Mochammad Tanggaq',
            'userProdi' => 'Teknologi Informasi',
        ];
    }

    public function dashboard()
    {
        return view('mahasiswa.dashboard', $this->sharedViewData());
    }

    public function kelompok()
    {
        return view('mahasiswa.kelompok', $this->sharedViewData());
    }

    public function proposal()
    {
        return view('mahasiswa.proposal', $this->sharedViewData());
    }

    public function logbook()
    {
        return view('mahasiswa.logbook', $this->sharedViewData());
    }

    public function milestone()
    {
        return view('mahasiswa.milestone', $this->sharedViewData());
    }

    /**
     * Store Proposal (backend stub).
     *
     * Endpoint ini siap menerima data dari form modal.
     * Saat ini mengembalikan JSON sukses untuk modal JS.
     * Implementasi penyimpanan ke DB dapat ditambahkan
     * setelah migrasi proposal table tersedia.
     */
    public function storeProposal(Request $request)
    {
        $validated = $request->validate([
            'judul'          => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'latar_belakang' => 'required|string',
            'tujuan'         => 'required|string',
            'metode'         => 'required|string',
        ]);

        // TODO: simpan ke database setelah tabel proposals tersedia
        // Proposal::create([...$validated, 'kelompok_id' => ..., 'status' => 'diajukan']);

        return response()->json([
            'success' => true,
            'message' => 'Proposal berhasil diajukan.',
            'status'  => 'diajukan',
        ]);
    }
}
