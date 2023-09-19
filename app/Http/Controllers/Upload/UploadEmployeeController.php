<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class UploadEmployeeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            // Process the CSV data and save employees here
            $employeesData = array_map('str_getcsv', file($path));

            foreach ($employeesData as $employeeData) {
                // Assuming CSV structure: name, email, etc.
                $employee = new Employee();
                $employee->name = $employeeData[0];
                $employee->email = $employeeData[1];
                // Add more fields as needed
                $employee->save();
            }

            return redirect('/employees')->with('success', 'Employees imported successfully.');
        }

        return redirect('/upload-employees')->with('error', 'No file selected.');
    }
}
