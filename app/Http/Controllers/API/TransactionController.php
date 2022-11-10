<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 20);
        $status = $request->input('status');

        if ($id) {
            $transaction = Transaction::with(['items.product'])->find($id);

            if ($transaction)
                return ResponseFormatter::success(
                    $transaction,
                    'Data transaksi berhasil diambil'
                );
            else
                return ResponseFormatter::error(
                    null,
                    'Data transaksi tidak ada',
                    404
                );
        }

        $transaction = Transaction::with(['items.product'])->where('users_id', Auth::user()->id)->latest();

        if ($status)
            $transaction->where('status', $status);

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data list transaksi berhasil diambil'
        );
    }
    public function alltransactions(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 20);
        $status = $request->input('status');

        if ($id) {
            $transaction = Transaction::with(['items.product'])->find($id);

            if ($transaction)
                return ResponseFormatter::success(
                    $transaction,
                    'Data transaksi berhasil diambil'
                );
            else
                return ResponseFormatter::error(
                    null,
                    'Data transaksi tidak ada',
                    404
                );
        }

        $transaction = Transaction::with(['items.product.galleries', 'user', 'user_location'])->latest();

        if ($status)
            $transaction->where('status', $status);

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data list transaksi berhasil diambil'
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'exists:products,id',
            'total_price' => 'required',
            'shipping_price' => 'required',
            'user_location_id' => 'required',
            'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED',
        ]);

        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'address' => $request->address,
            'total_price' => $request->total_price,
            'user_location_id' => $request->user_location_id,
            'shipping_price' => $request->shipping_price,
            'status' => $request->status
        ]);

        foreach ($request->items as $product) {
            TransactionItem::create([
                'users_id' => Auth::user()->id,
                'products_id' => $product['id'],
                'transactions_id' => $transaction->id,
                'quantity' => $product['quantity'],

            ]);
        }
        $SERVER_API_KEY = 'AAAALCUa4N0:APA91bG0SJP6S-jkV2u0LwnySlcFEqnvxU1cw-HFVtdONTUrL3BMwcO464apycQPZ_SvwJMFRa4MLCtmFxGVIqonyOcuy2_Z6S_W7SawomoPZY1PWOf6kJDJoxigur7JNMW8qp3eZy8w';

        $transaction->load('items.product');

        $data = [
            "to" => "/topics/Kurir",
            "notification" => [
                "title" => 'Pesanan masuk Dari' . ' ' . Auth::user()->name,
                "body" => 'Memesan Sebuah' . ' ' . $transaction->items[0]->product->name,
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
        return ResponseFormatter::success($transaction->load('items.product'), 'Transaksi berhasil');
    }

    //send notification to fcm topics ?


    public function addrating(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transactions,id',
            'rating' => 'required',
            'note' => 'required',
        ]);

        $transaction = Transaction::find($request->id);
        $data = $request->all();
        $transaction->update($data);

        return ResponseFormatter::success($transaction->load('items.product'), 'Rating berhasil ditambahkan ');
    }

    public function updatestatus(Request $request,)
    {
        $transaction = Transaction::findOrFail($request->id);
        $data = $request->all();
        $transaction->update($data);

        return ResponseFormatter::success($transaction, 'Transaksi berhasil diupdate');
    }
}
