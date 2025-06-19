<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function summary()
    {
        try {
            $result = $this->dashboardService->getSummary();
            return response()->json([
                "data" => $result,
                "message" => "Dashboard summary fetched successfully",
                "statusCode" => 200,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong " . $e->getMessage(),
                ]
            ], 500);
        }
    }
}
