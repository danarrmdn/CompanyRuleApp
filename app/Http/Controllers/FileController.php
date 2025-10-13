<?php

namespace App\Http\Controllers;

use App\Models\CompanyRule;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show(CompanyRule $rule)
    {
        if (! $rule->file_path || ! Storage::disk('public')->exists($rule->file_path)) {
            abort(404, 'File not found.');
        }

        $fileContents = Storage::disk('public')->get($rule->file_path);
        $fileName = basename($rule->file_path);

        return Response::make($fileContents, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }
}
