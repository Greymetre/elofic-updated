<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityAttendee extends Model
{
    protected $table = 'activity_attendees';

    protected $fillable = [
        'activity_id',
        'person_name',
        'contact_no',
        'address',
        'remarks'
    ];

    public function activity()
    {
        return $this->belongsTo(PromotionalActivity::class, 'activity_id');
    }
}