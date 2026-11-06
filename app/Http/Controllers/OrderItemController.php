<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderItemController extends Controller
{
    // public function dataTable()
    // {
    //     $query = OrderItem::query();
    //     return DataTables::eloquent($query)
    //         ->addIndexColumn()
    //         ->addColumn('action', function(OrderItem $order) {
    //             $btn ='<a href="'.route('order.edit', ['order' => $order->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
    //             return $btn;
    //         })
    //         ->rawColumns(['action'])
    //         ->toJson();
    // }
}
