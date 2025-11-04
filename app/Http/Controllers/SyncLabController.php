<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncLabController
{
    public function syncToExternalApi()
    {
        try {
            $baseUrl = env('MCU_URL');
            $clientId = env('MCU_API_KEY');
            $clientSecret = env('MCU_API_SECRET');

            $data = [
                [
                    "category" => "Laboratorium 1",
                    "services" => [
                        [
                            "name" => "Layanan 1",
                            "type" => "integer",
                            "limits" => [
                                [
                                    "gender" => "laki-laki",
                                    "usia_min" => 18,
                                    "usia_max" => 65,
                                    "batas_bawah" => 13,
                                    "batas_atas" => 17
                                ],
                                [
                                    "gender" => "perempuan",
                                    "usia_min" => 18,
                                    "usia_max" => 65,
                                    "batas_bawah" => 12,
                                    "batas_atas" => 15
                                ]
                            ]
                        ],
                        [
                            "name" => "Layanan 2",
                            "type" => "integer",
                            "limits" => [
                                [
                                    "gender" => null,
                                    "usia_min" => 18,
                                    "usia_max" => 65,
                                    "batas_bawah" => 4000,
                                    "batas_atas" => 11000
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "category" => "Laboratorium 2",
                    "services" => [
                        [
                            "name" => "Layanan A",
                            "type" => "integer"
                        ],
                        [
                            "name" => "Layanan B",
                            "type" => "integer"
                        ],
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Client-Id' => $clientId,
                'X-Client-Secret' => $clientSecret,
            ])->post($baseUrl . '/api/sync_lab', $data);

            if ($response->successful()) {
                Log::info('Lab data synced successfully', $response->json());
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data synced to external API',
                    'response' => $response->json()
                ]);
            }

            throw new RequestException($response);
        } catch (RequestException $e) {
            Log::error('Failed to sync lab data', [
                'error' => $e->getMessage(),
                'response' => $e->response?->json()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to sync data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
