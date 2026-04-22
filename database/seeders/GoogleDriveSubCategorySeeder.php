<?php

namespace Database\Seeders;

use App\Models\GoogleDriveCategory;
use App\Models\GoogleDriveSubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GoogleDriveSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create Prestasi category
        $prestasiCategory = GoogleDriveCategory::where('slug', 'prestasi')->first();

        if (!$prestasiCategory) {
            $prestasiCategory = GoogleDriveCategory::create([
                'name' => 'Prestasi',
                'slug' => 'prestasi',
            ]);
        }

        // Create "Tingkat" sub-category with options
        $tingkatSubCategory = GoogleDriveSubCategory::firstOrCreate(
            [
                'google_category_id' => $prestasiCategory->id,
                'name' => 'Tingkat',
            ],
            [
                'slug' => Str::slug('Tingkat'),
            ]
        );

        // Add options/choices for Tingkat
        $tingkatOptions = ['Kabupaten', 'Provinsi', 'Nasional', 'Internasional'];
        foreach ($tingkatOptions as $index => $optionName) {
            $tingkatSubCategory->options()->firstOrCreate(
                [
                    'name' => $optionName,
                ],
                [
                    'order' => $index,
                ]
            );
        }

        $this->command->info('✓ Sub-category "Tingkat" dengan pilihan berhasil dibuat untuk kategori "Prestasi"');

        // Find or create Data Pribadi category
        $dataCategory = GoogleDriveCategory::where('slug', 'data-pribadi')->first();

        if (!$dataCategory) {
            $dataCategory = GoogleDriveCategory::create([
                'name' => 'Data Pribadi',
                'slug' => 'data-pribadi',
            ]);
        }

        // Create "Tipe Dokumen" sub-category with options
        $tipeDocSubCategory = GoogleDriveSubCategory::firstOrCreate(
            [
                'google_category_id' => $dataCategory->id,
                'name' => 'Tipe Dokumen',
            ],
            [
                'slug' => Str::slug('Tipe Dokumen'),
            ]
        );

        // Add options for Tipe Dokumen
        $tipeDocOptions = ['KTP', 'Paspor', 'NPWP', 'Sertifikat Vaksin', 'Lainnya'];
        foreach ($tipeDocOptions as $index => $optionName) {
            $tipeDocSubCategory->options()->firstOrCreate(
                [
                    'name' => $optionName,
                ],
                [
                    'order' => $index,
                ]
            );
        }

        $this->command->info('✓ Sub-category "Tipe Dokumen" dengan pilihan berhasil dibuat untuk kategori "Data Pribadi"');
    }
}
