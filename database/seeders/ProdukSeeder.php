<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_produk' => 'Donat Coklat',
                'harga' => 10000,
                'stok' => 50,
                
                
            ],
            [
                'nama_produk' => 'Donat Keju',
                'harga' => 12000,
                'stok' => 40,
                
                
            ],
            [
                'nama_produk' => 'Donat Strawberry',
                'harga' => 11000,
                'stok' => 30,
                
                
            ],
            [
                'nama_produk' => 'Donat Matcha',
                'harga' => 13000,
                'stok' => 25,
                
                
            ],
            [
                'nama_produk' => 'Donat Tiramisu',
                'harga' => 15000,
                'stok' => 20,
                
                
            ],
        ];

        foreach ($data as $item) {
            Produk::create($item);
        }
    }
}