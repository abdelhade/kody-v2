<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    /**
     * Create an invoice with all its line items inside a database transaction.
     *
     * @param array $header
     * @param array $items
     * @param int $userId
     * @return Invoice
     * @throws ValidationException
     */
    public function createInvoice(array $header, array $items, int $userId = 1): Invoice
    {
        $proType = (int) ($header['pro_tybe'] ?? Invoice::TYPE_SALE);
        $storeId = (int) ($header['store_id'] ?? 1);
        $acc2Id  = (int) ($header['acc2_id'] ?? $header['acc2'] ?? 0);

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'يجب إضافة صنف واحد على الأقل داخل الفاتورة.'
            ]);
        }

        return DB::transaction(function () use ($header, $items, $userId, $proType, $storeId, $acc2Id) {
            $computedTotal = 0.0;
            $preparedItems = [];

            foreach ($items as $row) {
                $itemId = (int) ($row['item_id'] ?? 0);
                $qty = (float) ($row['qty'] ?? 1);
                $uVal = (float) ($row['u_val'] ?? 1);
                $price = (float) ($row['price'] ?? 0);
                $discount = (float) ($row['discount'] ?? 0);
                $plus = (float) ($row['plus'] ?? 0);

                $totalQty = $qty * ($uVal > 0 ? $uVal : 1);
                $lineTotal = ($qty * $price) - $discount + $plus;
                $computedTotal += $lineTotal;

                // Determine stock movement based on invoice type
                $qtyIn = 0.0;
                $qtyOut = 0.0;

                switch ($proType) {
                    case Invoice::TYPE_PURCHASE:
                    case Invoice::TYPE_SALE_RETURN:
                        $qtyIn = $totalQty;
                        break;
                    case Invoice::TYPE_SALE:
                    case Invoice::TYPE_POS:
                    case Invoice::TYPE_PURCHASE_RETURN:
                        $qtyOut = $totalQty;
                        break;
                    default:
                        // Orders and Price offers don't affect physical stock
                        break;
                }

                $preparedItems[] = [
                    'item_id'    => $itemId,
                    'det_store'  => $row['store_id'] ?? $storeId,
                    'pro_tybe'   => $proType,
                    'u_val'      => $uVal,
                    'qty_in'     => $qtyIn,
                    'qty_out'    => $qtyOut,
                    'price'      => $price,
                    'cost_price' => $row['cost_price'] ?? 0,
                    'discount'   => $discount,
                    'plus'       => $plus,
                    'det_value'  => $lineTotal,
                    'tenant'     => $header['tenant'] ?? 0,
                    'branch'     => $header['branch'] ?? 0,
                    'isdeleted'  => 0,
                ];
            }

            $fatDisc = (float) ($header['fat_disc'] ?? 0);
            $fatPlus = (float) ($header['fat_plus'] ?? 0);
            $fatTax  = (float) ($header['fat_tax'] ?? 0);
            $fatNet  = $computedTotal - $fatDisc + $fatPlus + $fatTax;

            $paidAmount = (float) ($header['paid_amount'] ?? 0);
            $remainingAmount = max(0, $fatNet - $paidAmount);

            // Create Invoice Header
            $invoice = Invoice::create([
                'pro_tybe'         => $proType,
                'pro_date'         => $header['pro_date'] ?? date('Y-m-d'),
                'accural_date'     => $header['accural_date'] ?? null,
                'pro_serial'       => $header['pro_serial'] ?? null,
                'store_id'         => $storeId,
                'acc2'             => $acc2Id,
                'acc_fund'         => $header['acc_fund'] ?? $header['fund_id'] ?? 0,
                'emp_id'           => $header['emp_id'] ?? 0,
                'info'             => $header['info'] ?? null,
                'fat_total'        => $computedTotal,
                'fat_disc'         => $fatDisc,
                'fat_plus'         => $fatPlus,
                'fat_tax'          => $fatTax,
                'fat_net'          => $fatNet,
                'paid_amount'      => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status'   => $remainingAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'),
                'invoice_status'   => $header['invoice_status'] ?? 'completed',
                'order_type'       => $header['order_type'] ?? 'takeaway',
                'table_id'         => $header['table_id'] ?? null,
                'user'             => $userId,
                'tenant'           => $header['tenant'] ?? 0,
                'branch'           => $header['branch'] ?? 0,
                'isdeleted'        => 0,
            ]);

            // Save line items linked to invoice id
            foreach ($preparedItems as $itemRow) {
                $itemRow['pro_id'] = $invoice->id;
                InvoiceItem::create($itemRow);
            }

            return $invoice->load('items.item', 'partyAccount');
        });
    }
}
