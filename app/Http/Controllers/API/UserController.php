<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\User;



class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize("isAdmin");
        return User::latest()->paginate(1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* validation of input request */
        $this->validate($request,[
            'name'=>'required|string|max:50',
            'email'=>'required|string|email|max:50|unique:users',
            'password'=>'required|string|min:6',
            'avatar'=>'image'
        ]);
        /* return created user */
        return User::create([
            'name'=>$request['name'],
            'email'=>$request['email'],
            'bio'=>$request['bio'],
            'password'=>Hash::make($request['password']),
            'type'=>$request['type']
            ]);
        }

        /**
         * Display the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function show($id)
        {
            //
        }

        /**
         * Display profile of connected user the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function profile()
        {
            return auth('api')->user();
        }
        /**
         * Update profile of connected user the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function updateProfile(Request $request)
        {
            $user= auth('api')->user();
            /* validation of input request */
            $this->validate($request,[
                'name'=>'required|string|max:50',
                'email'=>'required|string|email|max:50|unique:users,email,'.$user->id,
                'password'=>'sometimes|min:6'
            ]);


            $current_avatar = $user->avatar;

            if($request->avatar != $current_avatar){
                $name = time().'.'.explode('/', explode(':',substr($request->avatar,0,strpos($request->avatar,';')))[1])[1];

                if(!File::exists(public_path()."/img/uploads/avatars/".$user->name)){
                   File::makeDirectory(public_path()."/img/uploads/avatars/".$user->name,493, true);
                }
                \Image::make($request->avatar)->resize(60,60)->save(public_path("img/uploads/avatars/".$user->name."/".$name));
                $userAvatar = public_path($current_avatar);
                $request->merge(['avatar'=>"img/uploads/avatars/".$user->name."/".$name]);
                if(\file_exists($userAvatar)){
                    @unlink($userAvatar);
                }
            }
            if(!empty($request->password)){
                $request->merge(['password'=> Hash::make($request['password'])]);
            }

            $user->update($request->all());

            return ['message'=>'updated'];
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, $id)
        {
            //
            //dd($request->all());
            $user= User::findOrFail($id);
            $this->validate($request,[
                'name'=>'required|string|max:50',
                'email'=>'required|string|email|max:50|unique:users,email,'.$user->id,
                'password'=>'sometimes|min:6'
            ]);
            if (isset($request['password'])) {
               $request['password']= Hash::make($request['password']);
            }
            $user->update($request->all());
            return ['message'=>'Updated  the user info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize("isAdmin");
        $user = User::findOrFail($id);
        $user->delete();
        return ['message'=>'User deleted'];
    }

    /**
     * Fonction search
     *
     * Search dans la base de donnée des utilisateurs
     *
     * @param String $query Clé de recherche
     * @return String
     * @throws conditon
     **/
    public function search()
    {
        $users =null;
        if($search = \Request::get('q')){
            $users = User::where(function($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")->orWhere('email','LIKE', "%$search%");
            })->paginate(1);
        }
        return $users;
    }
}
