<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly ProjectRepository $projects)
    {
    }

    public function index(Request $request): View
    {
        $client = $request->user();

        return view('portal.dashboard', [
            'projects' => $client->projects()->with('stage')->latest('id')->take(5)->get(),
            'activeCount' => $client->projects()->where('status', Project::STATUS_ACTIVE)->count(),
            'completedCount' => $client->projects()->where('status', Project::STATUS_COMPLETED)->count(),
            'deliverableCount' => $client->projects()
                ->withCount(['media' => fn ($query) => $query->deliverables()->visibleToClient()])
                ->get()
                ->sum('media_count'),
        ]);
    }
}
