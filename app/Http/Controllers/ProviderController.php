<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductList;
use App\Models\Gamelist;
use App\Models\Category;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $categorys = Category::where('active',1)->get();
        $products = ProductList::all();
        return view('provider.index', compact('products','categorys'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|string|unique:product_list',
            'product_name' => 'required|string',
            'category' => 'required|integer',
            'active' => 'required|boolean',
            'order_top' => 'required|integer',
            'img' => 'required|image',
            'img_mini' => 'required|image',
        ]);

        // สร้างชื่อไฟล์ใหม่
        $imgName = time() . '_main.' . $request->img->getClientOriginalExtension();
        $imgMiniName = time() . '_mini.' . $request->img_mini->getClientOriginalExtension();

        // Path เต็ม
        $imgFullPath = public_path('products/' . $imgName);
        $imgMiniFullPath = public_path('products/' . $imgMiniName);

        try {
            // ย้ายไฟล์ไปยัง public/products
            $request->img->move(public_path('products'), $imgName);
            $request->img_mini->move(public_path('products'), $imgMiniName);

            // บันทึกลงฐานข้อมูล
            ProductList::create([
                'product_id' => $validated['product_id'],
                'product_name' => $validated['product_name'],
                'category' => $validated['category'],
                'active' => $validated['active'],
                'order_top' => $validated['order_top'],
                'img' => '/products/' . $imgName,
                'img_mini' => '/products/' . $imgMiniName,
            ]);

            return redirect()->back()->with('success', 'เพิ่มข้อมูลเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            // ลบไฟล์ที่ถูกอัปโหลดไว้แล้ว
            if (file_exists($imgFullPath)) {
                unlink($imgFullPath);
            }

            if (file_exists($imgMiniFullPath)) {
                unlink($imgMiniFullPath);
            }

            // ส่ง error message กลับ
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'product_id' => [
            'required',
            'string',
            Rule::unique('product_list')->ignore($id), // <-- อันนี้
        ],
        'product_name' => 'required|string',
        'category' => 'required|integer',
        'active' => 'required|boolean',
        'order_top' => 'required|integer',
        'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'img_mini' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $product = ProductList::findOrFail($id);

    // ลบรูปเก่า ถ้ามีการอัปโหลดรูปใหม่
    if ($request->hasFile('img')) {
        $oldImgPath = public_path($product->img);
        if (file_exists($oldImgPath)) {
            unlink($oldImgPath);
        }

        $imgName = time() . '_main.' . $request->img->getClientOriginalExtension();
        $request->img->move(public_path('products'), $imgName);
        $validated['img'] = '/products/' . $imgName;
    }

    if ($request->hasFile('img_mini')) {
        $oldMiniPath = public_path($product->img_mini);
        if (file_exists($oldMiniPath)) {
            unlink($oldMiniPath);
        }

        $miniName = time() . '_mini.' . $request->img_mini->getClientOriginalExtension();
        $request->img_mini->move(public_path('products'), $miniName);
        $validated['img_mini'] = '/products/' . $miniName;
    }

    $product->update($validated);

    return redirect()->back()->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
}




    public function updateprovider(Request $request)
    {
        //
        $product = ProductList::find($request->id);
        $product->active = $request->checked;
        $product->save();
        return true;
    }

    public function order_top(Request $request){
        $product = ProductList::find($request->id);
        $product->order_top = $request->order_top;
        $product->save();
        return true;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function Gamelist($pid)
    {

        // $data = $this->APIListGames($pid);
        // if ($data){
        //       foreach ($data as $game) {
        //         $check_game = GameList::where('gameName', $game->gameName)->where('productId',$pid)->first();
        //         if ($check_game == null) {
        //             GameList::create([
        //                 "gameId" => $game->gameId,
        //                 "productId" => $game->productId,
        //                 "launchCode" => $game->launchCode,
        //                 "gameCode"=> $game->gameCode,
        //                 "categoryId"=> $game->categoryId,
        //                 "bannerUrl"=> $game->bannerUrl,
        //                 "gameName"=> $game->gameName,
        //                 "describe"=> $game->describe,
        //                 "popular"=> $game->popular,
        //                 "new"=> $game->new,
        //                 "vote"=> $game->vote,
        //             ]);
        //         }
        //     }
        // }
        $gamelist = GameList::where('productId',$pid)->get();

        return view('provider.gamelist', compact('gamelist'));
    }

    public function GetAllGame()
    {
        set_time_limit(30000);
        $products = ProductList::get();
        foreach($products as $product){
            $data = $this->APIListGames($product->product_id);
            if ($data){
                foreach ($data as $game) {
                    $check_game = GameList::where('gameName', $game->gameName)->where('productId',$product->product_id)->first();
                    if ($check_game == null) {
                        $bannerUrl = '';
                        if($game->bannerUrl === ''){
                            $bannerUrl = '/image/logo3.png';
                        }else{
                            $bannerUrl = $game->bannerUrl;
                        }
                        GameList::create([
                            "gameId" => $game->gameId,
                            "productId" => $game->productId,
                            "launchCode" => $game->launchCode,
                            "gameCode"=> $game->gameCode,
                            "categoryId"=> $game->categoryId,
                            "bannerUrl"=> $bannerUrl,
                            "gameName"=> $game->gameName,
                            "describe"=> $game->describe,
                            "popular"=> $game->popular,
                            "new"=> $game->new,
                            "vote"=> $game->vote,
                        ]);
                    }
                }
            }
        }
        $gamelist = GameList::get();

        return $gamelist;
    }

    public function updategameimage(Request $request)
    {
        $game_id=$request->game_id;
        $provider=$request->provider;
        $validated = $request->validate([
            'imgupload' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);
        $fileName = $provider.$game_id.'.'.$request->imgupload->extension();

        $request->imgupload->move(public_path('images/games'), $fileName);

        $game = Gamelist::find($game_id);

        $game->bannerUrl = "/images/games/".$fileName;
        $game->save();
        //$pathtoimage = $game->image;
        $pathtoimage = $game->bannerUrl;

        return response()->json($pathtoimage);

    }
    public function updateproviderimage(Request $request)
    {
        $provider_id=$request->provider_id;
        $provider=$request->provider;
        $validated = $request->validate([
            'imgupload' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);


        $game = ProductList::find($provider_id);
        if($request->size == "L"){
            $fileName = $provider.$provider_id.'.'.$request->imgupload->extension();
            $request->imgupload->move(public_path('images/providers'), $fileName);
            $game->img = "/images/providers/".$fileName;
            $pathtoimage = $game->img;
        }else{
            $fileName = $provider.$provider_id.'.'.$request->imgupload->extension();
            $request->imgupload->move(public_path('images/mini'), $fileName);
            $game->img_mini = "/images/mini/".$fileName;
            $pathtoimage = $game->img_mini;
        }

        $game->save();
        //$pathtoimage = $game->image;
        

        return response()->json($pathtoimage);

    }

    public function updategamestatus(Request $request)
    {
        //
        $product = Gamelist::find($request->id);
        $product->active = $request->checked;
        $product->save();
        return true;
    }
    public function APIListGames($productId)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', env('APP_GPLAY_URL') . '/api/v1/games?pageNo=1&limit=1000&productId='.$productId, [
            'headers' => [
                'X-Authorization-Token' => $this->encrypt(),
                'Content-Type' => 'application/json'
            ]
        ]);
        $data = json_decode($response->getBody())->data;

        return $data;
    }
    public function auth_basic()
    {
        $auth = base64_encode(env('APP_ASK_AGENT') . ":" . env('APP_ASK_API_SECRET'));
        return $auth;
    }

    public function encrypt() {
        $text = env('APP_GPLAY_OPERATOR_TOKEN').':'.env('APP_GPLAY_SEAMLESS_KEY');
        $key = env('APP_GPLAY_KEY');

        if (strlen($key) !== 32) {
            throw ValidationException::withMessages(["Key must be 32 bytes long."]);
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            throw ValidationException::withMessages(["Encryption failed."]);
        }

        $data = $iv . $encrypted;
        return base64_encode($data);
    }
}
