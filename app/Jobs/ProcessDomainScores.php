<?php

namespace App\Jobs;

use App\Domain;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessDomainScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $apiEndPoint = config('app.test_end_point');

        if (!$apiEndPoint) {
            $this->failJob('Api endpoint invalid.');
        }

        $client = new Client();

        try {

            $result = $client->get($apiEndPoint);

        } catch (GuzzleException $e) {
            $this->failJob('Failed to successfully obtain JSON from API.');
        } catch (\Exception $e) {
            $this->failJob('An error occurred attempting to call the API.');
        }


        if ($result->getStatusCode() === 200) {

            $response = $result->getBody()->getContents();

            if (!is_object($response)) {
                $response = json_decode($response);
                if (!is_object($response)) {
                    $this->failJob('An invalid JSON object structure was returned by the API.');
                }
            }

            if (!isset($response->marketIntel)) {
                $this->failJob('The JSON object returned was not the expected schema:' . var_export($response, true));
            }

            // Attempt to store the data
            $this->storeApiData($response->marketIntel);

        } else {
            $this->failJob('Failed to successfully obtain JSON from API.');
        }

    }

    public function storeApiData($results)
    {
        foreach ($results as $result) {
            if (!isset($result->domain) || !isset($result->scores)) continue;
            $domain = Domain::findOrCreate($result->domain);
            foreach ($result->scores as $date => $value) {
                $domain->createOrUpdateScore(compact('date', 'value'));
            }
        }
    }

    private function failJob($message)
    {
        $this->fail(new \Exception($message));
        exit;
    }
}
