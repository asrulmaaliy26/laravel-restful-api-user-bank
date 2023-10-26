<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\BalanceAddSubRequest;
use App\Http\Requests\BalanceCreateRequest;
use App\Http\Requests\BalanceUpdateRequest;
use App\Http\Resources\BalanceResource;
use App\Models\Balance;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BalanceController extends \App\Http\Controllers\API\Controller
{
    public function create(BalanceCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth()->user();

        $balance = new Balance($data);
        $balance->user_id = $user->id;
        $balance->save();

        return (new BalanceResource($balance))->response()->setStatusCode(201);
    }
    public function get(int $id): BalanceResource
    {
        $user = Auth::user();
        $balance = Balance::where('id', $id)->where('user_id', $user->id)->first();
        if (!$balance) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new BalanceResource($balance);
    }

    public function update(BalanceUpdateRequest $request, $id): BalanceResource
    {
        $user = auth()->user();
        $balance = Balance::where('id', $id)->where('user_id', $user->id)->first();

        if (!$balance) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $balance->update($data);

        $balance->save();

        return new BalanceResource($balance);
    }

    public function add(BalanceAddSubRequest $request, $id): BalanceResource
    {
        $user = Auth::user();
        $balance = Balance::where('id', $id)->where('user_id', $user->id)->first();

        if (!$balance) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['Saldo tidak ditemukan'],
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();

        $originalAmount = $balance->getOriginal('amount');
        $newAmount = $originalAmount + $data['amount'];

        $balance->update(['amount' => $newAmount]);

        $change = $data['amount'];
        $balance->history .= '+' . $change . ',';

        $balance->save();

        return new BalanceResource($balance);
    }

    public function subtract(BalanceAddSubRequest $request, $id): BalanceResource
    {
        $user = Auth::user();
        $balance = Balance::where('id', $id)->where('user_id', $user->id)->first();

        if (!$balance) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['Saldo tidak ditemukan'],
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();

        $originalAmount = $balance->getOriginal('amount');

        if ($originalAmount < $data['amount']) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['Saldo tidak mencukupi'],
                ]
            ])->setStatusCode(400));
        }

        $newAmount = $originalAmount - $data['amount'];

        $balance->update(['amount' => $newAmount]);

        $change = -$data['amount'];
        $balance->history .= $change . ',';

        $balance->save();

        return new BalanceResource($balance);
    }


    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();

        $balance = Balance::where('id', $id)->where('user_id', $user->id)->first();
        if (!$balance) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $balance->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
