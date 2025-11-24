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
     * Menggunakan kunci field Bahasa Indonesia (utama) dan kompatibel jika masih memakai field bahasa Inggris.
     */
    public function syncToExternalApi()
    {
        try {
            $baseUrl = $this->baseUrl;
            $clientId = $this->clientId;
            $clientSecret = $this->clientSecret;

            // Contoh payload berbahasa Indonesia
            $data = [
                [
                    'kategori' => 'Laboratorium 1',
                    'layanan' => [
                        [
                            // tambahkan kode layanan supaya server bisa mencocokkan/identifikasi layanan
                            'kode_layanan' => 'LB-HB-001',
                            'nama' => 'Layanan 1',
                            'tipe' => 'integer',
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
                            'kode_layanan' => 'LB-WBC-002',
                            'nama' => 'Layanan 2',
                            'tipe' => 'integer',
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
                    'layanan' => [
                        [
                            'kode_layanan' => 'LB-GLC-101',
                            'nama' => 'Layanan A',
                            'tipe' => 'integer',
                        ],
                        [
                            'kode_layanan' => 'LB-GLC-102',
                            'nama' => 'Layanan B',
                            'tipe' => 'integer',
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
                // Log respons server (dalam Bahasa Indonesia jika server menggunakan terjemahan)
                Log::info('Data lab berhasil dikirim ke API eksternal', $response->json());

                // Menyesuaikan response yang dikembalikan ke pemanggil lokal
                return response()->json([
                    'status' => 'sukses',
                    'message' => 'Data tersinkronisasi ke API eksternal',
                    'response_server' => $response->json(),
                ]);
            }

            // Jika bukan HTTP 2xx, lempar exception untuk ditangani di catch
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
}
