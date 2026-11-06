<?php 

namespace App\Services;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class WishlistService
{
    protected $wishlistKey = 'wishlist';

    /**
     * Get current wishlist (guest or logged-in)
     */
    protected function getWishlistSession()
    {
        $wishlist = Session::get($this->wishlistKey, []);
        return collect($wishlist);
    }

    /**
     * Get all wishlist items
     */
    public function all()
    {
        if (Auth::check()) {
            return Wishlist::with('product')
                ->where('user_id', Auth::id())
                ->get()->toArray();
        }

        // Guest wishlist as collection
        return $this->getWishlistSession();
    }

    /**
     * Add product to wishlist
     */
    public function add($productId)
    {
        if (Auth::check()) {
            Wishlist::firstOrCreate([
                'user_id' => Auth::id(),
                'product_id' => $productId,
            ]);
        } else {
            $wishlist = $this->getWishlistSession();
            if (!$wishlist->has($productId)) {
                $product = Product::findOrFail($productId);
                $wishlist->put($productId, [
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'image'      => $product->image,
                    'price'      => $product->selling_price,
                ]);
                Session::put($this->wishlistKey, $wishlist->toArray());
            }
        }
    }

    /**
     * Remove product from wishlist
     */
    public function remove($productId)
    {
        if (Auth::check()) {
            Wishlist::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->delete();
        } else {
            $wishlist = $this->getWishlistSession();
            if ($wishlist->has($productId)) {
                $wishlist->forget($productId);
                Session::put($this->wishlistKey, $wishlist->toArray());
            }
        }
    }

    /**
     * Count wishlist items
     */
    public function count()
    {
        if (Auth::check()) {
            return Wishlist::where('user_id', Auth::id())->count();
        }

        return $this->getWishlistSession()->count();
    }

    /**
     * Merge guest wishlist into user wishlist on login
     */
    public function mergeGuestWishlistToUser($userId)
    {
        $guestWishlist = $this->getWishlistSession();
        if ($guestWishlist->isEmpty()) return;

        foreach ($guestWishlist as $item) {
            Wishlist::firstOrCreate([
                'user_id'    => $userId,
                'product_id' => $item['product_id'],
            ]);
        }

        Session::forget($this->wishlistKey);
    }

    /**
     * Check if product exists in wishlist
     */
    public function has($productId)
    {
        if (Auth::check()) {
            return Wishlist::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->exists();
        }

        return $this->getWishlistSession()->has($productId);
    }

    /**
     * Clear wishlist
     */
    public function clear()
    {
        if (Auth::check()) {
            Wishlist::where('user_id', Auth::id())->delete();
        }

        Session::forget($this->wishlistKey);
    }
}
