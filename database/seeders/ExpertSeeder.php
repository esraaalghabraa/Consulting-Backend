<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Date;
use App\Models\Experience;
use App\Models\Expert;
use App\Models\WorkDays;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExpertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Expert::truncate();
        Date::truncate();
        WorkDays::truncate();
        Experience::truncate();
        for($i=1;$i<=250;$i++){
            $start=rand(6,12).':00 AM';
            $end=rand(1,10).':00 PM';
            Expert::create([
                'name'=>fake()->firstName(),
                'phone_number'=>'093'.fake()->randomDigit().fake()->randomDigit().fake()->randomDigit().fake()->randomDigit().fake()->randomDigit().fake()->randomDigit().fake()->randomDigit(),
                'password'=>Hash::make('123456789'),
                'photo'=>'person'.rand(1,40).'.jfif',
                'rating'=>fake()->randomFloat(5),
                'rating_number'=>rand(100,50000),
                'address'=>fake()->country(),
                'category_id'=>rand(1,5),
                'money'=>rand(50000,10000000),
                'start_work'=>$start,
                'end_work'=>$end,
            ]);

            $j=3;
            for ($s=0;$s<$j;$s++){
                Experience::create([
                    'expert_id'=>$i,
                    'name'=>fake()->word(),
                ]);
                if($s==$j)
                    $j=rand(3,8);
            }
            $days=fake()->randomElements([1,2,3,4,5,6,7],rand(2,5));
            $times=divideTime($start,$end);
            foreach ($days as $day){
                WorkDays::create([
                    'expert_id'=>$i,
                    'day_id'=>$day,
                ]);
                foreach ($times as $time){
                    Date::create([
                        'expert_id'=>$i,
                        'day_id'=>$day,
                        'time'=>$time,
                    ]);
                }
            }
        }
    }
}
