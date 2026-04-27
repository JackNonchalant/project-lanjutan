<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;
use Midtrans\Snap;
use Midtrans\Config;

class OrderController extends Controller
{
    /*
    |-----------------------------------------
    | ADD TO CART
    |-----------------------------------------
    */
    public function addToCart(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:produks,id',
            'name'  => 'required',
            'price' => 'required|numeric',
        ]);

        $cart = session()->get('cart', []);
        $id = (int) $request->id;

        if (isset($cart[$id])) {
            $cart[$id]['qty']++;
        } else {
            $cart[$id] = [
                'id'    => $id,
                'name'  => $request->name,
                'price' => $request->price,
                'qty'   => 1,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('customer.cart')
            ->with('success', 'Produk ditambahkan ke keranjang');
    }

    /*
    |-----------------------------------------
    | CART PAGE
    |-----------------------------------------
    */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }

        return view('customer.cart', compact('cart', 'total'));
    }

    /*
    |-----------------------------------------
    | REMOVE ITEM
    |-----------------------------------------
    */
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Produk dihapus dari keranjang');
    }

    /*
    |-----------------------------------------
    | CHECKOUT
    |-----------------------------------------
    */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong');
        }

        $total = 0;

        DB::beginTransaction();

        try {

            // CEK STOK + HITUNG TOTAL
            foreach ($cart as $item) {
                $produk = Produk::find($item['id']);

                if (!$produk) {
                    throw new \Exception('Produk tidak ditemukan');
                }

                if ($produk->stok < $item['qty']) {
                    throw new \Exception('Stok tidak cukup untuk ' . $produk->nama_produk);
                }

                $total += $item['price'] * $item['qty'];
            }

            // CREATE ORDER
            $order = Order::create([
                'user_id'     => Auth::id(),
                'total_price' => $total,
                'status'      => 'pending',
            ]);

            // SAVE ORDER ITEMS
            foreach ($cart as $item) {
                DB::table('order_items')->insert([
                    'order_id'     => $order->id,
                    'produk_id'    => (int) $item['id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'qty'          => $item['qty'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            // MIDTRANS CONFIG
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $order->id,
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'name'  => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            DB::commit();

            session()->forget('cart');

            return view('customer.payment', compact('snapToken', 'order'));

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |-----------------------------------------
    | LIST ORDERS
    |-----------------------------------------
    */
    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('customer.orders', compact('orders'));
    }

    /*
    |-----------------------------------------
    | ORDER DETAIL
    |-----------------------------------------
    */
    public function orderDetail($id)
    {
        $order = Order::with('items')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('customer.order-detail', compact('order'));
    }

    /*
    |-----------------------------------------
    | MIDTRANS CALLBACK
    |-----------------------------------------
    */
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');

        $hashed = hash("sha512",
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($hashed != $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($request->transaction_status == 'settlement') {

            $order->status = 'paid';

            foreach ($order->items as $item) {
                $produk = Produk::find($item->produk_id);

                if ($produk) {
                    $produk->decrement('stok', $item->qty);
                }
            }

        } elseif ($request->transaction_status == 'pending') {
            $order->status = 'pending';
        } else {
            $order->status = 'failed';
        }

        $order->save();

        return response()->json(['message' => 'OK']);
    }
}