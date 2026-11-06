<?php

use App\Models\AccountGroup;
use App\Models\AccountHead;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function numberToMonthName($monthNumber){
    if ($monthNumber != '')
        return Carbon::create(null, $monthNumber, 1)->format('F');
    else
        return '';
}
function ordinalSuffix($number) {
    if ($number % 10 == 1 && $number % 100 != 11) {
        return 'st';
    } elseif ($number % 10 == 2 && $number % 100 != 12) {
        return 'nd';
    } elseif ($number % 10 == 3 && $number % 100 != 13) {
        return 'rd';
    } else {
        return 'th';
    }
}

function getFiscalYear($date) {

    $currentMonth = Carbon::parse($date)->format('m');
    if ($currentMonth < 7) {
        $firstYear = Carbon::parse($date)->format('Y') - 1;
        $secondYear = Carbon::parse($date)->format('Y');
        $fiscalYear = $firstYear.'-'.$secondYear;
    } else {
        $firstYear = Carbon::parse($date)->format('Y');
        $secondYear = $firstYear + 1;
        $fiscalYear = $firstYear.'-'.$secondYear;
    }


    return $fiscalYear;
}
function friendlyFieldName($fieldName) {

    $fieldName = preg_replace('/\.\d+/', '', $fieldName); // Remove dot and number
    $fieldName = str_replace('_', ' ', $fieldName); // Replace underscores with spaces
    return ($fieldName);
}
function accountGroupBalance($parentId,$startDate, $endDate,$type)
{
    if ($type == 1) {
        //Current Fiscal year
        $start = Carbon::parse($startDate)->format('Y-m-d');
        $end = Carbon::parse($endDate)->format('Y-m-d');
    } else {
        //Previous Fiscal year
        $start = date('d-m-Y', strtotime('-1 year', strtotime($startDate)));
        $end = date('d-m-Y', strtotime('-1 day', strtotime($startDate)));
    }

    // Get the parent AccountHeadType by ID
    $parentType = AccountGroup::find($parentId);
    // Get all child IDs recursively
    $childIds = $parentType->getAllChildIds();
    $childIds[] = $parentId;
    // Retrieve AccountHead records for the child types
    $accountHeadsIdQuery = AccountHead::whereIn('account_group_id', $childIds);

    $accountHeadsId = $accountHeadsIdQuery->select('id')
        ->pluck('id')
        ->toArray();

    $transactionLogQuery = Transaction::select(
        DB::raw('SUM(CASE WHEN transaction_type IN (1) THEN amount ELSE 0 END) as debitTotal'),
        DB::raw('SUM(CASE WHEN transaction_type IN (2) THEN amount ELSE 0 END) as creditTotal')
    );

    $transactionLogs = $transactionLogQuery->whereIn('account_head_id', $accountHeadsId)
        ->where('date','<=',$end)
        ->first();

    $debitOpening = 0;
    $creditOpening = 0;
        $accountHeadOpening = AccountHead::whereIn('id', $accountHeadsId)
            ->sum('opening_balance');

        if ($parentType->top_parent_id) {
            if ($parentType->top_parent_id == 3) {
                $debitOpening = $accountHeadOpening;
            } elseif ($parentType->top_parent_id == 4 || $parentType->top_parent_id == 5) {
                $creditOpening = $accountHeadOpening;
            }
        }

    return ($transactionLogs->debitTotal + $debitOpening) - ($transactionLogs->creditTotal + $creditOpening);
}
function accountGroupBalanceIncomeStatement($parentId,$startDate, $endDate,$type)
{
    if ($type == 1) {
        //Current Fiscal year
        $start = Carbon::parse($startDate)->format('Y-m-d');
        $end = Carbon::parse($endDate)->format('Y-m-d');
    } else {
        //Previous Fiscal year
        $start = date('d-m-Y', strtotime('-1 year', strtotime($startDate)));
        $end = date('d-m-Y', strtotime('-1 day', strtotime($startDate)));
    }

    // Get the parent AccountHeadType by ID
    $parentType = AccountGroup::find($parentId);
    // Get all child IDs recursively
    $childIds = $parentType->getAllChildIds();
    $childIds[] = $parentId;
    // Retrieve AccountHead records for the child types
    $accountHeadsIdQuery = AccountHead::whereIn('account_group_id', $childIds);
    $accountHeadsId = $accountHeadsIdQuery->select('id')
        ->pluck('id')
        ->toArray();

    $transactionLogQuery = Transaction::select(
        DB::raw('SUM(CASE WHEN transaction_type IN (1) THEN amount ELSE 0 END) as debitTotal'),
        DB::raw('SUM(CASE WHEN transaction_type IN (2) THEN amount ELSE 0 END) as creditTotal')
    );

    $transactionLogs = $transactionLogQuery->whereIn('account_head_id', $accountHeadsId)
        ->whereBetween('date',[$start,$end])
        ->first();

    return $transactionLogs->debitTotal - $transactionLogs->creditTotal;
}
function dropdownMenuContainer($elements){
    $html ='<div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i></button>
            <div class="dropdown-menu" role="menu" style="">';
    $html .= $elements;
    $html .='</div></div>';
    return $html;
}
function englishToBangla($number){
    $bn = ["১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০"];
    $en = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];
    return str_replace($en,$bn,$number);
}
function encrypt_value($encriptValue)
{
    return Crypt::encrypt($encriptValue);
}
function decrypt_value($decriptValue)
{
    return Crypt::decrypt($decriptValue);
}
function enToBn($number) {
    $en = ['0','1','2','3','4','5','6','7','8','9'];
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];

    return str_replace($en, $bn, $number);
}