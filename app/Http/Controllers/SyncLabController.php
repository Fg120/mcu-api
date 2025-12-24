<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncLabController extends Controller
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->baseUrl = env('MCU_URL', 'https://mcu.test');
        $this->clientId = env('MCU_API_KEY', 'cli_uuuk1r0dushrow5gg78c');
        $this->clientSecret = env('MCU_API_SECRET', 'sec_E2iIVHHQq1k89vyInu0OLNnRHBjpT85LzKSQmpH81zU2BkJe2Q054uJKLGTqc9w9');
    }

    /**
     * Kirim data laboratorium ke API eksternal (endpoint /api/sync_lab)
     * 
     * Format field menggunakan kode_rsuk dan kode_rsubh untuk identifikasi.
     * Minimal salah satu kode harus diisi (kode_rsuk atau kode_rsubh).
     */
    public function syncToExternalApi()
    {
        try {
            $baseUrl = $this->baseUrl;
            $clientId = $this->clientId;
            $clientSecret = $this->clientSecret;

            // Contoh payload dengan kode_rsuk dan kode_rsubh
            $data = [
                [
                    'kategori' => 'Laboratorium 1',
                    'kode_rsuk' => 'LAB-RSUK-001',      // Kode untuk RSUK (nullable, minimal salah satu)
                    'kode_rsubh' => null,               // Kode untuk RSUBH (nullable, minimal salah satu)
                    'layanan' => [
                        [
                            'kode_rsuk' => 'LB-HB-001',     // Kode layanan RSUK
                            'kode_rsubh' => 'LB-HB-001-BH', // Kode layanan RSUBH (boleh null)
                            'nama' => 'Hemoglobin',
                            'tipe' => 'integer',
                            'satuan' => 'g/dL',
                            'nilai_rujukan' => '13-17',
                            'batas' => [
                                [
                                    'gender' => 'laki-laki',
                                    'usia_min' => 18,
                                    'usia_max' => 65,
                                    'batas_bawah' => 13,
                                    'batas_atas' => 17,
                                ],
                                [
                                    'gender' => 'perempuan',
                                    'usia_min' => 18,
                                    'usia_max' => 65,
                                    'batas_bawah' => 12,
                                    'batas_atas' => 15,
                                ],
                            ],
                        ],
                        [
                            'kode_rsuk' => 'LB-WBC-002',
                            'kode_rsubh' => null,           // Hanya punya kode RSUK
                            'nama' => 'WBC',
                            'tipe' => 'integer',
                            'satuan' => '/uL',
                            'nilai_rujukan' => '4000-11000',
                            'batas' => [
                                [
                                    'gender' => null,
                                    'usia_min' => 18,
                                    'usia_max' => 65,
                                    'batas_bawah' => 4000,
                                    'batas_atas' => 11000,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'kategori' => 'Laboratorium 2',
                    'kode_rsuk' => null,                    // Hanya punya kode RSUBH
                    'kode_rsubh' => 'LAB-RSUBH-002',
                    'layanan' => [
                        [
                            'kode_rsuk' => null,
                            'kode_rsubh' => 'LB-GLC-101',   // Hanya punya kode RSUBH
                            'nama' => 'Glukosa Puasa',
                            'tipe' => 'integer',
                            'satuan' => 'mg/dL',
                            'nilai_rujukan' => '70-100',
                        ],
                        [
                            'kode_rsuk' => 'LB-GLC-102',
                            'kode_rsubh' => 'LB-GLC-102-BH',
                            'nama' => 'Glukosa 2 Jam PP',
                            'tipe' => 'integer',
                            'satuan' => 'mg/dL',
                            'nilai_rujukan' => '<140',
                        ],
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Client-Id' => $clientId,
                'X-Client-Secret' => $clientSecret,
            ])->post($baseUrl . '/api/sync_lab', $data);

            if ($response->successful()) {
                Log::info('Data lab berhasil dikirim ke API eksternal', $response->json());

                return response()->json([
                    'status' => 'sukses',
                    'message' => 'Data tersinkronisasi ke API eksternal',
                    'response_server' => $response->json(),
                ]);
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            Log::error('Gagal mengirim data lab', [
                'error' => $e->getMessage(),
                'response' => $e->response?->json(),
            ]);

            return response()->json([
                'status' => 'gagal',
                'message' => 'Gagal mengirim data ke API eksternal',
                'error' => $e->getMessage(),
                'response_server' => $e->response?->json(),
            ], 500);
        }
    }

    /**
     * Contoh kirim hasil lab ke API eksternal (endpoint /api/hasil_lab)
     * 
     * Format menggunakan kode_rsuk/kode_rsubh untuk identifikasi laboratorium dan layanan.
     * Minimal salah satu kode harus diisi.
     */
    public function sendHasilLabExample()
    {
        try {
            $baseUrl = $this->baseUrl;
            $clientId = $this->clientId;
            $clientSecret = $this->clientSecret;

            // Contoh payload hasil lab dengan kode_rsuk dan kode_rsubh
            $data = [
                'unit_id' => 1,
                'nik' => '1234567890123456',
                'no_mr' => 'MR2303',
                'waktu_periksa' => '2025-12-22 08:30:00',
                'kode_rsuk' => 'LAB-RSUK-001',      // Kode laboratorium RSUK (atau kode_rsubh)
                'kode_rsubh' => null,               // Bisa juga menggunakan kode_rsubh
                'penanggungjawab' => 'dr. Amin',
                'dokter_pengirim' => 'dr. Budi',
                'petugas_lab' => 'Rina',
                'ruang' => 'Ruang Lab A',
                'no_periksa' => 'LAB-2025-001',
                'hasil_pemeriksaan' => [
                    [
                        'kode_rsuk' => 'LB-HB-001',     // Kode layanan RSUK (atau kode_rsubh)
                        'kode_rsubh' => null,           // Minimal salah satu harus diisi
                        'hasil' => '14.5',
                        'status' => 'Normal',
                        'keterangan' => 'Dalam batas normal'
                    ],
                    [
                        'kode_rsuk' => null,
                        'kode_rsubh' => 'LB-GLC-101',   // Menggunakan kode RSUBH
                        'hasil' => '95',
                        'status' => 'Normal',
                        'keterangan' => 'GDP normal'
                    ],
                ],
                'keterangan' => 'Pemeriksaan rutin lengkap',
                'status' => 'Selesai'
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Client-Id' => $clientId,
                'X-Client-Secret' => $clientSecret,
            ])->post($baseUrl . '/api/hasil_lab', $data);

            if ($response->successful()) {
                Log::info('Hasil lab berhasil dikirim ke API eksternal', $response->json());

                return response()->json([
                    'status' => 'sukses',
                    'message' => 'Hasil lab berhasil dikirim',
                    'response_server' => $response->json(),
                ]);
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            Log::error('Gagal mengirim hasil lab', [
                'error' => $e->getMessage(),
                'response' => $e->response?->json(),
            ]);

            return response()->json([
                'status' => 'gagal',
                'message' => 'Gagal mengirim hasil lab ke API eksternal',
                'error' => $e->getMessage(),
                'response_server' => $e->response?->json(),
            ], 500);
        }
    }
}
