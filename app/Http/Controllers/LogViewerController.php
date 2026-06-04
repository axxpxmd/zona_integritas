<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class LogViewerController extends Controller
{
    public function index(Request $request)
    {
        $logDir = storage_path('logs');
        
        // Find all .log files
        if (!File::exists($logDir)) {
            File::makeDirectory($logDir, 0755, true);
        }
        
        $files = File::glob($logDir . '/*.log');
        
        // Format file list
        $logFiles = [];
        foreach ($files as $file) {
            $name = basename($file);
            $size = File::size($file);
            $modified = File::lastModified($file);
            $logFiles[] = [
                'name' => $name,
                'size' => $this->formatBytes($size),
                'modified' => date('Y-m-d H:i:s', $modified),
                'raw_size' => $size,
                'raw_modified' => $modified,
            ];
        }

        // Sort files: latest modified first
        usort($logFiles, function ($a, $b) {
            return $b['raw_modified'] <=> $a['raw_modified'];
        });

        // Determine active log file
        $activeFile = $request->query('file');
        if (!$activeFile && !empty($logFiles)) {
            $activeFile = $logFiles[0]['name'];
        }

        $parsedLogs = [];
        $selectedFileExists = false;

        if ($activeFile) {
            $filePath = $logDir . '/' . $activeFile;
            if (File::exists($filePath)) {
                $selectedFileExists = true;
                $content = File::get($filePath);

                // Regex to parse Laravel log entries including multi-line trace
                $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+([a-zA-Z0-9_\-]+)\.([A-Z]+):\s+(.*?)(?=\n\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|\z)/s';
                preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $timestamp = $match[1];
                    $env = $match[2];
                    $level = strtoupper($match[3]);
                    $message = trim($match[4]);

                    // Apply filters
                    if ($request->filled('level') && $request->level !== $level) {
                        continue;
                    }
                    if ($request->filled('search')) {
                        $search = strtolower($request->search);
                        if (strpos(strtolower($message), $search) === false && strpos(strtolower($timestamp), $search) === false) {
                            continue;
                        }
                    }

                    $parsedLogs[] = [
                        'timestamp' => $timestamp,
                        'env' => $env,
                        'level' => $level,
                        'message' => $message,
                    ];
                }

                // Show latest logs first
                $parsedLogs = array_reverse($parsedLogs);
            }
        }

        return view('page.logs.index', compact('logFiles', 'activeFile', 'parsedLogs', 'selectedFileExists'));
    }

    public function download($file)
    {
        // Prevent directory traversal
        $file = basename($file);
        $filePath = storage_path('logs/' . $file);

        if (!File::exists($filePath)) {
            abort(404, 'File log tidak ditemukan.');
        }

        return Response::download($filePath);
    }

    public function destroy($file)
    {
        // Prevent directory traversal
        $file = basename($file);
        $filePath = storage_path('logs/' . $file);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('logs.index')->with('success', 'File log berhasil dihapus.');
        }

        return redirect()->route('logs.index')->with('error', 'File log tidak ditemukan.');
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
