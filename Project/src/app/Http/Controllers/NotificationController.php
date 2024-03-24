<?php
 
namespace App\Http\Controllers;
 
use App\Models\Product;
use App\Models\User;
use App\Models\ProductVariation;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Show the product page for a given product.
     * Shows default product variation
     * 
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function notifyUser($id, $variation_id, $message)
    {   
        $user = User::findOrFail($id);
        if($message == 'Product available' || $message == 'Product out of stock' || $message == 'Price change') {
            $dontSend = 0;
            foreach($user->notifications()->get() as $notification) {
                if($notification->notification_type == $message) {
                    $dontSend = 1;
                }
            }
            if (!$dontSend) {
                DB::transaction(function () use ($message, $user, $variation_id) {
                    $notification = new Notification;
                    $notification->notification_type = $message;
                    $notification->user_id = $user->id;
                    $notification->product_variation_id = $variation_id;
                    $notification->save();
                });
            }
            return true;
        } else {
            return false;
        }
    }

    public function deleteNotification($type, Request $request)
    {   
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('edit', $user);
            foreach($user->notifications()->get() as $notification) {
                if($notification->notification_type == $type) {
                    $notification->delete();
                }
            }
            return redirect()->back();
           
        } catch (\Exception $e) {
            return response(json_encode("Failed to delete notification"));
        }
    }

}