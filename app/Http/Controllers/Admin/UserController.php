<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        $hasPermission = Auth::user()->hasPermission('view_users');

        if ($hasPermission) {

            $searchKey = $request->searchKey;
            $users = User::getUsersForFilters($searchKey);
            $permissions = Permission::get()->groupBy('type')->toArray();
            $roles = Role::all();

            foreach ($users as $user) {
                $user->assigned_permissions = User::getUserPermissions($user->id);
            }

            return view('admin.users.all_users', compact('users', 'permissions', 'searchKey', 'roles'));
        } else {
            return redirect('admin/not_allowed');
        }
    }


    public function update(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('edit_users');

        if ($hasPermission) {

            $validated = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'digits:10'],
                'dob' => ['required'],
                'role' => ['required'],
            ]);

            $user = User::where('id', $request->user_id)->get()->first();

            if ($user != null) {
                $user->email = $request->email;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->phone = $request->phone;
                $user->dob = $request->dob;
                $user->role_id = $request->role;

                if ($request->file('image')) {

                    $imageName = time() . '.' . $request->image->extension();
                    $request->image->move(public_path('images/uploads/users/'), $imageName);
                    $imageUrl = 'images/uploads/users/' . $imageName;

                    $user->user_image = $imageUrl;
                }

                $user->save();

                return back()->with('success', 'User updated successfully !');
            } else {
                return back()->with('error', 'Could not find the user');
            }
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function changeStatus($id)
    {

        try {

            $hasPermission = Auth::user()->hasPermission('edit_users');

            if ($hasPermission) {

                $user = User::where('id', $id)->get()->first();

                if ($user != null) {
                    if ($user->status == 1) {
                        $user->status = User::INACTIVE;

                        $user->save();
                        return back()->with('success', 'User deactivated successfully !');
                    } else if ($user->status == 0) {
                        $user->status = User::ACTIVE;

                        $user->save();

                        return back()->with('success', 'User activated successfully !');
                    }
                } else {
                    return back()->with('error', 'Could not find the user');
                }
            } else {
                return redirect('admin/not_allowed');
            }
        } catch (\Exception $exception) {
            return back()->with('error', 'Error occured - ' . $exception->getMessage());
        }
    }

    public function userProfileUI()
    {

        $user = Auth::user();

        return view('admin.users.profile', compact('user'));
    }

    public function changeUserPassword(Request $request)
    {

        $this->validate(
            $request,
            [
                'password' => 'required|confirmed|min:8',
            ],
            [
                'password.required' => 'New password required.',
                'password.confirmed' => 'Password and confirm password does not match.',
                'password.min' => 'Minimum password length is 8 characters.',
            ]
        );

        $user = User::where('id', $request->user_id)->get()->first();

        if ($user != null) {

            $user->password = Hash::make($request->password);
            $user->save();

            return back()->with('success', 'Password changed successfully !');
        } else {

            return back()->with('error', 'Could not find the user.');
        }
    }

    public function downloadUserLog($id)
    {

        $user = User::with('userLogs')->get()->first();

        $fileName = $user->first_name . '_user_log.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('LOGGED DATE', 'ACTION', 'DESCRIPTION');

        $callback = function () use ($user, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($user->userLogs as $userLog) {


                $row['LOGGED DATE']  = $userLog->created_at;
                $row['ACTION']    = $userLog->action;
                $row['DESCRIPTION']    = $userLog->description;


                fputcsv($file, array($row['LOGGED DATE'], $row['ACTION'], $row['DESCRIPTION']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function addNewUser(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('add_users');

        if ($hasPermission) {


            $validated = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'digits:10'],
            ]);

            $savedUser = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->phone),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'username' => $request->email,
                'status' => User::ACTIVE,
                'role_id' => $request->role,
            ]);

            $customer = new Customer;

            $customer->first_name = $request->first_name;
            $customer->last_name =  $request->last_name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->address = null;
            $customer->order_id = null;
            $customer->user_id = $savedUser->id;

            Customer::create($customer->toArray());


            //saving user log
            UserLog::saveUserLog($savedUser->id, "User registered", "registration completed");

            EmailSender::sendRegistrationEmail($savedUser->id, 0);

            return Redirect::route('users.all')->with(['success' => 'User saved successfully !']);
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function getAllCustomers(Request $request)
    {
        $hasPermission = Auth::user()->hasPermission('view_customers');

        if ($hasPermission) {

            $searchKey = $request->searchKey;
            $customers = Customer::getCustomersForFilters($searchKey);

            return view('admin.customers.all_customers', compact('customers', 'searchKey'));
        } else {
            return redirect('admin/not_allowed');
        }
    }
}
