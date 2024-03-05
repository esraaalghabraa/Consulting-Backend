<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expert;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::truncate();
        Category::create([
            'name_en'=>'Medical',
            'name_ar'=>'الطبية',
            'photo'=>'Medical.jfif'
        ]);
        Category::create([
            'name_en'=>'Vocational',
            'name_ar'=>'المهنية',
            'photo'=>'Vocational.jfif'
        ]);
        Category::create([
            'name_en'=>'Psychic',
            'name_ar'=>'النفسية',
            'photo'=>'Psychic.jfif'
        ]);
        Category::create([
            'name_en'=>'Familial',
            'name_ar'=>'العائلية',
            'photo'=>'Familial.jfif'
        ]);
        Category::create([
            'name_en'=>'Management',
            'name_ar'=>'الإدارية',
            'photo'=>'Management.jfif'
        ]);
    }


}
