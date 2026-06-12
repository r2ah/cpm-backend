<?php

namespace App\Providers;

use App\Models\Authority;
use App\Models\Intervention;
use App\Models\Opinion;
use App\Models\Person;
use App\Models\Proceeding;
use App\Models\User;

use App\Policies\AuthorityPolicy;
use App\Policies\InterventionPolicy;
use App\Policies\OpinionPolicy;
use App\Policies\PersonPolicy;
use App\Policies\ProceedingPolicy;
use App\Policies\UserPolicy;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
	protected $policies = [
		Authority::class => AuthorityPolicy::class,
		Intervention::class => InterventionPolicy::class,
		Opinion::class => OpinionPolicy::class,
		Person::class => PersonPolicy::class,
		Proceeding::class => ProceedingPolicy::class,
		User::class => UserPolicy::class,
	];

	public function boot()
	{
		$this->registerPolicies();
	}
}
