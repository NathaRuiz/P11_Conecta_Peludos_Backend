<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
     // Variable estática para verificar si ya se ejecutó el seeder
     protected static $executed = false;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica si ya se ejecutó el seeder
        if (static::$executed) {
            return;
        }
        var_dump('ProvinceSeeder is running');
        $provinces = [
            'Álava',
            'Albacete',
            'Alicante',
            'Almería',
            'Asturias',
            'Ávila',
            'Badajoz',
            'Barcelona',
            'Burgos',
            'Cáceres',
            'Cádiz',
            'Cantabria',
            'Castellón',
            'Ciudad Real',
            'Córdoba',
            'Cuenca',
            'Gerona',
            'Granada',
            'Guadalajara',
            'Guipúzcoa',
            'Huelva',
            'Huesca',
            'Islas Baleares',
            'Jaén',
            'La Coruña',
            'La Rioja',
            'Las Palmas',
            'León',
            'Lérida',
            'Lugo',
            'Madrid',
            'Málaga',
            'Murcia',
            'Navarra',
            'Orense',
            'Palencia',
            'Pontevedra',
            'Salamanca',
            'Santa Cruz de Tenerife',
            'Segovia',
            'Sevilla',
            'Soria',
            'Tarragona',
            'Teruel',
            'Toledo',
            'Valencia',
            'Valladolid',
            'Vizcaya',
            'Zamora',
            'Zaragoza'
        ];

        foreach ($provinces as $provinceName) {
            Province::create(['name' => $provinceName]);
        }

        // Marca el seeder como ejecutado
        static::$executed = true;
    }
}
