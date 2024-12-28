<?php

namespace App\Http\Controllers;

use App\Models\ModelHasPermission;
use App\Models\PurchaseOrders;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use PDF;

class CustomController extends Controller
{
    public function refresh_admin_permissions()
    {
        // Getting all permissions
        $permissions = Permission::where('guard_name','web')->get()->toArray();

        // Getting the users who has admin and super admin roles
        $users = User::role(['super_admin', 'admin'])->get();

        if(count($permissions) && count($users))
        {
            foreach ($permissions as $permission) {
                foreach ($users as $key => $user)
                {
                    ModelHasPermission::updateOrCreate(
                        [
                            'permission_id' => $permission['id'],
                            'model_type' => 'App\Models\User',
                            'model_id' => $user->id
                        ]
                    );
                }
            }
        }
    }

    public function get_pdf_view($id)
    {
        $purchase_order = PurchaseOrders::find($id);
        

        if (!$purchase_order) {
            return abort(404);
        }

        $data["email"] = "aatmaninfotech@gmail.com";

        $data["title"] = "From ItSolutionStuff.com";

        $data["purchase_order"] = $purchase_order;

        // dd($purchase_order->pur_orderItems);

        $pdf = PDF::loadView('business.pur_orders.download', $data);

        return $pdf->stream();


    }
}
