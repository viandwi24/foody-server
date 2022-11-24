<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'filter.status' => 'in:pending,processing,finished,declined'
        ]);

        $transactions = Transaction::with('menus');

        if ($request->has('filter')) {
            $filters = $request->filter;
            foreach ($filters as $key => $value) {
                $transactions = $transactions->where($key, $value);
            }
        }

        $transactions = $transactions->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Transaction',
            'data' => $transactions,
            'request' => $request->all(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'in:pending,processing,finished,declined'
        ]);

        $request->validate([
            'user_name' => 'required',
            // 'total' => 'required',
            'menus.*.id' => 'required|exists:menus,id',
            'menus.*.qty' => 'required|integer|min:1',
        ]);

        // calculate total
        $total = 0;
        $menus_models = [];
        foreach ($request->menus as $menu) {
            $menu_model = Menu::find($menu['id']);
            $menu_total = $menu_model->price * $menu['qty'];
            $menu_qty = $menu['qty'];

            $total += $menu_total;
            $menus_models[] = [
                'model' => $menu_model,
                'qty' => $menu_qty,
                'total' => $menu_total,
            ];
        }

        $transaction = null;
        DB::transaction(function () use ($request, $total, $menus_models, &$transaction) {
            $transaction = Transaction::create([
                'user_name' => $request->user_name,
                'total' => $total,
                'status' => 'pending'
            ]);

            foreach ($menus_models as $menu_model) {
                $transaction->menus()->attach($menu_model['model'], [
                    'quantity' => $menu_model['qty'],
                    'total' => $menu_model['total'],
                    'price' => $menu_model['model']->price,
                ]);
            }

            // reload transaction with menus
            $transaction->load('menus');
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaction Created',
            'data' => $transaction
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::with('menus')->findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Transaction',
            'data' => $transaction
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::with('menus')->findOrFail($id);

        $request->validate([
            'status' => 'in:pending,processing,finished,declined'
        ]);

        $transaction->update($request->only('status'));

        return response()->json([
            'success' => true,
            'message' => 'Transaction Updated',
            'data' => $transaction
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction Deleted',
            'data' => $transaction
        ], 200);
    }
}
