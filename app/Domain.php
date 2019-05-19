<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $table = 'domains';
    protected $guarded = ['id'];


    public function domainScores()
    {
        return $this->hasMany(DomainScore::class)->orderBy('score_date', 'ASC');
    }

    public function createOrUpdateScore($score = [])
    {
        if ($score && isset($this->id)) {
            $existingScore = $this->domainScores->where('score_date', $score['date'])->first();
            if ($existingScore) {
                if ($existingScore->score_value != number_format($score['value'], 2)) {
                    $existingScore->score_value = number_format($score['value'], 2);
                    $existingScore->save();
                }
                return $existingScore;
            } else {
                return \App\DomainScore::create([
                    'domain_id' => $this->id,
                    'score_date' => $score['date'],
                    'score_value' => number_format($score['value'], 2)
                ]);
            }
        }
        return false;
    }

    static function findOrCreate($name)
    {
        $domain = Domain::query()->where('name', $name)->first();
        if (!$domain) {
            $domain = Domain::create(['name' => $name]);
        }
        return $domain;
    }

}
