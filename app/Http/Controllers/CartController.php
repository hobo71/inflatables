<?php namespace App\Http\Controllers;

use App\Unit;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Previous;
use Illuminate\Support\Facades\Session;

/**
 * @package App\Http\Controllers
 */
class CartController extends Controller
{

    /**
     * @return mixed
     */
    public function index()
    {
        return view('cart');
    }


    /**
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {

        $unit = Unit::find($request->get('unit'));

        $media = ($unit->product->media->count() > 0) ? $unit->product->media->first()->getUrl('thumb') : null;

        Cart::instance(session('cartId'))->add($unit->id, $unit->description, 1, $unit->price, [
            'product_name' => $unit->product->name,
            'image'        => $media,
            'productSlug'  => $unit->product->slug,
            'categorySlug' => $unit->product->categories->first()->slug,
            'width'        => $unit->width,
            'length'       => $unit->length,
            'height'       => $unit->height,
            'weight'       => $unit->weight,
            'model'        => $unit->model,
        ]);

        Session::flash('backUrl', Previous::server('HTTP_REFERER'));

        return redirect()->route('cart.index');
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @param                          $unitId
     *
     * @return mixed
     */
    public function destroy(Request $request, $unitId)
    {
        Cart::instance(session('cartId'))->remove($unitId);

        flash('Item has been removed from your cart.');

        return redirect()->route('cart.index');

    }
}