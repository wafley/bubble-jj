<?php

namespace Database\Seeders;

use App\Models\UploadService;
use Illuminate\Database\Seeder;

class UploadServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Foto',
                'slug' => 'photo',
                'type' => 'image',
                'price' => 15000,
                'description' => 'Kirim foto kamu yang ingin dibuatkan video Jedag-jedug.',
            ],
            [
                'name' => 'Video',
                'slug' => 'video',
                'type' => 'video',
                'price' => 10000,
                'description' => 'Kirim video Jedag-jedug kamu yang mau diposting, tanpa batasan durasi dan ukuran.',
            ],
            [
                'name' => 'Video bersyarat',
                'slug' => 'free',
                'type' => 'video',
                'price' => 0,
                'description' => 'Upload video JJ dengan format MP4 yang sudah jadi dan ukuran maksimal 3MB.',
                'rules' => [
                    // "Video harus berdurasi maksimal 60 detik.",
                    "Video harus berukuran maksimal 3MB.",
                ],
            ],
        ];

        foreach ($services as $service) {
            UploadService::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
