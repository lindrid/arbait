<?php

namespace App\Http\Controllers\Api\V1;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

class CompaniesController extends Controller
{
    public function index()
    {
        return Company::all();
    }

    public function indexUser()
    {
        $user = JWTAuth::parseToken()->authenticate()->name;
        $posts = Company::where("user_name", "=", $user)->get();

        return response()->json($posts);
    }

    public function show($id)
    {
        return Company::findOrFail($id);

    }

    /*public function show(Request $request, Company $post)
    {
        $user = JWTAuth::parseToken()->authenticate()->name;
        $posts = Company::where("user_names", "=", $user->name)->get();

        return response()->json($posts);
    }*/

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->update($request->all());

        return $company;
    }

    public function store(Request $request, Company $post)
    {
        //$company = Company::create($request->all());
//        return $company;

        /*try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }*/

        $userId = JWTAuth::parseToken()->authenticate()->name;

        $company = Company::create([
            'user_name' => $userId,
            'name' => $request->get('name'),
            'address' => $request->get('address'),
            'website' => $request->get('website'),
            'email' => $request->get('email'),
        ]);

        return $company;

        // $user = User::find(Auth::user()->id);

        /*$newPost = $request->$user->posts()->create([
            'name' => $request->get('name'),
            'address' => $request->get('address'),
            'website' => $request->get('website'),
            'email' => $request->get('email'),
        ]);*/

//        return response()->json($post->with('user')->find($newPost->id));
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return '';
    }
}