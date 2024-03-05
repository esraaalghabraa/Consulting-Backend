<?php

namespace Database\Seeders;

use App\Models\Day;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Day::create([
            'name_en'=>'Saturday',
            'name_ar'=>'السبت'
        ]);
        Day::create([
            'name_en'=>'Sunday',
            'name_ar'=>'الأحد'
        ]);
        Day::create([
            'name_en'=>'Monday',
            'name_ar'=>'الاثنين'
        ]);
        Day::create([
            'name_en'=>'Tuesday',
            'name_ar'=>'الثلاثاء'
        ]);
        Day::create([
            'name_en'=>'Wednesday',
            'name_ar'=>'الأربعاء'
        ]);
        Day::create([
            'name_en'=>'Thursday',
            'name_ar'=>'الخميس'
        ]);
        Day::create([
            'name_en'=>'Friday',
            'name_ar'=>'الجمعة'
        ]);
    }
}
