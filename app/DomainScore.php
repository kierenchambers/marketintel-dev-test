<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainScore extends Model
{
    protected $table = 'domain_scores';
    protected $guarded = ['id'];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

}
