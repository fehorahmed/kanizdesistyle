<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\PackSize;
use App\Models\Product;
use App\Models\PurchaseInventory;
use App\Models\PurchaseInventoryLog;
use App\Models\PurchaseOrderProduct;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Ramsey\Uuid\Uuid;

class PurchasesImport implements ToCollection
{
    /**
     * @param $fileName
     */

    public function __construct($fileName){
        $this->fileName = $fileName;
    }

    public function collection(Collection $collection){
        try {
            $sub_total = 0;
            foreach ($collection as $key => $data) {
                if($key== 0){
                    continue;
                }
// dd($data[7]);
                $brand = Brand::where('name',trim($data[3]))->first();
                if(!$brand){
                    $brand = new Brand();
                    $brand->name = $data[3];
                    $brand->status = 1;
                    $brand->save();
                }
                $packSize = PackSize::where('name',$data[1])->where('number',$data[2])->first();
                if(!$packSize){
                    $packSize = new PackSize();
                    $packSize->name = $data[1];
                    $packSize->number = $data[2];
                    $packSize->status = 1;
                    $packSize->user_id = Auth::id();;
                    $packSize->save();
                }

                $lastProduct = Product::where('department_id',1)->latest('id')->first();

                // Generate the next code
                $nextCodeNumber = !empty($lastProduct) ? intval(substr($lastProduct->code, 4)) + 1 : 1;
                $newCode = 'FMP-' . str_pad($nextCodeNumber, 6, '0', STR_PAD_LEFT);

                $product = new Product();
                $product->name = $data[0];
                $product->code = $newCode;
                $product->alert_quantity = $data[6];
                $product->department_id = 1;
                $product->unit_id = 1;
                $product->category_id =1;
                $product->brand_id = $brand->id;
                $product->description = null;
                $product->image = null;
                $product->status = 1;
                $product->user_id = Auth::id();;
                $product->save();

                $purchaseOrderProduct = new PurchaseOrderProduct();
                $purchaseOrderProduct->department_id = 1;
                $purchaseOrderProduct->purchase_order_id = null;
                $purchaseOrderProduct->product_id = $product->id;
                $purchaseOrderProduct->name = $product->name;
                $purchaseOrderProduct->type = 2;
                $purchaseOrderProduct->serial_no = $product->code;
                $purchaseOrderProduct->expiry_date = '2025-11-30';
                $purchaseOrderProduct->pack_size = $packSize->number;
                $purchaseOrderProduct->pack_quantity = $data[7];
                $purchaseOrderProduct->quantity = $packSize->number*$data[7];
                $purchaseOrderProduct->unit_price = $data[4];
                $purchaseOrderProduct->selling_price =$data[5];
                $purchaseOrderProduct->total = $data[7] * $data[4];
                $purchaseOrderProduct->user_id = Auth::id();
                $purchaseOrderProduct->save();

                // Inventory Log
                $inventoryLog = new PurchaseInventoryLog();
                $inventoryLog->department_id = 1;
                $inventoryLog->product_id = $product->id;
                $inventoryLog->type = 1;
                $inventoryLog->date = Carbon::now()->format('Y-m-d');
                $inventoryLog->expiry_date = '2025-11-30';
                $inventoryLog->pack_size =  $packSize->number;
                $inventoryLog->pack_quantity = $data[7];
                $inventoryLog->quantity = $packSize->number*$data[7];
                $inventoryLog->unit_price =$data[4];
                $inventoryLog->selling_price = $data[5];
                $inventoryLog->supplier_id =null;
                $inventoryLog->purchase_order_id =null;
                $inventoryLog->user_id = Auth::id();
                $inventoryLog->save();

                $lastPurchaseInventory = PurchaseInventory::where('product_id',$product->id)->where('department_id',1)->latest('id')->first();
                // Generate the next code
                $nextCodeNumber = !empty($lastPurchaseInventory) ? intval($lastPurchaseInventory->batch_no) + 1 : 1;
                $newCode = $nextCodeNumber;

                $inventory = new PurchaseInventory();

                $batch_no = $newCode ?? $inventory->batch_no;
                $department_id =1;

                $inventoryLog->batch_no = $batch_no;
                $inventoryLog->save();

                $inventory->batch_no = $batch_no;
                $inventory->department_id = 1;
                $inventory->product_id = $product->id;
                $inventory->serial_no = $product->code;
                $inventory->pack_size = $packSize->number;
                $inventory->pack_quantity = $product->inventory_pack_qty($product->id, $department_id,$batch_no);
                $inventory->quantity = $product->inventory_qty($product->id, $department_id,$batch_no);
                $inventory->unit_price = $data[4];
                $inventory->selling_price = $data[5];
                $inventory->save();

                $purchaseOrderProduct->batch_no = $inventory->batch_no;
                $purchaseOrderProduct->save();

            }

        }catch (\Exception $e){
            dd($e);
            \Log::error($e->getMessage());
            throw new \Exception("Error occurred during import: " . $e->getMessage());

        }
    }
}
