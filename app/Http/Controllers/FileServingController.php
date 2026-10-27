<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FileServingController extends Controller
{
    public function preview($path)
    {
        $path = $this->sanitizePath($path);
        $this->authorizeAccess($path);

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        $name = basename($path);
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $fileUrl = route('serve-file', ['path' => $path]);
        $downloadUrl = route('download-file', ['path' => $path]);

        return Inertia::render('Files/Preview', compact('name', 'extension', 'fileUrl', 'downloadUrl'));
    }

    public function serveFile($path)
    {
        $path = $this->sanitizePath($path);
        $this->authorizeAccess($path);

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        $mimeType = $this->resolveMimeType($path);
        $fileName = basename($path);
        $content = Storage::disk('public')->get($path);

        return response($content, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', strlen($content))
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"')
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('X-Content-Type-Options', 'nosniff');
    }

    public function download($path)
    {
        $path = $this->sanitizePath($path);
        $this->authorizeAccess($path);

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($path, basename($path));
    }

    private function authorizeAccess(string $path): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        // Administrative and operational staff have full access
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super-admin', 'superadmin', 'consultant', 'application', 'accountant', 'editor', 'marketing'])) {
            return;
        }

        // If the logged-in user is a student, only allow their own documents
        $student = \App\Models\Student::where('email', $user->email)->first();
        if ($student) {
            if (is_array($student->documents)) {
                foreach ($student->documents as $doc) {
                    if (isset($doc['path']) && $doc['path'] === $path) {
                        return;
                    }
                }
            }
            if (is_array($student->translation_documents)) {
                foreach ($student->translation_documents as $doc) {
                    if (isset($doc['path']) && $doc['path'] === $path) {
                        return;
                    }
                }
            }
        }

        abort(403, 'Unauthorized access to this document.');
    }

    private function sanitizePath(string $path): string
    {
        // URL decode to handle encoded traversal attempts (%2e%2e, etc.)
        $path = urldecode($path);

        // Normalize slashes and remove null bytes
        $path = str_replace(['\\', "\0"], ['/', ''], $path);

        // Remove any path components that contain dots (prevents ../, ..\, ....//, etc.)
        $parts = explode('/', $path);
        $safeParts = array_filter($parts, function ($part) {
            return $part !== '' && $part !== '.' && $part !== '..' && !str_contains($part, '..');
        });

        // Reconstruct safe path
        return implode('/', $safeParts);
    }

    private function resolveMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => Storage::disk('public')->mimeType($path) ?: 'application/octet-stream',
        };
    }
}
