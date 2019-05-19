<?php

namespace App\Http\Controllers;

use App\Domain;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getDomainScores(Request $request)
    {
        if ($request->ajax()) {

            if (Domain::all()->count()) {

                $now = null;
                $end = null;
                if ($request->get('from') && $request->get('to')) {
                    try {
                        $now = Carbon::parse($request->get('from'))->format('Y-m-01');
                        $end = Carbon::parse($request->get('to'))->endOfMonth()->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Do nothing.
                    }
                }

                if (!$now) {
                    $now = Carbon::now()->format('Y-m-01');
                    $end = Carbon::now()->endOfMonth()->format('Y-m-d');
                }

                // Get average results
                $avgScores = [];
                $avgScoresQuery = DB::select(DB::raw("select `domain_id`, AVG(`score_value`) AS 'value' FROM domain_scores GROUP BY `domain_id`"));
                foreach ($avgScoresQuery as $q) $avgScores[$q->domain_id] = number_format($q->value, 2);

                // Get average monthly results
                $avgMonthlyScores = [];
                $avgMonthlyQuery = DB::select(DB::raw("SELECT domain_id, AVG(score_value) AS 'value', YEAR(score_date) AS 'year', MONTH(score_date)  AS 'month' FROM domain_scores WHERE score_date >= '{$now}' AND score_date <= '{$end}' GROUP BY YEAR(score_date), MONTH(score_date), domain_id;"));
                foreach ($avgMonthlyQuery as $q) {
                    $avgMonthlyScores[$q->domain_id][$q->year . '/' . $q->month . '/01'] = number_format($q->value, 2);
                }


                // Create JSON object
                $scoreData = [];
                foreach (Domain::with('domainScores')->get() as $score) {
                    $scoreData[$score->name] = [
                        'daily_scores' => $score->domainScores->whereBetween('score_date', [$now, $end])->pluck('score_value', 'score_date'),
                        'average_daily' => $avgScores[$score->id],
                        'average_monthly' => $avgMonthlyScores[$score->id]
                    ];
                }

                return response()->json(['success' => true, 'data' => $scoreData], 200);
            }

            return response()->json(['success' => false, 'message' => 'No data is available to generate the reports. Please try again later.'], 200);

        }
        return response(null, 403);
    }


    public function getAvailableMonths(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::select(DB::raw('SELECT DATE_FORMAT(score_date, \'%Y/%m/01\') AS months FROM domain_scores group by months ORDER BY months ASC'));
            return response()->json($data, 200);
        }
        return response(null, 403);
    }

}
