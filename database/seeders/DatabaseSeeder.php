<?php

namespace Database\Seeders;

use App\Models\KategoriBarang;
use App\Models\SukuCadang;
use App\Models\TiketServis;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'kelola master', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'proses servis', 'guard_name' => 'web']);

        $adminRole     = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $teknisiRole   = Role::firstOrCreate(['name' => 'teknisi', 'guard_name' => 'web']);
        $pelangganRole = Role::firstOrCreate(['name' => 'pelanggan', 'guard_name' => 'web']);

        $adminRole->givePermissionTo(['kelola master', 'proses servis']);
        $teknisiRole->givePermissionTo(['proses servis']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@servis.test'],
            ['name' => 'Admin Servis', 'password' => Hash::make('password')]
        );
        $admin->assignRole($adminRole);

        $teknisi = User::firstOrCreate(
            ['email' => 'teknisi@servis.test'],
            ['name' => 'Budi Teknisi', 'password' => Hash::make('password')]
        );
        $teknisi->assignRole($teknisiRole);

        $pelanggan = User::firstOrCreate(
            ['email' => 'pelanggan@servis.test'],
            ['name' => 'Sinta Pelanggan', 'password' => Hash::make('password')]
        );
        $pelanggan->assignRole($pelangganRole);

        $laptop = KategoriBarang::firstOrCreate(
            ['kode_kategori' => 'KAT-LPT'],
            ['nama_kategori' => 'Laptop & Notebook']
        );
        $hp = KategoriBarang::firstOrCreate(
            ['kode_kategori' => 'KAT-HP'],
            ['nama_kategori' => 'Smartphone & Tablet']
        );

        SukuCadang::firstOrCreate(
            ['kode_part' => 'PRT-SSD512'],
            ['nama_part' => 'SSD NVMe 512GB', 'stok' => 15, 'harga' => 650000]
        );
        SukuCadang::firstOrCreate(
            ['kode_part' => 'PRT-LCDHP'],
            ['nama_part' => 'Layar OLED Replacement', 'stok' => 8, 'harga' => 850000]
        );

        TiketServis::firstOrCreate(
            ['kode_tiket' => 'SRV-DEMO01'],
            [
                'user_id'            => $pelanggan->id,
                'teknisi_id'         => $teknisi->id,
                'kategori_barang_id' => $laptop->id,
                'nama_barang'        => 'ASUS ROG Zephyrus G14',
                'keluhan'            => 'Mati total saat dicas, indikator tidak menyala.',
                'status'             => 'diproses',
                'biaya_servis'       => 350000,
                'tanggal_masuk'      => now()->toDateString(),
            ]
        );
    }
}
