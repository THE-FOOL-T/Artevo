<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\Museum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    /**
     * View available reports.
     */
    public function index()
    {
        Gate::authorize('export-reports');

        return view('admin.reports.index');
    }

    /**
     * Generate and download a CSV report.
     */
    public function export(Request $request, string $type)
    {
        Gate::authorize('export-reports');

        return match ($type) {
            'users' => $this->exportUsers(),
            'museums' => $this->exportMuseums(),
            'artifacts' => $this->exportArtifacts(),
            default => abort(404),
        };
    }

    private function exportUsers()
    {
        $users = User::all();
        $filename = 'users_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $columns = ['ID', 'Name', 'Email', 'Role', 'Joined At'];

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->roleLabel(),
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportMuseums()
    {
        $museums = Museum::with('curator')->get();
        $filename = 'museums_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $columns = ['ID', 'Name', 'Curator', 'City', 'Country', 'Verification Status', 'Created At'];

        $callback = function () use ($museums, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($museums as $museum) {
                fputcsv($file, [
                    $museum->id,
                    $museum->name,
                    $museum->curator?->name ?? 'None',
                    $museum->city,
                    $museum->country,
                    $museum->verificationLabel(),
                    $museum->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportArtifacts()
    {
        $artifacts = Artifact::all();
        $filename = 'artifacts_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $columns = ['ID', 'Title', 'Type', 'Visibility', 'Verification Status', 'Created At'];

        $callback = function () use ($artifacts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($artifacts as $artifact) {
                fputcsv($file, [
                    $artifact->id,
                    $artifact->title,
                    $artifact->type,
                    $artifact->visibility,
                    $artifact->verificationLabel(),
                    $artifact->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
