<?php

use Intervention\Image\ImageManagerStatic as Image;

function uploadFile ($file, $destPath)
{
    $filename = '';
    if ($file) {
        $filename = date('mdYHis') . uniqid(). '.' .$file->getClientOriginalExtension();

        $file->move($destPath, $filename);
    }

    return $filename;
}

function uploadThumbnail ($img, $destPath)
{
    $img_name = '';
    if ($img) {
        $img_name = date('mdYHis') . uniqid(). '.' .$img->getClientOriginalExtension();

        $img_resized = Image::make($img->getRealPath());
        $img_resized->resize(300, null, function($constraint) {
            $constraint->aspectRatio();
        });
        $img_resized->save(public_path($destPath. '/' .$img_name));
    }

    return $img_name;
}

function convDbDateToThDate ($dbDate)
{
    if(empty($dbDate)) return '';

    $arrDate = explode('-', $dbDate);

    return $arrDate[2]. '/' .$arrDate[1]. '/' .((int)$arrDate[0] + 543);
}

function convThDateToDbDate ($dbDate)
{
    if(empty($dbDate)) return '';

    $arrDate = explode('/', $dbDate);

    return ((int)$arrDate[2] - 543). '-' .$arrDate[1]. '-' .$arrDate[0];
}

function calcBudgetYear ($sdate)
{
    $budgetYear = date('Y') + 543;
    list($day, $month, $year) = explode('/', $sdate);

    if ((int)$month >= 10) {
        $budgetYear = (int)$year + 1;
    } else {
        $budgetYear = (int)$year;
    }

    return $budgetYear;
}

function convDbDateToLongThDate ($dbDate)
{
    $monthNames = [
        '01' => 'มกราคม',
        '02' => 'กุมภาพันธ์',
        '03' => 'มีนาคม',
        '04' => 'เมษายน',
        '05' => 'พฤษภาคม',
        '06' => 'มิถุนายน',
        '07' => 'กรกฎาคม',
        '08' => 'สิงหาคม',
        '09' => 'กันยายน',
        '10' => 'ตุลาคม',
        '11' => 'พฤศจิกายน',
        '12' => 'ธันวาคม',
    ];

    if(empty($dbDate)) return '';

    $arrDate = explode('-', $dbDate);

    return (int)$arrDate[2]. ' ' .$monthNames[$arrDate[1]]. ' ' .((int)$arrDate[0] + 543);
}

/**
 * $renderType should be 'preview' | 'download'
 */
function renderPdf($view, $data, $renderType = 'preview')
{
    $pdf = PDF::loadView($view, $data);

    /** แบบนี้จะ stream มา preview */
    if ($renderType == 'preview') {
        return $pdf->stream();
    }

    /** แบบนี้จะดาวโหลดเลย */
    return $pdf->download('test.pdf');
}
