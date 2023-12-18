<?php

namespace App\Http\Controllers;
use App\Models\Animal;
use Illuminate\Http\Request;

use App\Models\Histogramme;
use App\Models\Image;
use App\Models\Moments;
use App\Models\Pallette;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Psr7\Response;
class AnimalController extends Controller
{
    public function index()
    {
        $animals = Animal::all();
        return view('animal', compact('animals'));
    }
    public function uploadImage(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName); 
            $imagePath = public_path('images') . '/' . $imageName; 
            $imageData = file_get_contents($imagePath); 
            Animal::create([
                'file_name' => $imageName,
                'binary_data' => $imageData, 
            ]);

            return redirect()->route('animal')->with('success');
        }
        return redirect()->route('animal')->with('error', 'Upload failed.');
}

public function editImage($id){
    $image = Animal::find($id);
    $response = Http::attach('image', file_get_contents(public_path('images') . '/' . $image->file_name), $image->file_name)->post('http://localhost:5000/image/upload/');
    $response1 = Http::attach('image', file_get_contents(public_path('images') . '/' . $image->file_name), $image->file_name)->post('http://localhost:5000/image/upload2');
    $response2 = Http::attach('image', file_get_contents(public_path('images') . '/' . $image->file_name), $image->file_name)->post('http://localhost:5000/image/upload1');
    $filename = $image->file_name; 
    $filename= '/Histogramme_' . $filename . '.png';
    $response3 = Http::get('http://localhost:5000/image/upload/histogramme/' . $filename);                
    $Histogramme_path = public_path('histograms');
    file_put_contents($Histogramme_path .$filename, $response3->body());
    $filename = $image->file_name; 
    $filename= '/pallette_' . $filename . '.png';
    $response4 = Http::get('http://localhost:5000/image/upload2/pallette/' . $filename);
    $pallette_path = public_path('Pallettes'); 
    file_put_contents($pallette_path .$filename, $response4->body());    
    $filename = $image->file_name; 
    $filename= '/Moment_' . $filename . '.png';
    $response5 = Http::get('http://localhost:5000/image/upload1/Moment/' . $filename);
    $Moment_path = public_path('Moments'); 
    file_put_contents($Moment_path .$filename, $response5->body());    
    return view('edit-image', compact('image'));
}

public function getHistogram($filename){             
$image = Image::find($filename);
return view('edit-image1', ['image' => $image]);
}    

public function getPallette($filename){    
$image = Image::find($filename);
return view('edit-image2', ['image' => $image]);
}

public function getMoments($filename){    
$image = Image::find($filename);
return view('edit-image3', ['image' => $image]);
}

public function showHistogram()
{
$histogramme = Histogramme::latest('id')->first();
return view('histogram-show', compact('histogramme'));
}

public function showPallette()
{
$pallette = Pallette::latest('id')->first();
return view('pallette-show', compact('pallette'));
}

public function showMoment()
{

$moment = Moments::latest('id')->first();
return view('moment-show', compact('moment'));
}

public function deleteImage($id){
$image = Animal::find($id);
if ($image) {
$imagePath = public_path('images') . '/' . $image->file_name;
if (file_exists($imagePath)) {
    unlink($imagePath);
}
$image->delete();
}
return redirect()->route('animal')->with('success');
}

}
