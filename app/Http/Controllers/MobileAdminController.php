<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\WorkExperience;
use App\Detail;
use Auth;
use App\Http\Requests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\DetailRequest;

class MobileAdminController extends Controller
{
	/**
     * view the list of all senior citizens
     *
     *
     * @return view with pagination
     **/
    public function searchCitizens() {
        $term = Input::get('term');
        $results = array(); $data = array();
        $queries = Detail::where('firstname', 'LIKE', '%'.$term.'%')->get();

        foreach($queries as $query) {
            if($query->user->type == '2') {
                $dob = $query->date_of_birth->toFormattedDateString();
                $add_permanent = implode(', ', array_filter(array($query->address_permanent, $query->city_permanent, $query->state_permanent, $query->country_permanent)));
                array_push($results, ['name' => $query->firstname, 'user_id' => $query->user_id, 'location' => $query->location, 'date_of_birth' => $dob, 'add_permanent' => $add_permanent, 'expertise_in' => $query->expertise_in]);
            }
        }
        $data['list'] = $results;
        return response()->json($data);
    }

    /**
     * view the list of all filtered profile viewers
     *
     * @param Request request
     * @return view with pagination
     **/
    public function searchViewers(Request $request) {
        $term = Input::get('term');
        $results = array(); $data = array();
        $queries = Detail::where('firstname', 'LIKE', '%'.$term.'%')->get();

        foreach($queries as $query) {
            if($query->user->type == '1') {
                $dob = $query->date_of_birth->toFormattedDateString();
                $add_permanent = implode(', ', array_filter(array($query->address_permanent, $query->city_permanent, $query->state_permanent, $query->country_permanent)));
                array_push($results, ['name' => $query->firstname, 'user_id' => $query->user_id, 'website' => $query->website, 'date_of_birth' => $dob, 'add_permanent' => $add_permanent, 'expertise_in' => $query->expertise_in, 'description' => $query->description]);
            }
        }
        $data['list'] = $results;
        return response()->json($data);
    }

    /**
     * view the list of all authorized departments
     *
     *
     * @return view with pagination
     **/
    public function departments() {
    	$departments = User::departments()->get();
        $departmentDetails = array();
        foreach($departments as $department) {
            $details = $department->detail()->get();
            if(count($details) != 0)
                array_push($departmentDetails, $details);
        }
        $perPage = 2;
        $currentPage = Input::get('page', 1) - 1;
        $total = count($departmentDetails);
        $pagedData = array_slice($departmentDetails, $currentPage * $perPage, $perPage);
        $departments = new LengthAwarePaginator($pagedData, $total, $perPage, $currentPage+1);
        $departments->setPath(Input::getBasePath());

        return view('admin.departments', compact('departments'));
    }

    /**
    * open the edit page to edit details of a user
    *
    * @param Detail detail
    * @return view
    **/
	public function edit(User $user) {
		// return view('admin_edit', compact('detail'));
        $type = $user->type;
        $details = $user->detail()->get()[0];
        switch($type) {
            case '1':
                return view('admin.profile_viewer_edit', compact('user', 'details'));
                break;
            case '2':
                return view('admin.senior_citizen_edit', compact('user', 'details'));
                break;
            case '3':
                return view('admin.department_edit', compact('user', 'details'));
                break;
        }
	}

	/**
    * modify the details of a user through admin panel
    *
    * @param Request request
    * @return admin home page
    **/
	public function update(Request $request) {
        $data = $request->all();
        $errors = array();
        $response = array();$detail = array();
        $validator = Validator::make($data, [
            'currentuserid' => 'required',
            'firstname' => 'required',
            'date_of_birth' => 'date',
            'retirement' => 'date',
            'contact_mobile' => 'max:10',
            'contact_home' => 'max:10',
            'contact_work' => 'max:10',
            'email_personal' => 'email',
            'email_work' => 'email',
            'email_other' => 'email',
            'members' => 'numeric',
            'website' => 'url',
            'fb' => 'active_url',
            'google' => 'active_url',
            'linkedin' => 'active_url'
        ]);
        if($validator->fails())
            $errors = $this->formatValidationErrors($validator);
        else {
            $user = User::find($request->currentuserid)->update($data);
            $detail = User::find($request->currentuserid)->detail()->update($data);
        }
        $response['details'] = $detail;
        $response['user'] = $user;
        $response['errors'] = $errors;
        return response()->json($response);
	}

    /**
    * open the view page to edit details of a user
    *
    * @param User $user
    * @return view
    **/
    public function show(User $user) {
        $type = $user->type;
        $detail = $user->detail()->get()[0];
        switch($type) {
            case '1':
                return view('admin.profile_viewer_profile', compact('detail'));
                break;
            case '2':
                $work_experiences = $user->work_experiences()->get();
                return view('admin.senior_citizen_profile', compact('detail', 'work_experiences'));
                break;
            case '3':
                return view('admin.department_profile', compact('detail'));
                break;
        }
    }

    /**
     * download cv for a user if it exists
     *
     * @param Detail detail
     * @return download pdf
     **/
    public function download(Detail $detail) {
        $cv = $detail->cv;
        return response()->download(public_path('cv\\'.$cv));
    }
}