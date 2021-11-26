<?php

namespace App\Http\Controllers;

use App\Models\Headache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HeadacheController extends Controller
{
    public function store()
    {
        $user_id = Auth::id();
        Headache::create([
            "user_id" => $user_id,
            "date" => request("date"),
            "time" => request("time"),
            "strength" => request("strength"),
            "comments" => request("comment", ''),
        ]);
        return back();
    }

    public function destroy($id)
    {
        Headache::findOrFail($id)->delete();

        return back();
    }

    public function export()
    {

        Carbon::setLocale(config('app.locale'));

        $tmp_file = uniqid() . ".xlsx";
        $header_filename = "Export_" . Carbon::now()->format("d_m_Y") . ".xlsx";
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "Data");
        $spreadsheet->addSheet($sheet);


        $month = 0;
        $col = -2;
        foreach (Headache::orderBy("date")->get() as $headache) {
            $headacheMonth = $headache->date->month;
            if ($month != $headacheMonth) {
                $col += 3;
                $month = $headacheMonth;
                $carb = Carbon::make($headache->date)->setDay(1);
                // Move to next column, reset line counter, write month days :
                $sheet->setCellValueByColumnAndRow($col, 1, $carb->isoFormat("MMMM"));
                $sheet->setCellValueByColumnAndRow($col, 2, $carb->format("Y"));
                // Merge header cells :
                $sheet->mergeCellsByColumnAndRow($col, 1, $col + 2, 1);
                $sheet->mergeCellsByColumnAndRow($col, 2, $col + 2, 2);
                // Now set days :
                $lastDay = $carb->addMonth()->subDay()->day;
                for ($day = 1; $day <= $lastDay; $day++) {
                    $carb = Carbon::make($headache->date)->setDay($day);
                    $sheet->setCellValueByColumnAndRow($col, 2 + $day, strtoupper(substr($carb->isoFormat("dddd"), 0, 1)));
                    $sheet->getCellByColumnAndRow($col + 1, 2 + $day)->setValueExplicit(
                        $carb->format("d"),
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    );
                    if ($carb->dayOfWeekIso >= 6) {
                        $coord1 = $sheet->getCellByColumnAndRow($col, $day + 2)->getCoordinate();
                        $coord2 = $sheet->getCellByColumnAndRow($col + 1, $day + 2)->getCoordinate();

                        $sheet->getStyle($coord1)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                        $sheet->getStyle($coord1)
                            ->getFill()->getStartColor()->setARGB('FF99FF99');
                        $sheet->getStyle($coord2)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                        $sheet->getStyle($coord2)
                            ->getFill()->getStartColor()->setARGB('FF99FF99');
                    }
                    // Auto resize columns :
                    $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
                    $sheet->getColumnDimensionByColumn($col + 1)->setAutoSize(true);
                    $sheet->getColumnDimensionByColumn($col + 2)->setAutoSize(true);

                }
            }

            $row = $headache->date->day + 2;
            $value = $sheet->getCellByColumnAndRow($col + 2, $row)->getValue() . " " . $headache->time;

            $sheet->setCellValueByColumnAndRow($col + 2, $row, trim($value));
            $sheet->getCellByColumnAndRow($col + 2, $row)->getStyle()
                ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getCellByColumnAndRow($col + 2, $row)->getStyle()
                ->getFill()->getStartColor()->setARGB('FFFF9999');

        }


        $writer = new Xlsx($spreadsheet);
        $writer->save($tmp_file);

        $file = file_get_contents($tmp_file);
        unlink($tmp_file);
        return response($file, 200, [
            "content-type" => "application/xlsx",
            "Content-Disposition" => 'attachment; filename="' . $header_filename . '"',
        ]);
    }
}
