<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // Gives controllers $this->authorize() for the Project / ProjectMedia
    // policies — clients reaching only their own work, staff gated by the
    // 'projects.*' permissions.
    use AuthorizesRequests;
}
