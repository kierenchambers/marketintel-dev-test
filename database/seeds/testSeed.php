<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domain;
use Carbon\Carbon;

class testSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        // Add any additional domains you would like
        $domains = ['apple.com','techradar.com','carphonewarehouse.com','samsung.com','amazon.co.uk','trustedreviews.com','gsmarena.com','wikipedia.org','argos.co.uk','o2.co.uk'];

        // Modify these dates accordingly to desired demo data creation
        $startDate = Carbon::parse('2019-01-01');
        $endDate = Carbon::parse('2019-04-31');

        // Modify these for desired decimal range.
        $randomNumberStart = 0;
        $randomNumberEnd = 3;

        // The number of dates to create for each domain.
        $maxLimit = 100;


        while(true) {

            if($maxLimit === 0 || $startDate->equalTo($endDate)) {
                break;
            }

            foreach($domains as $domain) {

                $domainEntity = Domain::findOrCreate($domain);
                if($domainEntity->domainScores->where($startDate->format('Y-m-d'))->count()) {
                    continue;
                }
                $domainEntity->createOrUpdateScore([
                   'date' => $startDate->format('Y-m-d'),
                   'value' => $this->randomDecimal($randomNumberStart, $randomNumberEnd)
                ]);


            }

            $startDate = $startDate->addDay();

            $maxLimit--;

        }

    }

    private function randomDecimal($start, $end) {
        return number_format(mt_rand($start, $end) . '.' . mt_rand(0, 99) , 2);
    }
}
