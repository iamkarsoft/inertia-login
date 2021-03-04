<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model {
	// use HasFactory;
	protected $table = "users_social";

	protected $guarded = [];
	protected $fillable = ['social_id', 'service'];

	public function user() {
		return $this->belongsTo(User::class);
	}
}
