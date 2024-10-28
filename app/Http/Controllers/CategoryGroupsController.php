<?php

namespace App\Http\Controllers;


use App\Models\CategoryGroups;

class CategoryGroupsController extends Controller
{
    
   
   

    public function index()
    {
        return CategoryGroups::all();
    }


}