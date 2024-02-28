<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Check_User_Controller extends Controller
{
      public function check_id(Response $request){

                  $user_id = $request->id;

                  return Response()->json($user_id);
                 




            }
}
