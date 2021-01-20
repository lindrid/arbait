<?php

namespace App\Http\Controllers\Api\V1;

use App\Application;
use App\Http\Controllers\Controller;
use JWTAuth;

class ApplicationsController extends Controller
{
    public function index()
    {
        return Application::all();
    }

    public function indexUser()
    {
        $user = JWTAuth::parseToken()->authenticate()->id;
        $posts = Application::where("user_id", "=", $user)->get();

        return response()->json($posts);
    }

    public function show($id)
    {
        return Application::findOrFail($id);

    }

    public function update(Application $request, $id)
    {
        $company = Application::findOrFail($id);
        $company->update($request->all());

        return $company;
    }

    public function store(Application $request, Application $post)
    {
        $userId = JWTAuth::parseToken()->authenticate()->name;

        $application = Application::create([
            'user_name' => $userId,
            'name' => $request->get('name'),
            'address' => $request->get('address'),
            'misc' => $request->get('website'),
        ]);

        return $application;

    }

    public function destroy($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();
        return '';
    }
}