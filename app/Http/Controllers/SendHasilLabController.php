<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendHasilLabController extends Controller
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->baseUrl = env('MCU_URL');
        $this->clientId = env('MCU_API_KEY');
        $this->clientSecret = env('MCU_API_SECRET');
    }

    public function sendHasilLab()
    {
        try {
            $data = [
                "unit_id" => 1,
                "nik" => "1234567890",
                "no_mr" => "MR2303",
                "tanggal_periksa" => "2025-11-04",
                "laboratorium_nama" => "Hematologi",
                "penanggungjawab" => "dr. Amin",
                "dokter_pengirim" => "dr. Budi",
                "petugas_lab" => "Rina",
                "ruang" => "Ruang Lab A",
                "no_periksa" => "LAB-2025-001",
                "hasil_pemeriksaan" => [
                    [
                        "nama_layanan" => "PCV",
                        "kode_layanan" => "HEMPCV",
                        "kode_hasil" => "PCV-01",
                        "hasil" => "500",
                        "satuan" => "mg/dL",
                        "nilai_rujukan" => "400-600",
                        "status" => "Tidak Normal",
                        "keterangan" => "amannn"
                    ]
                ],
                "keterangan" => "Pemeriksaan lengkap dengan file hasil",
                "status" => "Dalam Pemeriksaan"
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Client-Id' => $this->clientId,
                'X-Client-Secret' => $this->clientSecret,
            ])->post($this->baseUrl . '/api/hasil_lab', $data);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'response' => $response->json()
                ]);
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendMultipleHasilLab()
    {
        try {
            $hasilLabData = [
                [
                    "unit_id" => 1,
                    "nik" => "1234567890",
                    "no_mr" => "MR2303",
                    "tanggal_periksa" => "2025-11-04",
                    "laboratorium_nama" => "Hematologi",
                    "penanggungjawab" => "dr. Amin",
                    "dokter_pengirim" => "dr. Budi",
                    "petugas_lab" => "Rina",
                    "ruang" => "Ruang Lab A",
                    "no_periksa" => "LAB-2025-001",
                    "hasil_pemeriksaan" => [
                        [
                            "nama_layanan" => "PCV",
                            "kode_layanan" => "HEMPCV",
                            "kode_hasil" => "PCV-01",
                            "hasil" => "500",
                            "satuan" => "mg/dL",
                            "nilai_rujukan" => "400-600",
                            "status" => "Tidak Normal",
                            "keterangan" => "amannn"
                        ]
                    ],
                    "keterangan" => "Pasien 1",
                    "status" => "Selesai"
                ],
                [
                    "unit_id" => 1,
                    "nik" => "9876543210",
                    "no_mr" => "MR9999",
                    "tanggal_periksa" => "2025-11-04",
                    "laboratorium_nama" => "Hematologi",
                    "penanggungjawab" => "dr. Amin",
                    "dokter_pengirim" => "dr. Budi",
                    "petugas_lab" => "Rina",
                    "ruang" => "Ruang Lab A",
                    "no_periksa" => "LAB-2025-002",
                    "hasil_pemeriksaan" => [],
                    "keterangan" => "Pasien 2",
                    "status" => "Dalam Pemeriksaan"
                ]
            ];

            $results = [
                'success' => [],
                'failed' => []
            ];

            foreach ($hasilLabData as $data) {
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'X-Client-Id' => $this->clientId,
                        'X-Client-Secret' => $this->clientSecret,
                    ])->post($this->baseUrl . '/api/hasil_lab', $data);

                    if ($response->successful()) {
                        $results['success'][] = [
                            'nik' => $data['nik'],
                            'no_mr' => $data['no_mr'],
                            'response' => $response->json()
                        ];
                    } else {
                        $results['failed'][] = [
                            'nik' => $data['nik'],
                            'no_mr' => $data['no_mr'],
                            'error' => $response->json()
                        ];
                    }
                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'nik' => $data['nik'],
                        'no_mr' => $data['no_mr'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            Log::info('Batch hasil lab processing completed', [
                'total' => count($hasilLabData),
                'success' => count($results['success']),
                'failed' => count($results['failed'])
            ]);

            return response()->json([
                'status' => 'completed',
                'summary' => [
                    'total' => count($hasilLabData),
                    'success' => count($results['success']),
                    'failed' => count($results['failed'])
                ],
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Batch processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendMultipleHasilLabBulk()
    {
        try {
            $hasilLabData = [
                [
                    "unit_id" => 1,
                    "nik" => "1234567890",
                    "no_mr" => "MR2303",
                    "tanggal_periksa" => "2025-11-04",
                    "laboratorium_nama" => "Hematologi",
                    "penanggungjawab" => "dr. Amin",
                    "dokter_pengirim" => "dr. Budi",
                    "petugas_lab" => "Rina",
                    "ruang" => "Ruang Lab A",
                    "no_periksa" => "LAB-2025-001",
                    "hasil_pemeriksaan" => [
                        [
                            "nama_layanan" => "PCV",
                            "kode_layanan" => "HEMPCV",
                            "kode_hasil" => "PCV-01",
                            "hasil" => "500",
                            "satuan" => "mg/dL",
                            "nilai_rujukan" => "400-600",
                            "status" => "Tidak Normal",
                            "keterangan" => "amannn"
                        ]
                    ],
                    "keterangan" => "Pasien 1",
                    "status" => "Selesai"
                ],
                [
                    "unit_id" => 1,
                    "nik" => "9876543210",
                    "no_mr" => "MR9999",
                    "tanggal_periksa" => "2025-11-04",
                    "laboratorium_nama" => "Hematologi",
                    "penanggungjawab" => "dr. Amin",
                    "dokter_pengirim" => "dr. Budi",
                    "petugas_lab" => "Rina",
                    "ruang" => "Ruang Lab A",
                    "no_periksa" => "LAB-2025-002",
                    "hasil_pemeriksaan" => [],
                    "keterangan" => "Pasien 2",
                    "status" => "Dalam Pemeriksaan"
                ]
            ];

            $payload = [
                'items' => $hasilLabData,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Client-Id' => $this->clientId,
                'X-Client-Secret' => $this->clientSecret,
            ])->post($this->baseUrl . '/api/hasil_lab/bulk', $payload);

            $body = $response->json();

            if ($response->successful()) {
                Log::info('Batch hasil lab send completed', [
                    'summary' => $body['summary'] ?? null,
                ]);

                return response()->json([
                    'status' => 'success',
                    'response' => $body,
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'status_code' => $response->status(),
                'response' => $body,
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('Batch hasil lab send failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Batch processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // public function sendHasilLabWithFile()
    // {
    //     try {
    //         $pdfPath = public_path('dokumen.pdf');
    //         if (!file_exists($pdfPath)) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'File dokumen.pdf tidak ditemukan di public folder'
    //             ], 404);
    //         }
    //         $pdfContent = file_get_contents($pdfPath);
    //         $base64Pdf = base64_encode($pdfContent);
    //         $data = [
    //             "unit_id" => 1,
    //             "nik" => "1234567890",
    //             "no_mr" => "MR2303",
    //             "tanggal_periksa" => "2025-11-04",
    //             "laboratorium_nama" => "Hematologi",
    //             "penanggungjawab" => "dr. Amin",
    //             "dokter_pengirim" => "dr. Budi",
    //             "petugas_lab" => "Rina",
    //             "ruang" => "Ruang Lab A",
    //             "no_periksa" => "LAB-2025-001",
    //             "hasil_pemeriksaan" => [
    //                 [
    //                     "nama_layanan" => "PCV",
    //                     "kode_layanan" => "HEMPCV",
    //                     "kode_hasil" => "PCV-01",
    //                     "hasil" => "500",
    //                     "satuan" => "mg/dL",
    //                     "nilai_rujukan" => "400-600",
    //                     "status" => "Tidak Normal",
    //                     "keterangan" => "amannn"
    //                 ]
    //             ],
    //             "file_base64" => $base64Pdf,
    //             "keterangan" => "Pemeriksaan lengkap dengan file hasil",
    //             "status" => "Dalam Pemeriksaan"
    //         ];
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'X-Client-Id' => $this->clientId,
    //             'X-Client-Secret' => $this->clientSecret,
    //         ])->post($this->baseUrl . '/api/hasil_lab', $data);
    //         if ($response->successful()) {
    //             return response()->json([
    //                 'status' => 'success',
    //                 'response' => $response->json()
    //             ]);
    //         }
    //         throw new RequestException($response);
    //     } catch (RequestException $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
