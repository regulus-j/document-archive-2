<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationDataController extends Controller
{
    /**
     * Return only the cities for the requested country/state key.
     * This avoids loading the full cities.json payload in the browser.
     */
    public function cities(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country' => ['required', 'string', 'size:2'],
            'state' => ['required', 'string', 'max:20'],
        ]);

        $country = strtoupper($validated['country']);
        $state = strtoupper($validated['state']);
        $key = $country . '-' . $state;

        $citiesFile = public_path('data/cities.json');

        if (!is_file($citiesFile)) {
            return response()->json(['cities' => []]);
        }

        $cities = $this->extractCitiesByKey($citiesFile, $key);

                return response()->json([
            'cities' => $cities,
                ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
    }

    /**
     * Extract a single key array from large cities.json without decoding entire file.
     */
    private function extractCitiesByKey(string $filePath, string $key): array
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            return [];
        }

        $targetPrefix = '"' . $key . '":';
        $capturing = false;
        $buffer = '';
        $depth = 0;

        while (($line = fgets($handle)) !== false) {
            if (!$capturing) {
                if (strpos($line, $targetPrefix) === false) {
                    continue;
                }

                $arrayStartPos = strpos($line, '[');
                if ($arrayStartPos === false) {
                    continue;
                }

                $capturing = true;
                $fragment = substr($line, $arrayStartPos);
                $buffer .= $fragment;
                $depth += substr_count($fragment, '[');
                $depth -= substr_count($fragment, ']');

                if ($depth <= 0) {
                    break;
                }

                continue;
            }

            $buffer .= $line;
            $depth += substr_count($line, '[');
            $depth -= substr_count($line, ']');

            if ($depth <= 0) {
                break;
            }
        }

        fclose($handle);

        $buffer = trim($buffer);
        if ($buffer === '') {
            return [];
        }

        $buffer = rtrim($buffer);
        if (substr($buffer, -1) === ',') {
            $buffer = substr($buffer, 0, -1);
        }

        $decoded = json_decode($buffer, true);

        return is_array($decoded) ? $decoded : [];
    }
}
