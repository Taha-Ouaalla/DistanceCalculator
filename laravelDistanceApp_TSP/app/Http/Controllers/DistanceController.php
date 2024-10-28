<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;

class DistanceController extends Controller
{
    public function index()
    {
        return view('welcome');
    }
    public function calculateTSP(Request $request)
    {
        ini_set('max_execution_time', 0);  // Set unlimited execution time

        $request->validate([
            'locations' => 'required|file',
        ]);

        $inputFile = $request->file('locations')->store('temp');
        $pythonPath = "C:\\Users\\yoga\\AppData\\Local\\Programs\\Python\\Python312\\python.exe";
        $scriptPath ="C:\\Users\\yoga\\Desktop\\PFA\\2\\Distance_Calculator _Copy\\laravelDistanceApp\\storage\\app\\python\\TSP_script.py";
        $filePath = storage_path('app/' . $inputFile);

        $command = [$pythonPath, $scriptPath, $filePath];
        $process = new Process($command);
        $process->setWorkingDirectory(base_path());
        $process->run();

        if (!$process->isSuccessful()) {
            Storage::delete($inputFile);
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        Storage::delete($inputFile);
        return response()->json(json_decode($output), 200);
    }

    public function calculate(Request $request)
    {   ini_set('max_execution_time', 0);
        $request->validate([
            'city_country' => 'required',
            'locations_file' => 'required|file'
        ]);

        $cityCountry = escapeshellarg($request->city_country);
        $filePath = $request->file('locations_file')->getRealPath();
        $places = $this->readPlaces($filePath);

        // Define the paths to the Python executable and script
        $pythonPath = "C:\Users\yoga\AppData\Local\Programs\Python\Python312\python.exe"; // Update this to your Python path
        $scriptPath = "C:\\Users\\yoga\\Desktop\\PFA\\2\\Distance_Calculator\\laravelDistanceApp\\storage\\app\\python\\calculate_distances.py";

        // Construct the PowerShell command
        $command = "powershell -Command \"Get-Content $filePath | $pythonPath $scriptPath $cityCountry\"";

        exec($command, $output, $return_var);

        // Log the results of the command execution
        error_log("Command executed: $command");
        error_log("Command return value: $return_var");
        error_log("Command output: " . implode("\n", $output));

        if ($return_var != 0 || empty($output)) {
            error_log("Failed to execute command or no output returned.");
            return view('result', ['distanceMatrix' => [], 'places' => $places]);
        }

        // Assuming output is a series of comma-separated distances
        $distanceMatrix = array_map(function ($row) {
            return array_map(function ($item) {
                return number_format((float)$item, 4, '.', '');
            }, explode(',', $row));
        }, $output);

        Session::put('distanceMatrix', $distanceMatrix);
        Session::put('places', $places);

        return view('result', compact('distanceMatrix', 'places'));
    }

    public function save(Request $request)
    {
        $distanceMatrix = Session::get('distanceMatrix');
        $places = Session::get('places');

        if (empty($distanceMatrix) || empty($places)) {
            return redirect()->back()->with('error', 'No data to save.');
        }

        $filename = "distance_matrix.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array_merge([''], $places));

        foreach ($distanceMatrix as $i => $row) {
            fputcsv($handle, array_merge([$places[$i]], $row));
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    private function readPlaces($filePath)
    {
        $places = [];
        $handle = fopen($filePath, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $parts = explode(',', $line);
                if (count($parts) >= 1) {
                    $places[] = trim($parts[0]);
                }
            }
            fclose($handle);
        }
        return $places;
    }
}
