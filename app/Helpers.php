<?php

use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;

if (!function_exists('getClientIdAndSecret')) {

    function getClientIdAndSecret($provider)
    {
        $client = DB::table('oauth_clients')->where('provider', $provider)->first();
        return $client;
    }
}

if (!function_exists('file_upload')) {

    function file_upload($file, $path)
    {
        $path_store = Storage::disk('s3')->put($path, $file);

        return $path_store;
    }
}

if (!function_exists('resize_file_upload')) {

    function resize_file_upload($upload_file, $path, $height, $width)
    {
        // Ensure the directory exists in the storage path
        $storageDirectory = storage_path('app/resize_image');
        if (!File::exists($storageDirectory)) {
            File::makeDirectory($storageDirectory, 0755, true);
        }

        $image = $upload_file;
        $image_name = time() . '.' . $image->getClientOriginalExtension();
        $filePath = $storageDirectory . '/' . $image_name;

        // Resize and save the image in the storage path
        $resize_image = Image::make($image->getRealPath());
        $resize_image->resize($height, $width)->save($filePath);

        $s3Path = $path . '/' . $image_name;
        $path_store = Storage::disk('s3')->put($s3Path, file_get_contents($filePath));

        if ($path_store) {
            File::delete($filePath); // Delete the local file
        }

        // Get the S3 URL of the uploaded file

        return $s3Path;
    }
}


if (!function_exists('otp_generate')) {

    function otp_generate($length, $type)
    {
        // 0 = Digits
        if ($type == 0) {
            $pool = '0123456789';
        }

        // 1 = Letter Only
        if ($type == 1) {
            $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        // 2 = Digit and Letter
        if ($type == 2) {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $otp = substr(str_shuffle(str_repeat($pool, $length)), 0, $length);

        return $otp;
    }
}

if (!function_exists('mailNotification')) {
    function mailNotification($data)
    {
        Mail::send($data["view"], $data, function ($message) use ($data) {
            $message->to($data["email"])
                ->subject($data["title"]);
        });
    }
}

if (!function_exists('mailNotificationAttach')) {
    function mailNotificationAttach($data, $pdf)
    {
        Mail::send($data["view"], $data, function ($message) use ($data, $pdf) {
            $message->to($data["email"])
                ->subject($data["title"])
                ->attachData($pdf, "attachment.pdf");
        });
    }
}

if (!function_exists('action_buttons')) {
    function action_buttons($action, $edit_url, $route_id, $view_url)
    {
        if ($edit_url != '') {
            $action .= '<a href="' . $edit_url . '" class="dropdown-item" title="Edit"><i class="fa-solid fa-pen-to-square m-r-5"></i>Edit</a> ';
        }

        if (Auth()->user()->hasRole('super_admin')) {
            $action .= '<button type="button" class="dropdown-item" title="Delete" onclick="deleteConfirmation(' . $route_id . ')" data-id="' . $route_id . '"><i class="fa-solid fas fa-trash m-r-5"></i>Delete</button>  ';
        }

        if ($view_url != '') {
            $action .= '<a class="dropdown-item" title="View"  href="' . $view_url . '"><i class="fa-solid fa-eye m-r-5"></i>View</a>';
        }

        return $action;
    }
}

if (!function_exists('refno_generate')) {

    function refno_generate($length, $type, $id)
    {
        // 0 = Digits
        if ($type == 0) {
            $pool = '0123456789';
        }

        // 1 = Letter Only
        if ($type == 1) {
            $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        // 2 = Digit and Letter
        if ($type == 2) {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $id_length = strlen($id);
        $ref_length = $length - $id_length;

        $ref_no = $id;
        if ($ref_length > 0) {
            $otp = substr(str_shuffle(str_repeat($pool, $ref_length)), 0, $ref_length);
            $ref_no = $otp . $id;
        }

        return $ref_no;
    }
}

if (!function_exists('api_client_business_id')) {

    function api_client_business_id($request)
    {
        $business_id = Auth::guard('api-client')->user()->business_id;
        if (isset($request->salon_id) && !empty($request->salon_id)) {
            $business_id = $request->salon_id;
        }

        return $business_id;
    }
}

if (!function_exists('action_btns')) {
    function action_btns($action, $user, $permission, $edit_url, $route_id, $read_url,  $view_url)
    {
        if ($edit_url != '' && $user->hasPermissionTo('Update_' . $permission)) {
            $action .= '<a class="dropdown-item" title="Edit" href="' . $edit_url . '"><i class="fa-solid fa-pen-to-square m-r-5"></i> Edit</a>';
        }

        if ($user->hasPermissionTo('Delete_' . $permission)) {
            $action .= '<a class="dropdown-item" title="Delete" href="javascript:;" onclick="deleteConfirmation(' . $route_id . ')" data-id="' . $route_id . '"><i class="fa-solid fas fa-trash m-r-5"></i> Delete</a>';
        }

        if ($user->hasPermissionTo('Read_' . $permission) && $read_url != '') {
            $action .= '<a class="dropdown-item" title="View" href="' . $read_url . '"><i class="fa-solid fa-eye m-r-5"></i> View</a>';
        }

        if ($user->hasPermissionTo('Read_' . $permission) && $view_url != '') {
            $action .= '<a class="dropdown-item" title="View" href="' . $view_url . '"><i class="fa-solid fa-eye m-r-5"></i> View</a>';
        }

        return $action;
    }
}

if (!function_exists('action_btns_pur')) {
    function action_btns_pur($action, $user, $permission, $view_url, $route_id,  $pur_url)
    {
        if ($view_url != '' && $user->hasPermissionTo('Update_' . $permission)) {
            $action .= '<a class="dropdown-item"  title="View" href="' . $view_url . '"><i class="fa fa-eye m-r-5"></i>View</a>';
        }

        if ($user->hasPermissionTo('Delete_' . $permission)) {
            $action .= '<a class="dropdown-item"  title="Delete" href="javascript:;" onclick="deleteConfirmation(' . $route_id . ')" data-id="' . $route_id . '"><i class="fa-solid fas fa-trash m-r-5"></i> Delete</a>';
        }

        if ($user->hasPermissionTo('Create_Payement')) {
            $action .= '<a href="' . $pur_url . '"  class="dropdown-item"  title="Payment"><i class="fa-solid fa-dollar-sign m-r-10"></i>Payment</a>';
        }

        return $action;
    }
}

if (!function_exists('action_btns_model')) {
    function action_btns_model($action, $user, $permission, $edit_url, $route_id, $read_url, $view_url)
    {
        if ($edit_url != '' && $user->hasPermissionTo('Update_' . $permission)) {
            $action .= '<a class="dropdown-item"  title="Edit" href="javascript:;" onclick="openUpdateModal(' . $route_id . ')"><i class="fa-solid fa-pen-to-square m-r-5"></i> Edit</a>';
        }

        if ($user->hasPermissionTo('Delete_' . $permission)) {
            $action .= '<a class="dropdown-item"  title="Delete" href="javascript:;" onclick="deleteConfirmation(' . $route_id . ')" data-id="' . $route_id . '"><i class="fa-solid fas fa-trash m-r-5"></i> Delete</a>';
        }

        if ($user->hasPermissionTo('Read_' . $permission) && $read_url != '') {
            $action .= '<a class="dropdown-item"  title="Payment" href="javascript:;" onclick="openViewModal(' . $route_id . ')"><i class="fa-solid fa-eye m-r-5"></i> View</a>';
        }

        if ($user->hasPermissionTo('Read_' . $permission) && $view_url != '') {
            $action .= '<a class="dropdown-item" title="View" href="' . $view_url . '"><i class="fa-solid fa-eye m-r-5"></i> View</a>';
        }

        return $action;
    }
}

if (!function_exists('user_permission_check')) {
    function user_permission_check($user, $permission)
    {
        $status = false;

        if ($user->hasPermissionTo($permission)) {
            $status = true;
        }

        return $status;
    }
}

if (!function_exists('check_snap_status')) {
    function check_snap_status($business_id)
    {
        $status = false;

        $business = Business::find($business_id);

        if ($business) {
            if ($business->snap_auth_key != "") {
                $api_url = env('SNAP_API_URL') . '/authentication';

                $headers = [
                    'Content-Type' => 'application/json'
                ];

                $requestOptions = [
                    'json' => [
                        'api_key' => $business->snap_auth_key
                    ],
                    'headers' => $headers
                ];

                $guzzleHttpClient = new GuzzleClient();

                try {
                    $response = $guzzleHttpClient->post($api_url, $requestOptions);
                    $responseContent = $response->getBody()->getContents();
                    $responseContentArray = json_decode($responseContent, true);

                    $status = $responseContentArray['status'];
                } catch (ClientException $ex) {
                    $response = $ex->getResponse();
                    $responseContent = $response->getBody()->getContents();
                    $responseContentArray = json_decode($responseContent, true);

                    $status = false;
                }
            }
        }

        return $status;
    }
}

if (!function_exists('send_slsnap_message')) {
    function send_slsnap_message($request)
    {
        $status = false;

        $api_url = env('SNAP_API_URL') . '/send_message';

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $requestOptions = [
            'json' => [
                'ApiKey' => $request->snap_auth_key,
                'firstname' => $request->client_name,
                'phone' => $request->client_contact,
                'message' => $request->message
            ],
            'headers' => $headers
        ];

        $guzzleHttpClient = new GuzzleClient();

        try {
            $response = $guzzleHttpClient->post($api_url, $requestOptions);
            $responseContent = $response->getBody()->getContents();
            $responseContentArray = json_decode($responseContent, true);

            $status = $responseContentArray['status'];
        } catch (ClientException $ex) {
            $response = $ex->getResponse();
            $responseContent = $response->getBody()->getContents();
            $responseContentArray = json_decode($responseContent, true);

            $status = false;
        }

        return $status;
    }
}


if (!function_exists('auto_increment_id')) {
    function auto_increment_id($id)
    {
        $auto_id = $id;
        if ($id < 10) {
            $auto_id = '000' . $id;
        }

        if ($id >= 10 && $id < 100) {
            $auto_id = '00' . $id;
        }

        if ($id >= 100 && $id < 1000) {
            $auto_id = '0' . $id;
        }

        return $auto_id;
    }
}

if (!function_exists('action_btns_po')) {
    function action_btns_po($action, $user, $edit_url, $can_delete, $read_url, $permission, $order)
    {
        if ($edit_url != '' && $user->hasPermissionTo('Update_' . $permission)) {
            $action .= '<a class="dropdown-item"  title="Edit" href="' . $edit_url . '"><i class="fa-solid fa-pen-to-square m-r-5"></i> Edit</a>';
        }

        if ($can_delete && $user->hasPermissionTo('Delete_' . $permission)) {
            $action .= '<a class="dropdown-item"  title="Delete" href="javascript:;" onclick="deleteConfirmation(' . $order->id . ')" data-id="' . $order->id . '"><i class="fa-solid fas fa-trash m-r-5"></i> Delete</a>';
        }

        if ($user->hasPermissionTo('PO_Approval') && $order->status == 0) {
            $action .= '<a class="dropdown-item" title="Approve" href="javascript:;" onclick="change_status(' . $order->id . ',1)" data-id="' . $order->id . '"><i class="fa-solid fas fa-check m-r-5"></i> Approve</a>';
        }

        if ($user->hasPermissionTo('PO_Hold') && $order->status == 1) {
            $action .= '<a class="dropdown-item" title="On Hold" href="javascript:;" onclick="change_status(' . $order->id . ',2)" data-id="' . $order->id . '"><i class="fa-solid fas fa-pause m-r-5"></i> On Hold</a>';
        }

        if ($user->hasPermissionTo('PO_Cancel') && in_array($order->status, [0, 1, 2])) {
            $action .= '<a class="dropdown-item" title="Cancel" href="javascript:;" onclick="change_status(' . $order->id . ',3)" data-id="' . $order->id . '"><i class="fa-solid fas fa-times m-r-5"></i> Cancel</a>';
        }

        if ($user->hasPermissionTo('PO_Fullfillment') && in_array($order->status, [1, 2])) {
            $action .= '<a class="dropdown-item" title="Fullfillment" href="javascript:;" onclick="change_status(' . $order->id . ',4)" data-id="' . $order->id . '"><i class="fa-solid fas fa-check m-r-5"></i> Fullfillment</a>';
        }

        if ($user->hasPermissionTo('PO_Received') && $order->status == 4) {
            $route = route('business.purchaseorder.order.receive', $order->ref_no);
            $action .= '<a class="dropdown-item" title="Received" href="' . $route . '" data-id="' . $order->id . '"><i class="fa-solid fas fa-check m-r-5"></i>Received</a>';
        }

        if ($user->hasPermissionTo('PO_Closed') && $order->status == 5) {
            $action .= '<a class="dropdown-item" title="Close" href="javascript:;" onclick="change_status(' . $order->id . ',6)" data-id="' . $order->id . '"><i class="fa-solid fas fa-check m-r-5"></i> Close</a>';
        }

        if ($user->hasPermissionTo('Create_PurchaseOrder') && $order->status == 6) {
            $action .= '<a class="dropdown-item" title="Reorder" href="javascript:;" onclick="re_order(' . $order->id . ')" data-id="' . $order->id . '"><i class="fa-solid fas fa-redo-alt m-r-5"></i> Reorder</a>';
        }

        $permissons = ['PO_Approval', 'PO_Hold', 'PO_Cancel', 'PO_Fullfillment', 'PO_Received', 'PO_Closed'];

        if ($user->hasAnyPermission($permissons)) {
            $action .= '<a class="dropdown-item" title="View Order" href="' . $read_url . '"><i class="fa-solid fa-list m-r-5"></i> View Order</a>';
        }

        return $action;
    }
}

if (!function_exists('action_btns_po_return')) {
    function action_btns_po_return($action, $user, $edit_url, $can_delete, $read_url, $permission,  $return, $view_url)
    {
        if ($edit_url != '' && $user->hasPermissionTo('Update_' . $permission)) {
            $action .= '<a class="dropdown-item" title="Edit" href="' . $edit_url . '"><i class="fa-solid fa-pen-to-square m-r-5"></i> Edit</a>';
        }

        if ($can_delete && $user->hasPermissionTo('Delete_' . $permission)) {
            $action .= '<a class="dropdown-item"  title="Delete" href="javascript:;" onclick="deleteConfirmation(' . $return->id . ')" data-id="' . $return->id . '"><i class="fa-solid fas fa-trash m-r-5"></i> Delete</a>';
        }

        if ($user->hasPermissionTo('PO_Return_Approval') && $return->status == 0) {
            $action .= '<a class="dropdown-item" title="Approve" href="javascript:;" onclick="change_status(' . $return->id . ',1)" data-id="' . $return->id . '"><i class="fa-solid fas fa-check m-r-5"></i> Approve</a>';
        }

        if ($user->hasPermissionTo('PO_Return_Hold') && $return->status == 1) {
            $action .= '<a class="dropdown-item" title="On Hold" href="javascript:;" onclick="change_status(' . $return->id . ',2)" data-id="' . $return->id . '"><i class="fa-solid fas fa-pause m-r-5"></i> On Hold</a>';
        }

        if ($user->hasPermissionTo('PO_Return_Cancel') && in_array($return->status, [0, 1, 2])) {
            $action .= '<a class="dropdown-item" title="Cancel" href="javascript:;" onclick="change_status(' . $return->id . ',3)" data-id="' . $return->id . '"><i class="fa-solid fas fa-times m-r-5"></i> Cancel</a>';
        }

        if ($user->hasPermissionTo('PO_Return_Fullfillment') && in_array($return->status, [1, 2])) {
            $action .= '<a class="dropdown-item" title="Fullfillment" href="javascript:;" onclick="change_status(' . $return->id . ',4)" data-id="' . $return->id . '"><i class="fa-solid fas fa-check m-r-5"></i> Fullfillment</a>';
        }

        if ($user->hasPermissionTo('PO_Return_Received') && $return->status == 4) {

            $action .= '<a class="dropdown-item" title="Return" href="javascript:;" onclick="change_status(' . $return->id . ',5)" data-id="' . $return->id . '"><i class="fa-solid fas fa-check m-r-5"></i>Return</a>';
        }

        if ($user->hasPermissionTo('PO_Return_Closed') && $return->status == 5) {
            $action .= '<a class="dropdown-item" title="Close" href="javascript:;" onclick="change_status(' . $return->id . ',6)" data-id="' . $return->id . '"><i class="fa-solid fas fa-check m-r-5"></i> Close</a>';
        }

        $permissons = ['PO_Return_Approval', 'PO_Return_Hold', 'PO_Return_Cancel', 'PO_Return_Fullfillment', 'PO_Return_Received', 'PO_Return_Closed'];

        if ($user->hasAnyPermission($permissons)) {
            $action .= '<a class="dropdown-item" title="View Return" href="' . $read_url . '"><i class="fa-solid fa-list m-r-5"></i> View Return</a>';
        }

        if ($user->hasPermissionTo('Read_' . $permission) && $view_url != '') {
            $action .= '<a class="dropdown-item" title="View" href="' . $view_url . '"><i class="fa-solid fa-eye m-r-5"></i> View</a>';
        }

        return $action;
    }
}

if (!function_exists('user_any_permission_check')) {
    function user_any_permission_check($user, $permission)
    {
        $status = false;

        if ($user->hasAnyPermission($permission)) {
            $status = true;
        }

        return $status;
    }
}
