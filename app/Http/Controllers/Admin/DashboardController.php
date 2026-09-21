<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly ClientRepository $clients,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'rolesCount' => Role::count(),
            'permissionsCount' => Permission::count(),
            'usersCount' => User::count(),
            'recentRoles' => Role::latest()->take(5)->get(),

            // Operational view: what is waiting on us right now.
            'pendingClientsCount' => $this->clients->pendingCount(),
            'activeClientsCount' => $this->clients->activeClientCount(),
            'activeProjectsCount' => Project::where('status', Project::STATUS_ACTIVE)->count(),
            'completedProjectsCount' => Project::where('status', Project::STATUS_COMPLETED)->count(),
            'recentProjects' => $this->projects->recent(),
            'stages' => PipelineStage::ordered()->get(),
            'countsByStage' => $this->projects->countsByStage(),
        ]);
    }
}
