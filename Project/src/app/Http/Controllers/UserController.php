<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use App\Models\Purchase;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Show the profile for a given user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('show', $user);

            if ($user->is_admin) { // If i am an admin
                    return $this->showAdmin($user->id, $request);
            } else if (!$user->blocked) {
                if($request->flag == 'userReviews')
                    return $this->getUserReviews($request->id);
                else if($request->flag == 'wishlist')
                    return $this->getUserWishlist($request->id);
                else if($request->flag == 'userPurchases')
                    return $this->getUserPurchases($request->id);
                return view('pages.userprofile')->with('user', $user);
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter user profile"));
        }
    }

    public function showAdmin($id, Request $request)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('showAdmin', $user);


            if (!$user->blocked) {
                $selectedUser = User::findOrFail($id);
                if ($selectedUser->is_admin) {
                    if($request->flag == 'reviews')
                        return $this->getAllReviews($id);
                    else if($request->flag == 'users')
                        return $this->getNormalUsers($id);
                    else if($request->flag == 'purchases')
                        return $this->getAllPurchases($id);
                    else if($request->flag == 'userReviews')
                        return $this->getUserReviews($request->id);
                    else if($request->flag == 'wishlist')
                        return $this->getUserWishlist($request->id);
                    else if($request->flag == 'userPurchases')
                        return $this->getUserPurchases($request->id);
                    return view('pages.adminprofile')->with('user', $selectedUser)->with('userList', User::where('is_admin', '=', 'False')->orderBy('id')->get())->with('reviews', Review::orderBy('date', 'DESC')->get());

                } else
                    return view('pages.userprofile')->with('user', $selectedUser);
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter user profile"));
        }
    }


    public function getNormalUsers($id)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('showAdmin', $user);


            if (!$user->blocked) {
                $selectedUser = User::findOrFail($id);
                if ($selectedUser->is_admin) {
                    return User::where('is_admin', '=', 'False')->orderBy('id')->get();
                    /* if ($request->get('data') == 'reviews')
                        return view('partials.userlist')->with('user', $selectedUser)->with('userList', User::where('is_admin', '=', 'False')->orderBy('id')->get());
                    else if($request->get('data') == 'users')
                        return view('partials.reviewlist')->with('user', $selectedUser)->with('userList', User::where('is_admin', '=', 'False')->orderBy('id')->get()); */

                } else
                    return view('pages.userprofile')->with('user', $selectedUser);
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter user profile"));
        }
    }

    public function getAllReviews($id)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('showAdmin', $user);


            if (!$user->blocked) {
                $selectedUser = User::findOrFail($id);
                if ($selectedUser->is_admin) {
                    return Review::all();
                    /* if ($request->get('data') == 'reviews')
                        return view('partials.userlist')->with('user', $selectedUser)->with('userList', User::where('is_admin', '=', 'False')->orderBy('id')->get());
                    else if($request->get('data') == 'users')
                        return view('partials.reviewlist')->with('user', $selectedUser)->with('userList', User::where('is_admin', '=', 'False')->orderBy('id')->get()); */

                } else
                    return view('pages.userprofile')->with('user', $selectedUser);
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter user profile"));
        }
    }

    public function getUserReviews($id)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('show', $user);

            if (!$user->blocked) {
                $selectedUser =  User::findOrFail($id);
                return $selectedUser->reviews()->get();
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter user profile"));
        }
    }

    public function getAllPurchases($id)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('showAdmin', $user);


            if (!$user->blocked) {
                $selectedUser = User::findOrFail($id);
                if ($selectedUser->is_admin) {
                    return Purchase::all();
                    /* if ($request->get('data') == 'reviews')
                        return view('partials.userlist')->with('user', $selectedUser)->with('userList', User::where('is_admin', '=', 'False')->orderBy('id')->get());
                    else if($request->get('data') == 'users')
                        return view('partials.reviewlist')->with('user', $selectedUser)->with('userList', User::where('is_admin', '=', 'False')->orderBy('id')->get()); */

                } else
                    return view('pages.userprofile')->with('user', $selectedUser);
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter user profile"));
        }
    }

    public function getUserPurchases($id)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('show', $user);

            if (!$user->blocked) {
                $selectedUser =  User::findOrFail($id);
                return $selectedUser->purchases()->get();
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter user profile"));
        }
    }

    public function getUserWishlist($id)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('show', $user);

            if (!$user->blocked) {
                $selectedUser =  User::findOrFail($id);
                return $selectedUser->wishlist()->get();
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter user profile"));
        }
    }

    /**
     * Show the profile editor for a given user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showEdit()
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('edit', $user);

            if (!$user->blocked) {
                return view('pages.userprofile_edit')->with('user', $user);
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter edit profile form"));
        }
    }

    public function showEditAdmin($id)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('editAdmin', $user);

            if (!$user->blocked) {
                return view('pages.userprofile_edit', ['user' => User::find($id)]);
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter edit profile form"));
        }
    }

    /**
     * Update profile for a given user.
     *
     * @param int  $id, 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function updateProfile($id, Request $request)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();

            if ($user->is_admin) {
                $foo = User::findOrFail($id);
                $this->authorize('edit', $user);
                $this->validate($request, [
                    'name' => 'required|string|max:255',
                    'birthdate' => 'required|date',
                    'address' => 'required|string|max:255',
                    'phone_number' => 'required|numeric|min:9',
                ]);
                if (isset($request->name)) {
                    $foo->name = $request->name;
                }

                if (isset($request->birthdate)) {
                    $foo->birthdate = $request->birthdate;
                }

                if (isset($request->address)) {
                    $foo->address = $request->address;
                }

                if (isset($request->phone_number)) {
                    $foo->phone_number = $request->phone_number;
                }

                $foo->save();

                return redirect(route('profile', [
                    'id' => $id
                ]));
            } else if (!$user->blocked) {
                $this->authorize('edit', $user);
                $this->validate($request, [ // it auto returns in case of failure
                    'name' => 'required|string|max:255',
                    'password' => 'required|string|min:6|confirmed',
                    'birthdate' => 'required|date',
                    'address' => 'required|string|max:255',
                    'phone_number' => 'required|numeric|min:9',
                ]);

                if (isset($request->name)) {
                    $user->name = $request->name;
                }

                if (isset($request->birthdate)) {
                    $user->birthdate = $request->birthdate;
                }

                if (isset($request->address)) {
                    $user->address = $request->address;
                }

                if (isset($request->phone_number)) {
                    $user->phone_number = $request->phone_number;
                }
                if ($request->password != "") {
                    $user->password = bcrypt($request->password);
                }
                $user->save();

                return redirect(route('profile', [
                    'id' => $id
                ]));
            } else {
                return response(json_encode("You are blocked from the website"));
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to update profile"));
        }
    }

    /**
     * Delete profile for a given user.
     *
     * @param int  $id, 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function deleteUser($id, Request $request)
    {


        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            //$user = Auth::user();
            $user = User::find($id);
            $currUser = Auth::user();

            if ($user->is_admin) {
                return redirect()->back()->with('danger', 'You are an admin, you cannot be deleted');
            }

            if ($request->submit == "Delete Account") {
                return redirect()->back()->with('deleteconfirmation', 'Are you sure?');
            } else {
                $request->validate([
                    'userpassword' => 'required',
                ]);
                $password = bcrypt($request->userpassword);
                if (Hash::check($request->userpassword, $currUser->password)) {
                    $user->name = "Anonymous";
                    $user->birthdate = date('Y-m-d');
                    $user->address = "Anonymous";
                    $user->phone_number = "111111111";
                    $user->password = bcrypt($user->email);
                    $user->blocked = True;
                    $user->update();
                    if ($user->id == $currUser->id) {
                        return redirect()->route('logout');
                    } else {
                        return redirect()->route('profile');
                    }
                } else {
                    return redirect()->back()->with('danger', 'User cannot be deleted, incorrect password');
                }
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to enter edit profile form"));
        }
    }

    /**
     * Block and unblock user profile.
     *
     * @param int  $id, 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function blockUser($id, Request $request)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $foo = User::findOrFail($id);
            if ($user->is_admin && $foo->id != $user->id) {
                $this->authorize('edit', $user);
                if (!$foo->blocked) {
                    $foo->blocked = True;
                } else {
                    $foo->blocked = False;
                }
                $foo->update();
                return redirect(route('profile', ['id' => $id]));
            }
            return redirect()->back()->with('danger', 'You are an admin, you cannot block yourself');
        } catch (\Exception $e) {
            return response(json_encode("Failed to block user"));
        }
    }

    /**
     * Update the user's purchase status
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function updatePurchaseStatus(Request $request)
    {
        try {
            $purchase = Purchase::findOrFail($request->id);
        } catch (\Exception $e) {
            return response(json_encode("Error updating purchase status"), 400);
        }

        if ($purchase != null) {

            try {
                $purchase->pur_status = $request->status;
                $purchase->update();
                return response(json_encode("Purchase status was updated"), 200);
            } catch (\Exception $e) {
                return response(json_encode("Error updating purchase status"), 400);
            }

        }
    }
}