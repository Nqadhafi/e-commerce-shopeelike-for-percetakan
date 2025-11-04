<?php

namespace App\Services;

use App\Models\{Product, ProductVariantMatrix, Addon, ProductVariantOption};
use Illuminate\Validation\ValidationException;

class CartPricingService
{
    public function resolveUnitPrice(Product $product, ?int $v1OptionId, ?int $v2OptionId): float
    {
        if ($product->has_variants) {
            $row = ProductVariantMatrix::query()
                ->where('product_id', $product->id)
                ->when($v1OptionId, fn($q)=>$q->where('variant1_option_id', $v1OptionId))
                ->when($v2OptionId, fn($q)=>$q->where('variant2_option_id', $v2OptionId))
                ->first();

            if (!$row) {
                throw ValidationException::withMessages(['variant' => 'Kombinasi variasi tidak ditemukan.']);
            }
            return (float)$row->unit_price;
        }

        return (float)($product->default_unit_price ?? 0);
    }

    public function buildSnapshot(Product $product, ?int $v1OptionId, ?int $v2OptionId, array $addonIds): array
    {
        $v1Label = $v2Label = null; $imagePath = null;

        if ($product->has_variants) {
            $row = ProductVariantMatrix::query()
                ->where('product_id', $product->id)
                ->when($v1OptionId, fn($q)=>$q->where('variant1_option_id', $v1OptionId))
                ->when($v2OptionId, fn($q)=>$q->where('variant2_option_id', $v2OptionId))
                ->first();

            if ($row) {
                $imagePath = $row->image_path;
                if ($v1OptionId) $v1Label = optional(ProductVariantOption::find($v1OptionId))->label;
                if ($v2OptionId) $v2Label = optional(ProductVariantOption::find($v2OptionId))->label;
            }
        }

        $addons = Addon::whereIn('id', $addonIds)->get(['id','code','label','type','billing_basis','amount']);
        return [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'unit_label' => $product->unit_label,
                'thumbnail_url' => $product->thumbnail_url,
            ],
            'variants' => [
                'v1' => ['id'=>$v1OptionId, 'label'=>$v1Label],
                'v2' => ['id'=>$v2OptionId, 'label'=>$v2Label],
                'image_path' => $imagePath,
            ],
            'addons' => $addons->map(fn($a)=>[
                'id'=>$a->id,'code'=>$a->code,'label'=>$a->label,'type'=>$a->type,'billing_basis'=>$a->billing_basis,'amount'=>(float)$a->amount
            ])->values()->all(),
        ];
    }

    public function computeAddonsTotal(int $qty, array $addons): float
    {
        $total = 0.0;
        foreach ($addons as $a) {
            $amt = (float)$a['amount'];
            if (($a['billing_basis'] ?? 'per_unit') === 'per_unit') {
                $total += $amt * $qty;
            } else {
                $total += $amt;
            }
        }
        return $total;
    }

    public function makeSignature(int $productId, ?int $v1, ?int $v2, array $addonIds): string
    {
        sort($addonIds);
        return sha1(implode('|', [$productId, $v1 ?? 0, $v2 ?? 0, implode(',', $addonIds)]));
    }
}
