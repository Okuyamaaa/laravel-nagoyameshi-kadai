<?php

namespace App\Http\Controllers\Admin;

use App\Models\Restaurant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request){
        $keyword = $request->keyword;

        if($keyword === null){
            $restaurants = Restaurant::paginate(15);
            $total = $restaurants->total();
        }else{
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")->paginate(15);
            $total = $restaurants->total();
        }

        return view('admin.restaurants.index', compact('restaurants', 'total', 'keyword'));
    }

    public function show(Restaurant $restaurant){
        return view('admin.restaurants.show', compact('restaurant'));
    }

    public function create(){
        return view('admin.restaurants.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'image' =>  'image|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0'
]);

           
           
    
        
    $restaurant = new Restaurant();
    $restaurant->name = $request->input('name');
    $restaurant->image = $request->file('image')->store('restaurants', 'public');
    $restaurant->description = $request->input('description');
    $restaurant->lowest_price = $request->input('lowest_price');
    $restaurant->highest_price = $request->input('highest_price');
    $restaurant->postal_code = $request->input('postal_code');
    $restaurant->address = $request->input('address');
    $restaurant->opening_time = $request->input('opening_time');
    $restaurant->closing_time = $request->input('closing_time');
    $restaurant->seating_capacity = $request->input('seating_capacity');

    

    $restaurant->save();
    
  
    $category_ids = array_filter($request->input('category_ids'));
    $restaurant->categories()->sync($category_ids);

    return to_route('admin.restaurants.store')->with('flash_message', '店舗を登録しました。');
    }

    public function edit(Restaurant $restaurant){
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
{
       
        $request->validate([
            'name' => 'required',
            'image' =>  'image|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0'
]);
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('restaurants', 'public');
        } else {
            $image =  $restaurant->image;
        }
        


  
        $restaurant->name = $request->input('name');
        $restaurant->image = $image;
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        


        $restaurant->update();

       


        return to_route('admin.restaurants.index')->with('flash_message', '店舗を編集しました。');
        
    }

    public function destroy(Restaurant $restaurant){
        $restaurant->delete();

        return to_route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
}
