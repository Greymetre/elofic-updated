<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SecondaryCustomer;
use App\Models\MasterDistributor;
use App\Models\User;

class PromotionalActivity extends Model
{
    protected $table = 'promotional_activities';

    protected $fillable = [
        'activity_approved_by',
        'activity_date',
        'target_market',
        'product_category',
        'activity_type',
        'total_order_qty',
        'total_order_amount',
        'order_confirm_by',
        'customer_type',
        'total_participants',
        'customer_count',
        'retailer_id',
        'distributor_id',
        'discussion_Points',
        'material_and_Samples',
        'feedback',
        'managers_remarks',
        'created_by'
    ];

    protected $casts = [
        'activity_date' => 'date',
        'total_order_amount' => 'decimal:2'
    ];

    public function discussionPoints()
    {
        return $this->hasMany(ActivityDiscussionPoint::class, 'activity_id');
    }

    public function materials()
    {
        return $this->hasMany(ActivityMaterial::class, 'activity_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(ActivityFeedback::class, 'activity_id');
    }

    public function attendees()
    {
        return $this->hasMany(ActivityAttendee::class, 'activity_id');
    }

    // Retailer
    public function retailer()
    {
        return $this->belongsTo(SecondaryCustomer::class, 'retailer_id','id');
    }

    // Distributor
    public function distributor()
    {
        return $this->belongsTo(MasterDistributor::class, 'distributor_id','id'
        );
    }

    // Activity Approved By
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'activity_approved_by','id');
    }

    // Activity Created By
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by','id');
    }

    public function orderConfirmedBy()
{
    return $this->belongsTo(SecondaryCustomer::class, 'order_confirm_by','id');
}
}