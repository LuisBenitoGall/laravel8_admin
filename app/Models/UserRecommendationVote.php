<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRecommendationVote extends Model{
    use HasFactory;

    protected $table = 'user_recommendation_votes';
}
