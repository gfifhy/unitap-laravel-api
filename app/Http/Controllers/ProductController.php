<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Utils\FileServiceInterface;
use App\Traits\ExceptionTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    use ExceptionTrait;
    private $productImageFolderName;
    private $fileService;
    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
        $this->productImageFolderName = config('storage.base_path') . 'products';
    }
    public function index() {
        $products = Product::whereNull('deleted_at')->where('user_id', auth()->user()->id)->get();
        foreach($products as $product){
            $product->image = $this->fileService->download($product->image, $product->user_id);
        }
        return $product;
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'product_name' => 'required|string',
            'description' => 'string',
            'image' => 'required|string',
            'stock' => 'required|string',
            'price' => 'required|string',
        ]);
        if(Product::where('product_name', $fields['product_name'])->where('user_id', Auth::user()->id)->first()){
            return $this->throwException('Product Already Exist', 422);
        }
        $filename = md5(Auth::user()->id.Carbon::now()->timestamp);
        $fields['image'] = $this->fileService->upload($this->productImageFolderName, $filename, $fields['image'], Auth::user()->id);


        $product = Product::create([
            'product_name' => $fields['product_name'],
            'user_id' => Auth::user()->id,
            'description' => $fields['description'],
            'image' => $fields['image'],
            'stock' => $fields['stock'],
            'price' => $fields['price'],
        ]);

        return  response($product, 201);
    }

    public function show($id)
    {
        return Product::where('user_id',Auth::user()->id)->where('id', $id)->whereNull('deleted_at')->first();
    }
    public function update(Request $request, $id)
    {

        $fields = $request->validate([
            'product_name' => 'required|string',
            'stock' => 'required|string',
            'price' => 'required|string',
        ]);
        $product = Product::where('id', $id);
        $product->product_name = $fields['product_name'];
        $product->stock = $fields['stock'];
        $product->price = $fields['price'];
        $product->save();

        return response($product, 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::where('id', $id)->first();
        $product->deleted_at = Carbon::now();
        $product->save();
        return $product;
    }


}
