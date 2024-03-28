<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\TimeRecord;
use App\Services\TimeRecordService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimesheetUploadController extends Controller
{
    protected $timeRecordService;

    private const CSV_HEADERS = [
        'employee_id',
        'clock_in',
        'clock_out',
    ];

    private const EMPLOYEE_ID = 0;

    private const CLOCK_IN = 1;

    private const CLOCK_OUT = 2;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function store(Request $request, Company $company): JsonResponse
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        if ($request->file('csv_file')->isValid()) {
            $filePath = $request->file('csv_file')->getRealPath();

            $firstRow = true;
            if (($handle = fopen($filePath, 'r')) !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    if ($firstRow) {
                        $firstRow = false;

                        continue;
                    }

                    $employee = Employee::find($data[self::EMPLOYEE_ID]);

                    throw_unless($employee, new Exception('Employee not found. ID: '.$data[self::EMPLOYEE_ID]));

                    $expectedSchedule = $this->timeRecordService->getExpectedScheduleOf(
                        $employee,
                        $data[self::CLOCK_IN],
                        $data[self::CLOCK_OUT]
                    );

                    $timeData = [
                        'company_id' => $company->id,
                        'employee_id' => $employee->id,
                        'clock_in' => $data[self::CLOCK_IN],
                        'clock_out' => $data[self::CLOCK_OUT],
                    ];

                    if ($expectedSchedule['expected_clock_in'] && $expectedSchedule['expected_clock_out']) {
                        $expectedClockIn = Carbon::parse($data[self::CLOCK_IN])->format('Y-m-d')
                            .' '.Carbon::parse($expectedSchedule['expected_clock_in'])->format('H:i:s');
                        $expectedClockOut = Carbon::parse($data[self::CLOCK_OUT])->format('Y-m-d')
                            .' '.Carbon::parse($expectedSchedule['expected_clock_out'])->format('H:i:s');

                        TimeRecord::updateOrCreate([
                            'expected_clock_in' => $expectedClockIn,
                            'expected_clock_out' => $expectedClockOut,
                        ], $timeData);
                    } else {
                        $timeData['expected_clock_in'] = null;
                        $timeData['expected_clock_out'] = null;
                        TimeRecord::create($timeData);
                    }
                }
                fclose($handle);
            }

            return $this->sendResponse(null, 'CSV file uploaded and processed successfully.');
        }
    }
}
