<?php
namespace App\Http\Controllers;
use App\Models\Histogrammes;
use App\Models\Histogramme;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Picture;
use Illuminate\Support\Facades\Redirect;
use App\Models\Moments;
use App\Models\Pallette;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Psr7\Response;
class ImageController extends Controller
{
public function showUploadForm(){
        return view('images');
}

public function showImages(){
        $images = Image::all();
        return view('images', ['images' => $images]);
}
// public function uploadImage(Request $request){
//         $request->validate([
//             'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
//         ]);
//         if ($request->hasFile('image')) {
//             $image = $request->file('image');
//             $imageName = time() . '_' . $image->getClientOriginalName();
//             $image->move(public_path('images'), $imageName); 
//             $imagePath = public_path('images') . '/' . $imageName; 
//             $imageData = file_get_contents($imagePath); 
//             Image::create([
//                 'file_name' => $imageName,
//                 'binary_data' => $imageData, 
//             ]);

//             return redirect()->route('images')->with('success');
//         }
//         return redirect()->route('images')->with('error', 'Upload failed.');
// }
public function gotosearch()
{
     // Assurez-vous que 'file_name' contient votre liste de données RGB
    
    return view('search_types');
}
public function uploadImage(Request $request)
{
    $request->validate([
        'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $uploadedImages = [];

    foreach ($request->file('image') as $image) {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('images'), $imageName);
        $imagePath = public_path('images') . '/' . $imageName;
        $imageData = file_get_contents($imagePath);

        Image::create([
            'file_name' => $imageName,
            'binary_data' => $imageData,
        ]);

        $uploadedImages[] = $imageName;
    }

    return redirect()->route('images')->with('success')->with('uploadedImages', $uploadedImages);
}


public function editImage($id){
            $image = Image::find($id);
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

// public function showHistogram()
// {
//     $histogramme = Histogramme::latest('id')->first();
//     return view('histogram-show', compact('histogramme'));
// }

// public function showHistogram()
// {
//     $histogramme = Histogramme::latest('id')->first();
//     $histogrammeData = json_decode($histogramme->file_name, true); // Assurez-vous que 'file_name' contient votre liste de données RGB
    
//     return view('histogram-show', compact('histogrammeData'));
// }

public function showHistogram()
{
  
    $histogrammes  = Histogrammes::latest('id')->first();
    // $histogramme_r = json_decode($histogrammes->red,  true);  
    // $histogramme_g = json_decode( $histogrammes->green,true); 
    // $histogramme_b = json_decode( $histogrammes->blue,true);
    // dd($histogramme_g);
    // dd($histogramme_r);  
    // dd($histogramme_b);
    return view('histogram-show', compact('histogrammes'));

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
    $image = Image::find($id);
    if ($image) {
        $imagePath = public_path('images') . '/' . $image->file_name;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $image->delete();
    }
    return redirect()->route('images')->with('success');
}

public function search ($id,Request $request){
 
            $image = Image::find($id);
            $numOfResults = request('numberOfResults');
            $response = Http::attach('image', file_get_contents(public_path('images') . '/' . $image->file_name), $image->file_name)->post('http://localhost:5000/Search/Simple/', [
                'numOfResults' => $numOfResults,
            ]);
            $arraydata = $response->body();
            $array = json_decode($arraydata, true);
            $imaget = [];
            foreach ($array['images'] as $value) {
            $image = Picture::where('id', $value)->first();
            $filename = $image->file_name; 
            $filename = '/' . $filename;
            $Moment_path = public_path('Moments'); 
            file_put_contents($Moment_path .$filename, $image->image_bytes);    
            $imaget[]=$image->file_name;
    }

        return view('search',compact('imaget'));
}

public function search_RF ($id,Request $request){
 
    $image = Image::find($id);
    $numOfResults = request('numberOfResults');
    $response = Http::attach('image', file_get_contents(public_path('images') . '/' . $image->file_name), $image->file_name)->post('http://localhost:5000/relevance-feedback/', [
        'numOfResults' => $numOfResults,
    ]);
    $arraydata = $response->body();
    $array = json_decode($arraydata, true);
    $imaget = [];
    foreach ($array['images'] as $value) {
    $image = Picture::where('id', $value)->first();
    $filename = $image->file_name; 
    $filename = '/' . $filename;
    $Moment_path = public_path('Moments'); 
    file_put_contents($Moment_path .$filename, $image->image_bytes);    
    $imaget[]=$image->file_name;
}

return view('search_RF',compact('imaget','id'));
}

// public function search_RF ($id){
 
//     $image = Image::find($id);
//     $response = Http::attach('image', file_get_contents(public_path('images') . '/' . $image->file_name), $image->file_name)->post('http://localhost:5000/relevance-feedback');
//     $arraydata = $response->body();
//     $array = json_decode($arraydata, true);
//     $imaget = [];
//     foreach ($array['images'] as $value) {
//     $image = Picture::where('id', $value)->first();
//     $filename = $image->file_name; 
//     $filename = '/' . $filename;
//     $Moment_path = public_path('Moments'); 
//     file_put_contents($Moment_path .$filename, $image->image_bytes);    
//     $imaget[]=$image->file_name;
// }

// return view('search_RF',compact('imaget'));
// }

// public function search_RF($id)
// {
//     $image = Image::find($id);

//     $response = Http::timeout(300)->attach('image', file_get_contents(public_path('images') . '/' . $image->file_name), $image->file_name)->post('http://localhost:5000/relevance-feedback');

//     // $arraydata = $response->body();
//     // $array = json_decode($arraydata, true);
//     // $imaget = [];
//     // echo $arraydata;
//     // foreach ($array['image_path'] as $value) {
//     //     $image = Picture::where('id', $value)->first();
//     //     $filename = $image->file_name; 
//     //     $filename = '/' . $filename;
//     //     $Moment_path = public_path('Moments'); 
//     //     file_put_contents($Moment_path . $filename, $image->image_bytes);    
//     //     $imaget[] = $image->file_name;
//     // }
//     header("Location: http://127.0.0.1:5000/relevance-feedback");

//     // return Redirect::to($redirectTo);
//     // return view('search_RF', compact('imaget'));
// }

public function enregistrerImage(Request $request)
{
    $imageBase64 = $request->input('image');
    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageBase64));

    $nomFichier = 'image_cropee_' . time() . '.jpg';
    $chemin = public_path('images/' . $nomFichier);

    file_put_contents($chemin, $imageData);

    // Enregistrez le chemin du fichier dans la base de données
    $image = new Image();
    $image->file_name = $nomFichier;
    $image->save();

    return redirect()->back()->with('success', 'Image enregistrée avec succès');
}




public function search_by_RF ($id,Request $request){
    $relevance = $request->input('relevant', []);
    $id_images = $request->input('image_ids', []);
    
    $image = Image::find($id);
    $response = Http::post('http://localhost:5000/relevance-feedback-results/', [
        'relevant' => $relevance,
        'image_ids' => $id_images,
        'image' => $id,
    ]);
    $arraydata = $response->body();
    $array = json_decode($arraydata, true);
    $imaget = [];
    foreach ($array['images'] as $value) {
    $image = Picture::where('id', $value)->first();
    $filename = $image->file_name; 
    $filename = '/' . $filename;
    $Moment_path = public_path('Moments'); 
    file_put_contents($Moment_path .$filename, $image->image_bytes);    
    $imaget[]=$image->file_name;
}

return view('search_RF',compact('imaget','id'));

}
}
