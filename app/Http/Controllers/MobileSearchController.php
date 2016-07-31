<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use DB;
use App\User;
use App\Detail;
use App\WorkExperience;

class MobileSearchController extends Controller
{

    /**
     * retrieve all the related private categories
     *
     * @return Response
     **/
    public function getPrivateCategory() {
        $term = Input::get('term');
        $results = array();
        $queries = WorkExperience::where('category', 'LIKE', '%'.$term.'%')->get();

        foreach($queries as $query) {
            if($query->sector == "Private Sector")
                array_push($results, ['id' => $query->id, 'value' => $query->category]);
        }
        $data = array();
        $data['list'] = $results;
        return response()->json($data);
    }

    /**
     * retrieve all the related ministries
     *
     * @return Response
     **/
    public function getMinistry() {
    	$term = Input::get('term');
    	$results = array();
    	$queries = WorkExperience::where('ministry', 'LIKE', '%'.$term.'%')->get();

    	foreach($queries as $query) {
    		array_push($results, ['id' => $query->id, 'value' => $query->ministry]);
    	}
        $data = array();
        $data['list'] = $results;
        return response()->json($data);
    }

    /**
     * retrieve all the related departments
     *
     * @return Response
     **/
    public function getDepartment() {
        $term = Input::get('term');
        $results = array();
        $queries = WorkExperience::where('department', 'LIKE', '%'.$term.'%')->get();

        foreach($queries as $query) {
            array_push($results, ['id' => $query->id, 'value' => $query->department]);
        }
    $data = array();$data['list'] = $results;return response()->json($data);
    }

    /**
     * retrieve all the related companies
     *
     * @return Response
     **/
    public function getCompany() {
    	$term = Input::get('term');
    	$results = array();
    	$queries = WorkExperience::where('company', 'LIKE', '%'.$term.'%')->get();

    	foreach($queries as $query) {
    		array_push($results, ['id' => $query->id, 'value' => $query->company]);
    	}
	$data = array();$data['list'] = $results;return response()->json($data);
    }

    /**
     * retrieve all the related locations
     *
     * @return Response
     **/
    public function getLocation() {
        $term = Input::get('term');
        $results = array();
        $queries = WorkExperience::where('location', 'LIKE', '%'.$term.'%')->get();

        foreach($queries as $query) {
            array_push($results, ['id' => $query->id, 'value' => $query->location]);
        }
    $data = array();$data['list'] = $results;return response()->json($data);
    }

    /**
     * retrieve all the related roles
     *
     * @return Response
     **/
    public function getRole() {
        $term = Input::get('term');
        $results = array();
        $queries = WorkExperience::where('role', 'LIKE', '%'.$term.'%')->get();

        foreach($queries as $query) {
            array_push($results, ['id' => $query->id, 'value' => $query->role]);
        }
    $data = array();$data['list'] = $results;return response()->json($data);
    }

    /**
     * retrieve all the related positions
     *
     * @return Response
     **/
    public function getPosition() {
        $term = Input::get('term');
        $results = array();
        $queries = WorkExperience::where('position', 'LIKE', '%'.$term.'%')->get();

        foreach($queries as $query) {
            array_push($results, ['id' => $query->id, 'value' => $query->position]);
        }
    $data = array();$data['list'] = $results;return response()->json($data);
    }

    /**
     * retrieve all the related firstnames
     *
     * @return Response
     **/
    public function getFirstnameViewer() {
        $term = Input::get('term');
        $results = array(); $data = array();
        $queries = Detail::where('firstname', 'LIKE', '%'.$term.'%')->get();

        foreach($queries as $query) {
            if($query->user->type == '1')
                array_push($results, ['id' => $query->id, 'value' => $query->firstname]);
        }
        $data['list'] = $results;
        return response()->json($data);
    }
}
