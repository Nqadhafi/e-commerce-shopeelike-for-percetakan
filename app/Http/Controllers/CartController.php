<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Cart, CartItem, Product};
use App\Services\CartPricingService;

class CartController extends Controller
{
    public function __construct(private CartPricingService $pricing) {}

    protected function getOrCreateCart(Request $r): Cart
    {
        $sessionId = $r->session()->getId();
        $cart = Cart::firstOrCreate(
            ['session_id' => $sessionId, 'status' => 'open'],
            ['user_id' => auth()->id(), 'currency' => 'IDR']
        );
        if (auth()->check() && !$cart->user_id) { $cart->user_id = auth()->id(); $cart->save(); }
        return $cart->load('items');
    }

    public function show(Request $r)
    {
        $cart = $this->getOrCreateCart($r);
        $totals = $cart->totals();
        return response()->json([
            'ok'=>true,
            'cart'=>[
                'id'=>$cart->id,'currency'=>$cart->currency,'status'=>$cart->status,
                'items'=>$cart->items->map(fn(CartItem $i)=>[
                    'id'=>$i->id,'product_id'=>$i->product_id,'qty'=>$i->qty,
                    'unit_price'=>$i->unit_price,'addons_total'=>$i->addons_total,
                    'line_total'=>$i->line_total,'v1_option_id'=>$i->v1_option_id,
                    'v2_option_id'=>$i->v2_option_id,'spec_snapshot'=>$i->spec_snapshot,
                    'customer_note'=>$i->customer_note,
                ])->values(),
                'totals'=>$totals,
            ]
        ]);
    }

    public function add(Request $r)
    {
        $data = $r->validate([
            'product_slug'  => ['required_without:product_id','string','max:150'],
            'product_id'    => ['nullable','integer','exists:products,id'],
            'qty'           => ['required','integer','min:1'],
            'v1_option_id'  => ['nullable','integer','exists:product_variant_options,id'],
            'v2_option_id'  => ['nullable','integer','exists:product_variant_options,id'],
            'addon_ids'     => ['sometimes','array'],
            'addon_ids.*'   => ['integer','exists:addons_catalog,id'],
        ]);

        $product = $data['product_id'] 
            ? Product::where('is_active',1)->findOrFail($data['product_id'])
            : Product::where('is_active',1)->where('slug',$data['product_slug'])->firstOrFail();

        // Resolve unit price from matrix/default
        $unit = $this->pricing->resolveUnitPrice($product, $data['v1_option_id'] ?? null, $data['v2_option_id'] ?? null);

        // Snapshot spec & addons
        $snapshot = $this->pricing->buildSnapshot(
            $product,
            $data['v1_option_id'] ?? null,
            $data['v2_option_id'] ?? null,
            $data['addon_ids'] ?? []
        );

        $addonsTotal = $this->pricing->computeAddonsTotal($data['qty'], $snapshot['addons']);
        $lineTotal   = ($unit * $data['qty']) + $addonsTotal;

        $cart = $this->getOrCreateCart($r);
        $sig  = $this->pricing->makeSignature($product->id, $data['v1_option_id'] ?? null, $data['v2_option_id'] ?? null, array_column($snapshot['addons'],'id'));

        // If same signature exists, increment qty & recalc totals
        $existing = $cart->items()->where('signature',$sig)->first();
        if ($existing) {
            $newQty = $existing->qty + (int)$data['qty'];
            $addonsTotalNew = $this->pricing->computeAddonsTotal($newQty, $existing->spec_snapshot['addons'] ?? []);
            $existing->update([
                'qty' => $newQty,
                'unit_price' => $unit, // keep latest price snapshot or keep old? pilih latest
                'addons_total' => $addonsTotalNew,
                'line_total' => ($unit * $newQty) + $addonsTotalNew,
            ]);
        } else {
            $cart->items()->create([
                'product_id'   => $product->id,
                'v1_option_id' => $data['v1_option_id'] ?? null,
                'v2_option_id' => $data['v2_option_id'] ?? null,
                'qty'          => (int)$data['qty'],
                'unit_price'   => $unit,
                'addons_total' => $addonsTotal,
                'line_total'   => $lineTotal,
                'spec_snapshot'=> $snapshot,
                'signature'    => $sig,
                'customer_note'=> $data['customer_note'] ?? null,
            ]);
        }

        return $this->show($r);
    }

    public function updateQty(Request $r, int $id)
    {
        $data = $r->validate(['qty'=>['required','integer','min:1']]);
        $cart = $this->getOrCreateCart($r);
        $item = $cart->items()->where('id',$id)->firstOrFail();

        $unit = (float)$item->unit_price;
        $addons = $item->spec_snapshot['addons'] ?? [];
        $addonsTotal = $this->pricing->computeAddonsTotal($data['qty'], $addons);
        $lineTotal = ($unit * $data['qty']) + $addonsTotal;

        $item->update([
            'qty'=>$data['qty'],
            'addons_total'=>$addonsTotal,
            'line_total'=>$lineTotal,
        ]);

        return $this->show($r);
    }

    public function remove(Request $r, int $id)
    {
        $cart = $this->getOrCreateCart($r);
        $cart->items()->where('id',$id)->delete();
        return $this->show($r);
    }

    public function clear(Request $r)
    {
        $cart = $this->getOrCreateCart($r);
        $cart->items()->delete();
        return $this->show($r);
    }
}
