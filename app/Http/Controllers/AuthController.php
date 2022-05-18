<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use App\Models\fixed_ledger_account;
use App\Models\depreciation_method;
use App\Models\account_type;
use App\Models\category_name;
use App\Models\controller_account;

class AuthController extends Controller
{
    function loginView()
    {
        return view("login");
    }

    function registerView()
    {
        return view("register");
    }

    function doLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',   // required and email format validation
            'password' => 'required', // required and number field validation

        ]); // create the validations
        if ($validator->fails())   //check all validations are fine, if not then redirect and show error messages
        {

            return back()->withInput()->withErrors($validator);
            // validation failed redirect back to form

        } else {
            //validations are passed try login using laravel auth attemp
            if (\Auth::attempt($request->only(["email", "password"]))) {
                return redirect("dashboard")->with('success', 'Login Successful');
            } else {
                return back()->withErrors( "Invalid credentials"); // auth fail redirect with error
            }
        }
    }

    function doRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',   // required and email format validation
            'password' => 'required|min:8', // required and number field validation
            'confirm_password' => 'required|same:password',

        ]); // create the validations
        if ($validator->fails())   //check all validations are fine, if not then redirect and show error messages
        {

            return back()->withInput()->withErrors($validator);
            // validation failed redirect back to form

        } else {
            //validations are passed, save new user in database
            $User = new User;
            $User->name = $request->name;
            $User->email = $request->email;
            $User->password = bcrypt($request->password);
            $User->save();

            return redirect("login")->with('success', 'You have successfully registered, Login to access your dashboard');
        }
    }
   // show dashboard
    function dashboard()
    {
        return View('dashboard')
        ->with('fixeddata', fixed_ledger_account::all())
        ->with('depre_methods', depreciation_method::all())
        ->with('acc_type', account_type::all())
        ->with('categ_name', category_name::all())
        ->with('control_account', controller_account::all());
        // $fixeddata=fixed_ledger_account::all();
        // return view ("dashboard",compact("fixeddata"));
        //return view("dashboard");
    }

//data Save function
     public function conformord(Request $request)
     {
          $saveconform = new fixed_ledger_account;

          $saveconform->account_type=$request->account_type;
          $saveconform->category_code=$request->category_code;
          $saveconform->main_account_code=$request->main_accouns_code;
          $saveconform->ledger_account_name=$request->legger_accouns_name;
          $saveconform->status=$request->active;
          $saveconform->category_name=$request->category_name;
          $saveconform->main_account_name=$request->main_accouns_name;
          $saveconform->ledger_account_code=$request->Legger_accouns_code;
          $saveconform->cost=$request->cost;
          $saveconform->life_time=$request->life_time;
          $saveconform->depreciation_rate=$request->depreciation_rate;
          $saveconform->depreciation_method=$request->depreciation_method;
          $saveconform->date_of_depreciation=$request->date_of_depreciation;

          $saveconform->save();
          return redirect()->back();
          //return view("/dashboard");
     }

//data delete function
function delete($id)
{
    $deketeform = fixed_ledger_account::find($id);
    $deketeform->delete();
    return redirect()->back();
}

//data edit function
function edit($id)
{
   return redirect()->action(
        [AuthController::class, 'dashboard'], ['id' => $id]
    );

    //return back()->withInput();
    //return redirect()->route('dashboard', ['id' => 1]);
    //return view("dashboard");     Route::get('/dashboard', [AuthController::class,"dashboard"]);
}

//data update function
public function update($id, Request $request) {

    fixed_ledger_account::findOrFail($id)->update($request->all());

    return redirect(route('posts.all'))->with('status', 'Post updated!');
}


    // logout method to clear the sesson of logged in user
    function logout()
    {
        \Auth::logout();
        return redirect("/")->with('success', 'Logout successfully');;
    }
}