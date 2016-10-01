<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use App\Http\Requests\DetailRequest;
use App\Detail;
use App\WorkExperience;
use App\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Validator;

class MobileUserController extends Controller
{
    public function __construct() {
    	
    }

    public function index(Request $request) {
        $type = User::find($request->id)->type;
        $details = User::find($request->id)->detail()->get()->count();
        $data['details'] = $details;
    	switch($type) {
    		case '1':
    			return response()->json($data);
    			break;
    		case '2':
                $work_experiences = User::find($request->id)->work_experiences()->get()->count();
                $data['work_experiences'] = $work_experiences;
    			return response()->json($data);
    			break;
    		case '3':
    			return response()->json($data);
    			break;
    	}
    }

    public function profile(Request $request) {
        $user = User::find($request->id);
        $details = $user->detail()->get();
        $d = $details[0];
        $details[0]['name'] = $d['firstname']." ".$details[0]['middlename']." ".$details[0]['lastname'];
        $details[0]['date_of_birth2'] = $d['date_of_birth']->toFormattedDateString();
        $details[0]['age'] = $d['date_of_birth']->diffInYears();
        $details[0]['retirement2'] = $d['retirement']->toFormattedDateString();
        $details[0]['retired'] = $d['retirement']->diffForHumans();
        $details[0]['retirement_age'] = $d['retirement']->diffInYears($details[0]['date_of_birth']);
        $details[0]['contact'] = $user->contact;
        $details[0]['email'] = $user->email;
        $details[0]['add_permanent'] = implode(', ', array_filter(array($d['address_permanent'], $d['city_permanent'], $d['state_permanent'], $d['country_permanent'])));
        $data['details'] = $details;
        $details[0]['add_current'] = implode(', ', array_filter(array($d['address_current'], $d['city_current'], $d['state_current'], $d['country_current'])));
        $data['details'] = $details;
        $details[0]['add_alternate'] = implode(', ', array_filter(array($d['address_alternate'], $d['city_alternate'], $d['state_alternate'], $d['country_alternate'])));
        $data['details'] = $details;
        // return response()->json($data);
        $a = json_encode($data, JSON_PRETTY_PRINT);
        return $a;
        // $a = response()->json($data);
    }

    public function startVerification() {
        $verify = Auth::user()->verify;
        if($verify=='0')
            return redirect('verification')->with('verif_id', Auth::user()->id);
    }

    public function workExperience(Request $request) {
        $work_experiences = User::find($request->id)->work_experiences()->latest('to')->get();
        $data['work_experiences'] = $work_experiences;
        return response()->json($data);
    }

    /**
     * view the list of filtered senior citizens
     *
     * @param Request request
     * @return view with pagination
     **/
    public function view(Request $request) {
        $data = $request->all();
        unset($data['_token']);
        unset($data['page']);
        if(!count($data))
            return redirect('search_senior_citizens');
        $data = array_filter($data);
        $query = WorkExperience::where($data);
        $seniorCitizens = $query->get();
        $seniorCitizenDetails = array();
        foreach($seniorCitizens as $seniorCitizen) {
            $details = $seniorCitizen->user->detail()->get();
            $verify = $seniorCitizen->user->verify;
            if(count($details) != 0 && $verify==1)
                array_push($seniorCitizenDetails, $details);
        }
        $perPage = 2;
        $currentPage = Input::get('page', 1) - 1;
        $total = count($seniorCitizenDetails);
        $pagedData = array_slice($seniorCitizenDetails, $currentPage * $perPage, $perPage);
        $seniorCitizens = new LengthAwarePaginator($pagedData, $total, $perPage, $currentPage+1);
        $seniorCitizens->setPath(Input::getBasePath());
        return view('profile_viewer.view', compact('seniorCitizens'));
    }

    /**
     * show details of particular senior citizen
     *
     * @param User user
     * @return view of user
     **/
    public function show(Request $request) {
        $user = User::find($request->id);
        $data['details'] = $user->detail()->get()[0];
        $data['work_experiences'] = $user->work_experiences()->get();

        return response()->json($data);
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

    /**
     * store the work experiences in database
     *
     * @param Request request
     * @return profile page with flash or some alert
     **/
    public function storeExperience(Request $request) {
        $validator = Validator::make($request, [
            'id' => 'required',
            'sector' => 'required',
            'category' => 'required',
            'company' => 'required',
            'position' => 'required',
            'role' => 'required',
            'from' => 'date|required',
            'to' => 'date|required'
        ]);
        if($validator->fails())
            $errors = $this->formatValidationErrors($validator);
        else {
            $newexperience = User::find($request->id)->work_experiences()->create($request->all());
        }
        $response['work_experiences'] = $newexperience;
        $response['errors'] = $errors;
        return response()->json($response);
    }

    /**
     * store the initial details in database
     *
     * @param DetailRequest request
     * @return profile page with flash or some alert
     **/
    public function store(Request $request) {
        $data = $request->all();
        $errors = array();$response = array(); $detail = array();
        $validator = Validator::make($data, [
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
            if(User::find($request->id)->detail()->get()->count() == 0)
                $detail = User::find($request->id)->detail()->create($data);
            else
                $detail = User::find($request->id)->detail()->get();
        }
        // if(Input::file('photofile')->isValid()) {
        //     $destination = 'photo';
        //     $extension = Input::file('photofile')->getClientOriginalExtension();
        //     $filename = time().'.'.$extension;
        //     Input::file('photofile')->move($destination, $filename);
        //     $data['photo'] = $filename;
        // }
        // if(Auth::user()->type=='2' && Input::file('cvfile')->isValid()) {
        //     $destination = 'cv';
        //     $extension = Input::file('cvfile')->getClientOriginalExtension();
        //     $filename = time().'.'.$extension;
        //     Input::file('cvfile')->move($destination, $filename);
        //     $data['cv'] = $filename;
        // }
       $response['details'] = $detail;
       $response['errors'] = $errors;
       return response()->json($response);
    }

    /**
     * update the details in database
     *
     * @param DetailRequest request
     * @return profile page with flash or some alert
     **/
    public function update(Request $request) {
        $data = $request->all();
        $errors = array();
        $response = array();$detail = array();
        $validator = Validator::make($data, [
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
            'id' => 'required'
        ]);
        if($validator->fails())
            $errors = $this->formatValidationErrors($validator);
        else {
            $detail = User::find($request->id)->detail()->update($data);
        }
        $response['details'] = $detail;
        $response['errors'] = $errors;
        return response()->json($response);
    }

    public function uploadPhoto(Request $request) {
        $data = $request->all();
        $response = array();
        // if(Input::file('photo')->isValid()) {
        //     $destination = 'photo';
        //     $extension = Input::file('photo')->getClientOriginalExtension();
        //     $filename = time().'.'.$extension;
        //     Input::file('photo')->move($destination, $filename);
        //     $data['photo'] = $filename;
        // }
        // $detail = User::find($request->currentuserid)->detail()->update($data);
        $detail = User::find($request->id)->detail();
        $response['details'] = $detail;
        return response()->json($response);
    }

    /**
     * store the bulk details in database
     *
     * @param Request request
     * @return profile page with flash or some alert
     **/
    public function bulkUpload(Request $request) {
        $this->validate($request, [
            'file' => 'required|max:1024'
        ]);
        if($request->hasFile('file')) {
            $file = Input::file('file');
            
            $extension = $file->getClientOriginalExtension();
            if ($extension!='csv' && $extension!='xls' && $extension!='xlsx') {
                return redirect('upload')->with('filetype_error','upload a csv or an excel file only');
            }
           
            if (($handle = fopen($file,'r')) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ',')) !==FALSE) {
                    if(!isset($data[0]) || empty($data[0]) || !isset($data[9]) || empty($data[9]))
                        continue;
                    if(strtolower($data[0]) == "firstname")
                        continue;
                    $check = User::where("email",$data[1])->count();
                    if($check>0)
                        continue; 

                    $user = new User();
                    $user->name = $data[0].' '.$data[2];
                    $user->email = $data[9];
                    $user->contact = $data[5];
                    $user->password = $data[9];
                    $user->type = '2';
                    if($user->save()) {
                        $detail = new Detail();
                        $detail->user_id = $user->id;
                        $detail->firstname = $data[0];
                        $detail->middlename = $data[1];
                        $detail->lastname = $data[2];
                        $detail->date_of_birth = $data[3];
                        $detail->gender = $data[4];
                        $detail->contact_mobile = $data[5];
                        $detail->contact_home = $data[6];
                        $detail->contact_pager = $data[7];
                        $detail->contact_fax = $data[8];
                        $detail->email_personal = $data[9];
                        $detail->address_permanent = $data[10];
                        $detail->city_permanent = $data[11];
                        $detail->state_permanent = $data[12];
                        $detail->country_permanent = $data[13];
                        $detail->retirement = $data[14];
                        $detail->expertise_in = $data[15];
                        $detail->save();
                    }
                }
            }
            fclose($handle);
            return redirect('upload')->with(
                        'uploded_success','Profiles successfully uploaded.'
                    );
        }
        return redirect('upload');
    }
}