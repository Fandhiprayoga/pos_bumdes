<?php
$products = isset($products) && is_array($products) ? $products : [];
$categories = isset($categories) && is_array($categories) ? $categories : [];
$nextInvoiceNo = isset($nextInvoiceNo) ? (string) $nextInvoiceNo : '-';
?>
<?php $this->section('css') ?>
<style>
  .pos-modern {
    --pm-primary: #0f766e;
    --pm-primary-dark: #0b5f59;
    --pm-accent: #ea580c;
    --pm-border: #e2e8f0;
    --pm-muted: #64748b;
    --pm-bg-soft: #f8fafc;
  }

  .pos-modern .hero {
    border: 1px solid var(--pm-border);
    border-radius: 18px;
    background: linear-gradient(140deg, #f8fafc 0%, #ecfeff 100%);
    padding: 18px 20px;
    margin-bottom: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
  }

  .pos-modern .hero-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 6px;
    color: #0f172a;
  }

  .pos-modern .hero-sub {
    margin-bottom: 0;
    color: var(--pm-muted);
  }

  .pos-modern .panel-card {
    border: 1px solid var(--pm-border);
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    overflow: hidden;
    margin-bottom: 16px;
  }

  .pos-modern .panel-card .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    padding: 14px 16px;
  }

  .pos-modern .panel-card .card-body {
    padding: 16px;
  }

  .pos-modern .shift-pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid #99f6e4;
    background: #f0fdfa;
    color: #0f766e;
    font-size: 12px;
    font-weight: 600;
    gap: 6px;
  }

  .pos-modern .filter-row .form-control,
  .pos-modern .transaction-form .form-control,
  .pos-modern .transaction-form .custom-select {
    border-color: #d8e1ea;
    border-radius: 10px;
    min-height: 42px;
  }

  .pos-modern .filter-row .form-control:focus,
  .pos-modern .transaction-form .form-control:focus,
  .pos-modern .transaction-form .custom-select:focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 0.2rem rgba(20, 184, 166, 0.12);
  }

  .pos-modern .products-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 9px;
    max-height: 72vh;
    overflow: auto;
    padding-right: 2px;
  }

  .pos-modern .product-card {
    border: 1px solid var(--pm-border);
    border-radius: 14px;
    background: #fff;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
  }

  .pos-modern .product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
  }

  .pos-modern .product-media {
    position: relative;
    aspect-ratio: 4 / 3;
    overflow: hidden;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  }

  .pos-modern .product-media.no-image {
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .pos-modern .product-media.no-image::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
      linear-gradient(rgba(203,213,225,0.35) 1px, transparent 1px),
      linear-gradient(90deg, rgba(203,213,225,0.35) 1px, transparent 1px);
    background-size: 18px 18px;
  }

  .pos-modern .product-media.no-image .product-placeholder-icon {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    color: #94a3b8;
  }

  .pos-modern .product-media:not(.no-image) .product-placeholder-icon {
    display: none;
  }

  .pos-modern .product-media.no-image .product-placeholder-icon svg {
    width: 36px;
    height: 36px;
    opacity: 0.55;
  }

  .pos-modern .product-media .product-thumb.is-hidden {
    display: none;
  }

  .pos-modern .product-thumb {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .pos-modern .product-category-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    max-width: calc(100% - 16px);
    padding: 4px 8px;
    border-radius: 999px;
    background: var(--pcb-bg, rgba(15, 23, 42, 0.78));
    color: var(--pcb-fg, #f8fafc);
    border: 1px solid var(--pcb-border, transparent);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    box-shadow: 0 8px 16px rgba(15, 23, 42, 0.16);
    backdrop-filter: blur(2px);
  }

  .pos-modern .product-body {
    padding: 7px 9px 9px;
    display: flex;
    flex-direction: column;
    gap: 3px;
    flex: 1;
  }

  .pos-modern .product-name {
    margin-bottom: 0;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
    min-height: calc(1.35em * 2);
    display: -webkit-box;
    line-clamp: 2;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .pos-modern .product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    color: var(--pm-muted);
    gap: 8px;
  }

  .pos-modern .product-meta span {
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .pos-modern .product-sku {
    text-align: right;
  }

  .pos-modern .product-footer {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding-top: 4px;
  }

  .pos-modern .product-price {
    font-weight: 700;
    color: #0f172a;
    font-size: 13px;
    margin-bottom: 0;
  }

  .pos-modern .btn-add {
    border-radius: 10px;
    background-color: #6777ef !important;
    border-color: #6777ef !important;
    font-weight: 600;
    font-size: 11px;
    padding: 5px 8px;
    color: #fff !important;
    -webkit-tap-highlight-color: transparent;
  }

  .pos-modern .btn-add:hover,
  .pos-modern .btn-add:focus {
    background-color: #4f5ece !important;
    border-color: #4f5ece !important;
    box-shadow: 0 6px 14px rgba(103, 119, 239, 0.24);
    color: #fff !important;
  }

  .pos-modern .btn-add:active,
  .pos-modern .btn-add:not(:disabled):not(.disabled):active,
  .pos-modern .btn-add:not(:disabled):not(.disabled).active,
  .pos-modern .show>.btn-add.dropdown-toggle {
    background-color: #4253d9 !important;
    border-color: #4253d9 !important;
    color: #fff !important;
    box-shadow: 0 3px 8px rgba(66, 83, 217, 0.26);
  }

  .pos-modern .btn-add:focus-visible {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.28);
  }

  .pos-modern .btn-add i {
    color: #fff;
  }

  .pos-modern .btn-add:disabled {
    opacity: 0.55;
    cursor: not-allowed;
  }

  .pos-modern .category-chips {
    display: none;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 2px;
    margin-bottom: 14px;
    -webkit-overflow-scrolling: touch;
  }

  .pos-modern .category-chip {
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #334155;
    border-radius: 999px;
    padding: 8px 14px;
    white-space: nowrap;
    font-size: 13px;
    font-weight: 600;
    min-height: 40px;
  }

  .pos-modern .category-chip.active {
    background: #6777ef;
    border-color: #6777ef;
    color: #fff;
    box-shadow: 0 8px 18px rgba(103, 119, 239, 0.24);
  }

  .pos-modern .toolbar-actions {
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .pos-modern .cart-toggle-btn {
    display: none;
    position: relative;
    min-height: 40px;
    border-radius: 10px;
    font-weight: 600;
  }

  .pos-modern .cart-toggle-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    border-radius: 999px;
    background: #ea580c;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    margin-left: 6px;
  }

  .pos-modern .cart-drawer-close {
    display: none;
    min-height: 38px;
    min-width: 38px;
    border-radius: 10px;
  }

  .pos-modern .cart-drawer-backdrop {
    display: none;
  }

  .pos-modern .empty-products,
  .pos-modern .empty-cart {
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    color: var(--pm-muted);
    background: #fcfdff;
  }

  .pos-modern .empty-cart {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .pos-modern .empty-products {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .pos-modern .empty-cart-visual {
    width: 58px;
    height: 58px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
  }

  .pos-modern .empty-cart-visual i {
    font-size: 20px;
    color: #94a3b8;
  }

  .pos-modern .empty-products-visual {
    width: 58px;
    height: 58px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
  }

  .pos-modern .empty-products-visual i {
    font-size: 20px;
    color: #94a3b8;
  }

  .pos-modern .cart-table td,
  .pos-modern .cart-table th {
    vertical-align: middle;
    border-color: #edf2f7;
  }

  .pos-modern .cart-table .cart-row-active {
    background: #ecfeff;
    box-shadow: inset 3px 0 0 #0f766e;
  }

  .pos-modern .qty-control {
    display: inline-flex;
    align-items: center;
    border: 1px solid #dbe3ec;
    border-radius: 999px;
    overflow: hidden;
  }

  .pos-modern .qty-control button {
    border: none;
    background: #f8fafc;
    width: 28px;
    height: 28px;
    color: #334155;
  }

  .pos-modern .qty-control span {
    min-width: 34px;
    text-align: center;
    font-weight: 600;
    font-size: 13px;
  }

  .pos-modern .summary-box {
    border: 1px solid var(--pm-border);
    border-radius: 12px;
    background: var(--pm-bg-soft);
    padding: 12px;
  }

  .pos-modern .sticky-cart-summary {
    position: sticky;
    top: 86px;
    z-index: 5;
  }

  @media (min-width: 993px) {
    .pos-modern .cart-column {
      position: sticky;
      top: 72px;
      align-self: flex-start;
      max-height: calc(100vh - 88px);
      overflow-y: auto;
    }
  }

  .pos-modern .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    color: #334155;
  }

  .pos-modern .summary-row.total {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed #cbd5e1;
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
  }

  .pos-modern .transaction-form .btn-save {
    border-radius: 10px;
    min-height: 44px;
    font-weight: 700;
    background: var(--pm-accent);
    border-color: var(--pm-accent);
  }

  .pos-modern .transaction-form .btn-save:hover,
  .pos-modern .transaction-form .btn-save:focus {
    background: #c2410c;
    border-color: #c2410c;
  }

  .pos-modern .quick-amount-pad {
    margin-top: 10px;
  }

  .pos-modern .quick-amount-section + .quick-amount-section {
    margin-top: 14px;
  }

  .pos-modern .quick-amount-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
  }

  .pos-modern .quick-amount-title {
    margin: 0;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: 0.01em;
  }

  .pos-modern .quick-amount-note {
    margin: 0;
    font-size: 11px;
    color: #64748b;
  }

  .pos-modern .quick-amount-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
  }

  .pos-modern .quick-amount-btn {
    border: 1px solid #cbd5e1;
    background: #fff;
    border-radius: 12px;
    min-height: 58px;
    padding: 8px 10px;
    text-align: left;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
  }

  .pos-modern .quick-amount-btn:hover,
  .pos-modern .quick-amount-btn:focus {
    border-color: #14b8a6;
    box-shadow: 0 10px 20px rgba(20, 184, 166, 0.12);
    transform: translateY(-1px);
    outline: none;
  }

  .pos-modern .quick-amount-btn:disabled {
    opacity: 0.55;
    box-shadow: none;
    transform: none;
  }

  .pos-modern .quick-amount-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 3px;
  }

  .pos-modern .quick-amount-value {
    display: block;
    font-size: 12px;
    color: #64748b;
    line-height: 1.35;
  }

  .pos-modern .quick-amount-btn.is-suggested {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  }

  .pos-modern .quick-keypad-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
  }

  .pos-modern .quick-keypad-btn {
    min-height: 56px;
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #0f172a;
    font-size: 18px;
    font-weight: 700;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
  }

  .pos-modern .quick-keypad-btn:hover,
  .pos-modern .quick-keypad-btn:focus {
    border-color: #14b8a6;
    box-shadow: 0 10px 20px rgba(20, 184, 166, 0.12);
    transform: translateY(-1px);
    outline: none;
  }

  .pos-modern .quick-keypad-btn.keypad-action {
    font-size: 14px;
    font-weight: 700;
    background: #f8fafc;
  }

  .pos-modern .amount-paid-input {
    text-align: right;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 0.01em;
  }

  .pos-modern .payment-modal .modal-content {
    border-radius: 16px;
    border: 1px solid #dbe3ec;
  }

  .pos-modern .payment-modal .modal-header {
    background: linear-gradient(135deg, #0f766e 0%, #0b5f59 100%);
    color: #fff;
    border-radius: 16px 16px 0 0;
    padding: 16px 20px;
  }

  .pos-modern .payment-modal .modal-title {
    color: inherit;
  }

  .pos-modern .payment-modal .close {
    color: #fff;
    opacity: 0.8;
    text-shadow: none;
  }

  .pos-modern .payment-modal .close:hover {
    opacity: 1;
  }

  .pos-modern .payment-recap {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
    background: #f8fafc;
    margin-bottom: 16px;
  }

  .pos-modern .payment-recap-total {
    font-size: 28px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
  }

  .pos-modern .payment-recap-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
  }

  .pos-modern .payment-recap-change {
    font-size: 15px;
    font-weight: 700;
    color: #0f766e;
  }

  .pos-modern .btn-pay {
    border-radius: 10px;
    min-height: 46px;
    font-weight: 700;
    font-size: 15px;
    background-color: #6777ef !important;
    border-color: #6777ef !important;
    color: #fff !important;
    -webkit-tap-highlight-color: transparent;
  }

  .pos-modern .btn-pay:hover,
  .pos-modern .btn-pay:focus {
    background-color: #4f5ece !important;
    border-color: #4f5ece !important;
    box-shadow: 0 6px 14px rgba(103, 119, 239, 0.24);
    color: #fff !important;
  }

  .pos-modern .btn-pay:active,
  .pos-modern .btn-pay:not(:disabled):not(.disabled):active,
  .pos-modern .btn-pay:not(:disabled):not(.disabled).active,
  .pos-modern .show>.btn-pay.dropdown-toggle {
    background-color: #4253d9 !important;
    border-color: #4253d9 !important;
    color: #fff !important;
    box-shadow: 0 3px 8px rgba(66, 83, 217, 0.26);
  }

  .pos-modern .btn-pay:focus-visible {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.28);
  }

  .pos-modern .btn-pay i {
    color: #fff;
  }

  .pos-modern .btn-pay:disabled {
    opacity: 0.55;
    cursor: not-allowed;
  }

  .pos-modern .checkout-actions {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.6fr) minmax(0, 1.8fr);
    gap: 8px;
  }

  .pos-modern .btn-save-pending {
    border-radius: 10px;
    min-height: 46px;
    font-weight: 700;
    font-size: 14px;
    border: 1px solid #f59e0b;
    background: #fff7ed;
    color: #b45309;
  }

  .pos-modern .btn-save-pending:hover,
  .pos-modern .btn-save-pending:focus {
    border-color: #d97706;
    background: #ffedd5;
    color: #92400e;
    box-shadow: 0 6px 14px rgba(245, 158, 11, 0.18);
  }

  .pos-modern .pending-item {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 0;
    background: #fff;
    overflow: hidden;
    transition: box-shadow 0.18s ease;
  }

  .pos-modern .pending-item:hover {
    box-shadow: 0 6px 18px rgba(15,23,42,0.08);
  }

  .pos-modern .pending-item + .pending-item {
    margin-top: 10px;
  }

  .pos-modern .pending-item-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px 10px;
    border-bottom: 1px solid #f1f5f9;
  }

  .pos-modern .pending-item-title {
    margin: 0 0 2px;
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    font-family: Menlo, Monaco, Consolas, monospace;
    letter-spacing: 0.02em;
  }

  .pos-modern .pending-item-customer {
    font-size: 12px;
    color: #64748b;
    margin: 0;
  }

  .pos-modern .pending-item-meta {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0;
    font-size: 12px;
  }

  .pos-modern .pending-item-meta-cell {
    padding: 8px 14px;
    display: flex;
    flex-direction: column;
    gap: 2px;
    border-right: 1px solid #f1f5f9;
  }

  .pos-modern .pending-item-meta-cell:last-child {
    border-right: none;
  }

  .pos-modern .pending-item-meta-label {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #94a3b8;
    margin: 0;
  }

  .pos-modern .pending-item-meta-value {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
  }

  .pos-modern .pending-item-meta-value.text-teal {
    color: #0f766e;
  }

  .pos-modern .pending-item-actions {
    display: flex;
    gap: 0;
    border-top: 1px solid #f1f5f9;
  }

  .pos-modern .pending-item-actions .btn-restore,
  .pos-modern .pending-item-actions .btn-delete-pending {
    flex: 1;
    border-radius: 0;
    border: none;
    padding: 9px 10px;
    font-size: 13px;
    font-weight: 600;
  }

  .pos-modern .pending-item-actions .btn-restore {
    background: #f0fdf4;
    color: #15803d;
    border-right: 1px solid #dcfce7;
  }

  .pos-modern .pending-item-actions .btn-restore:hover {
    background: #dcfce7;
    color: #166534;
  }

  .pos-modern .pending-item-actions .btn-delete-pending {
    background: #fff1f2;
    color: #be123c;
  }

  .pos-modern .pending-item-actions .btn-delete-pending:hover {
    background: #ffe4e6;
    color: #9f1239;
  }

  .pos-modern .pending-modal-header-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
  }

  .pos-modern .pending-modal-count-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    background: #fef3c7;
    color: #92400e;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid #fde68a;
  }

  @media (max-width: 576px) {
    .pos-modern .pending-item-meta {
      grid-template-columns: 1fr 1fr;
    }
    .pos-modern .pending-item-meta-cell:nth-child(2) {
      border-right: none;
    }
    .pos-modern .pending-item-meta-cell:nth-child(3) {
      border-top: 1px solid #f1f5f9;
      grid-column: span 2;
    }
  }

  .pos-modern .btn-reset-cart {
    border-radius: 10px;
    min-height: 46px;
    font-weight: 700;
    font-size: 14px;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #334155;
  }

  .pos-modern .btn-reset-cart:hover,
  .pos-modern .btn-reset-cart:focus {
    border-color: #94a3b8;
    background: #f8fafc;
    color: #1e293b;
    box-shadow: 0 6px 14px rgba(100, 116, 139, 0.16);
  }

  .pos-modern .btn-reset-cart:active,
  .pos-modern .btn-reset-cart:not(:disabled):not(.disabled):active,
  .pos-modern .btn-reset-cart:not(:disabled):not(.disabled).active,
  .pos-modern .show>.btn-reset-cart.dropdown-toggle {
    background: #eef2f7;
    border-color: #94a3b8;
    color: #1e293b;
    box-shadow: 0 3px 8px rgba(100, 116, 139, 0.18);
  }

  .pos-modern .btn-reset-cart:focus-visible {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(148, 163, 184, 0.3);
  }

  .pos-modern .floating-scan {
    position: fixed;
    right: 22px;
    bottom: 22px;
    width: 56px;
    height: 56px;
    border-radius: 999px;
    border: none;
    background-color: #6777ef;
    color: #fff;
    box-shadow: 0 14px 30px rgba(103, 119, 239, 0.35);
    z-index: 1040;
    transition: background-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    -webkit-tap-highlight-color: transparent;
  }

  .pos-modern .shortcut-hint {
    color: var(--pm-muted);
    font-size: 12px;
    margin-top: 6px;
    margin-bottom: 0;
  }

  .pos-modern .shortcut-help-btn {
    width: 30px;
    height: 30px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    font-size: 13px;
  }

  .pos-modern .katalog-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
  }

  .pos-modern .katalog-header .shortcut-help-btn {
    margin-right: -4px;
  }

  .pos-modern .shortcut-list-group {
    margin-bottom: 0;
  }

  .pos-modern .shortcut-item {
    color: #334155;
    font-size: 14px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 10px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
  }

  .pos-modern .shortcut-item:last-child {
    margin-bottom: 0;
  }

  .pos-modern .shortcut-action {
    color: #475569;
    font-size: 13px;
    margin: 0;
    flex: 1;
    text-align: right;
  }

  .pos-modern .kbd-group {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
  }

  .pos-modern .kbd-key {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 28px;
    padding: 0 10px;
    border-radius: 8px;
    border: 1px solid #94a3b8;
    border-bottom: 3px solid #64748b;
    background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
    box-shadow: 0 2px 0 rgba(15, 23, 42, 0.18), 0 4px 10px rgba(15, 23, 42, 0.12);
    color: #0f172a;
    font-size: 12px;
    font-weight: 700;
    font-family: Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
    line-height: 1;
    letter-spacing: 0.02em;
    text-transform: uppercase;
  }

  .pos-modern .shortcut-plus {
    color: #64748b;
    font-weight: 700;
    font-size: 12px;
  }


  .pos-modern .floating-scan:hover,
  .pos-modern .floating-scan:focus {
    background-color: #4f5ece;
    color: #fff;
    box-shadow: 0 6px 14px rgba(103, 119, 239, 0.24);
    transform: translateY(-2px);
    outline: none;
  }

  .pos-modern .floating-scan:active {
    background-color: #4253d9;
    color: #fff;
    box-shadow: 0 3px 8px rgba(66, 83, 217, 0.26);
    transform: translateY(0);
  }

  .pos-modern .scan-modal .modal-content {
    border-radius: 14px;
    border: 1px solid #dbe3ec;
  }

  @media (max-width: 1200px) {
    .pos-modern .products-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr));
      max-height: none;
    }
  }

  @media (max-width: 992px) {
    body.pos-cart-drawer-open {
      overflow: hidden;
    }

    body.pos-cart-drawer-open .navbar-bg,
    body.pos-cart-drawer-open .main-navbar,
    body.pos-cart-drawer-open .main-sidebar {
      z-index: 1 !important;
    }

    .pos-modern .toolbar-actions {
      display: inline-flex;
    }

    .pos-modern .cart-toggle-btn {
      display: inline-flex;
      align-items: center;
    }

    .pos-modern .category-chips {
      display: flex;
    }

    .pos-modern .filter-row .form-group.col-md-6:last-child {
      display: none;
    }

    .pos-modern .products-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .pos-modern .sticky-cart-summary {
      position: static;
    }

    .pos-modern .product-card {
      border-radius: 16px;
    }

    .pos-modern .product-body {
      padding: 10px 12px 12px;
      gap: 6px;
    }

    .pos-modern .product-name {
      font-size: 14px;
      min-height: 34px;
    }

    .pos-modern .product-sku {
      display: none;
    }

    .pos-modern .btn-add {
      min-height: 42px;
      font-size: 13px;
      padding: 8px 10px;
    }

    .pos-modern .quick-amount-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .pos-modern .qty-control button {
      width: 36px;
      height: 36px;
      font-size: 16px;
    }

    .pos-modern .qty-control span {
      min-width: 42px;
      font-size: 14px;
    }

    .pos-modern .cart-drawer-backdrop {
      display: block;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.42);
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.18s ease;
      z-index: 2147483000;
    }

    .pos-modern .cart-drawer-backdrop.is-open {
      opacity: 1;
      pointer-events: auto;
    }

    .pos-modern .cart-column {
      position: fixed;
      top: 0;
      right: 0;
      width: min(420px, 92vw);
      height: 100vh;
      padding: 12px;
      background: #f8fafc;
      transform: translateX(100%);
      transition: transform 0.2s ease;
      z-index: 2147483001;
      overflow-y: auto;
    }

    .pos-modern .cart-column.is-open {
      transform: translateX(0);
    }

    .pos-modern .cart-column .panel-card {
      min-height: calc(100vh - 24px);
      margin-bottom: 0;
      box-shadow: 0 16px 40px rgba(15, 23, 42, 0.14);
    }

    .pos-modern .cart-drawer-close {
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
  }

  @media (max-width: 576px) {
    .pos-modern .hero-title {
      font-size: 20px;
    }

    .pos-modern .products-grid {
      grid-template-columns: 1fr;
      gap: 10px;
      max-height: none;
    }

    .pos-modern .panel-card .card-body,
    .pos-modern .panel-card .card-header {
      padding: 12px;
    }

    .pos-modern .floating-scan {
      right: 14px;
      bottom: 14px;
      width: 50px;
      height: 50px;
    }

    .pos-modern .cart-column {
      width: 100vw;
      padding: 0;
    }

    .pos-modern .cart-column .panel-card {
      border-radius: 0;
      min-height: 100vh;
    }

    .pos-modern .quick-amount-grid {
      grid-template-columns: 1fr;
    }

    .pos-modern .checkout-actions {
      grid-template-columns: 1fr;
    }

    .pos-modern .quick-keypad-grid {
      grid-template-columns: 1fr;
    }

    .pos-modern .pending-item-meta {
      grid-template-columns: 1fr;
    }
  }
</style>
<?= $this->endSection() ?>

<div class="pos-modern">
  <input type="hidden" id="pos-csrf-name" value="<?= esc(csrf_token()) ?>">
  <input type="hidden" id="pos-csrf-hash" value="<?= esc(csrf_hash()) ?>">

  <form action="<?= base_url('pos/checkout') ?>" method="post" id="checkout-form" class="transaction-form"
    data-pending-list-url="<?= esc(base_url('pos/pending-transactions')) ?>"
    data-pending-save-url="<?= esc(base_url('pos/pending-transactions')) ?>"
    data-pending-restore-base-url="<?= esc(base_url('pos/pending-transactions')) ?>"
    data-pending-delete-base-url="<?= esc(base_url('pos/pending-transactions')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="invoice_no" value="<?= esc($nextInvoiceNo) ?>">

    <div class="row">
      <div class="col-12 col-lg-7">
        <div class="card panel-card">
          <div class="card-header">
            <div class="katalog-header">
              <h4 class="mb-0">Katalog Produk</h4>
              <div class="toolbar-actions">
                <button type="button" class="btn btn-outline-secondary btn-sm cart-toggle-btn" id="cart-toggle-btn">
                  <i class="fas fa-shopping-basket mr-1"></i> Cart
                  <span class="cart-toggle-badge" id="cart-toggle-badge">0</span>
                </button>
                <!-- <button type="button" class="btn btn-outline-secondary btn-sm shortcut-help-btn" data-toggle="modal" data-target="#shortcutHelpModal" title="Lihat shortcut keyboard">
                  <i class="fas fa-question"></i>
                </button> -->
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="category-chips" id="category-chips">
              <button type="button" class="category-chip active" data-category="">Semua</button>
              <?php foreach ($categories as $category): ?>
                <button type="button" class="category-chip" data-category="<?= esc((string) ($category['name'] ?? '')) ?>">
                  <?= esc((string) ($category['name'] ?? '')) ?>
                </button>
              <?php endforeach; ?>
            </div>

            <div class="form-row filter-row mb-3">
              <div class="form-group col-md-6 mb-2 mb-md-0">
                <label for="product-search">Cari Produk</label>
                <input type="text" id="product-search" class="form-control" placeholder="Cari nama / SKU produk (Esc fokus, Enter tambah)">
              </div>
              <div class="form-group col-md-6 mb-0">
                <label for="product-category-filter">Filter Kategori</label>
                <select id="product-category-filter" class="form-control custom-select">
                  <option value="">Semua Kategori</option>
                  <?php foreach ($categories as $category): ?>
                    <option value="<?= esc((string) ($category['name'] ?? '')) ?>"><?= esc((string) ($category['name'] ?? '')) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="products-grid" id="products-grid">
              <?php foreach ($products as $product): ?>
                <?php
                $name = (string) ($product['name'] ?? 'Tanpa Nama');
                $category = trim((string) ($product['category'] ?? 'Umum'));
                $sku = (string) ($product['sku'] ?? '');
                $stock = (int) ($product['stock'] ?? 0);
                $imagePath = trim((string) ($product['image'] ?? ''));
                $imageUrl = $imagePath !== '' ? base_url($imagePath) : '';
                ?>
                <div class="product-card js-product-card"
                  data-product-id="<?= (int) $product['id'] ?>"
                  data-name="<?= esc($name) ?>"
                  data-category="<?= esc($category) ?>"
                  data-sku="<?= esc($sku) ?>"
                  data-price="<?= (float) $product['sell_price'] ?>"
                  data-stock="<?= $stock ?>">
                  <div class="product-media<?= $imageUrl === '' ? ' no-image' : '' ?>" data-js-product-media>
                    <?php if ($imageUrl !== ''): ?>
                    <img src="<?= esc($imageUrl) ?>" alt="<?= esc($name) ?>" class="product-thumb js-product-image"
                      onerror="this.closest('[data-js-product-media]').classList.add('no-image');this.remove();">
                    <?php endif; ?>
                    <span class="product-placeholder-icon" aria-hidden="true">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                        <rect x="2" y="3" width="20" height="16" rx="2"/>
                        <path d="M2 15l5-5 4 4 3-3 8 6" stroke-linejoin="round" stroke-linecap="round"/>
                      </svg>
                    </span>
                    <span class="product-category-badge"><?= esc($category !== '' ? $category : 'Umum') ?></span>
                  </div>
                  <div class="product-body">
                    <p class="product-name"><?= esc($name) ?></p>
                    <div class="product-meta">
                      <span>Stok: <?= $stock ?></span>
                      <span class="product-sku"><?= esc($sku !== '' ? $sku : '-') ?></span>
                    </div>
                    <div class="product-footer">
                      <p class="product-price">Rp <?= number_format((float) $product['sell_price'], 0, ',', '.') ?></p>
                      <button type="button" class="btn btn-sm btn-add js-add-to-cart" <?= $stock <= 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-plus mr-1"></i> Tambah ke Cart
                      </button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <div id="empty-products" class="empty-products d-none mt-3">
              <div class="empty-products-visual" aria-hidden="true">
                <i class="fas fa-search"></i>
              </div>
              <p class="mb-0">Produk tidak ditemukan untuk filter saat ini.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-5 cart-column" id="cart-column">
        <div class="card panel-card">
          <div class="card-header">
            <div class="d-flex align-items-center w-100">
              <div>
                <h4 class="mb-0">Detail Transaksi</h4>
                <small class="text-muted d-block mt-1">Invoice: <strong id="current-invoice-label"><?= esc($nextInvoiceNo) ?></strong></small>
              </div>
              <div class="d-flex align-items-center ml-auto">
                <button type="button" class="btn btn-outline-warning btn-sm mr-2 position-relative" id="btn-pending-transactions" title="Transaksi Tertunda">
                  <i class="fas fa-pause-circle"></i>
                  <span class="badge badge-warning badge-pill" id="pending-transactions-count" style="position:absolute;top:-6px;right:-6px;font-size:10px;">0</span>
                </button>
                <button type="button" class="btn btn-outline-secondary cart-drawer-close" id="cart-drawer-close">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive mb-3">
              <table class="table table-sm cart-table mb-0" id="cart-table" tabindex="-1">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody id="cart-items-body"></tbody>
              </table>
            </div>

            <div id="empty-cart" class="empty-cart mb-3">
              <div class="empty-cart-visual" aria-hidden="true">
                <i class="fas fa-shopping-basket"></i>
              </div>
              <p class="mb-0">Belum ada item. Pilih produk dari kolom kiri.</p>
            </div>

            <div id="checkout-hidden-items"></div>

            <div class="form-group">
              <label for="customer_name">Nama Pelanggan</label>
              <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Opsional">
            </div>

            <div class="form-group">
              <label for="discount_amount">Diskon (Rp)</label>
              <input type="number" id="discount_amount" class="form-control" name="discount_amount" value="0" min="0" step="0.01">
            </div>

            <div class="sticky-cart-summary">
              <div class="summary-box mb-3">
                <div class="summary-row">
                  <span>Subtotal</span>
                  <strong id="summary-subtotal">Rp 0</strong>
                </div>
                <div class="summary-row">
                  <span>Diskon</span>
                  <strong id="summary-discount">Rp 0</strong>
                </div>
                <div class="summary-row total">
                  <span>Grand Total</span>
                  <span id="summary-grand-total">Rp 0</span>
                </div>
              </div>

              <div class="checkout-actions">
                <button type="button" class="btn btn-reset-cart" id="btn-reset-cart">
                  <i class="fas fa-undo-alt"></i>
                </button>
                <button type="button" class="btn btn-save-pending" id="btn-save-pending">
                  <i class="fas fa-pause mr-1"></i> Simpan Tertunda
                </button>
                <button type="button" class="btn btn-pay btn-block" id="btn-pay">
                  <i class="fas fa-cash-register mr-1"></i> Lanjut Bayar
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </form>

  <button type="button" class="floating-scan" id="floating-scan-btn" title="Scan Barcode">
    <i class="fas fa-camera"></i>
  </button>

  <div class="cart-drawer-backdrop" id="cart-drawer-backdrop"></div>
</div>

<div class="modal fade pos-modern" id="pendingTransactionsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:560px;" role="document">
    <div class="modal-content" style="border-radius:16px;border:1px solid #dbe3ec;">
      <div class="modal-header" style="background:linear-gradient(135deg,#b45309 0%,#92400e 100%);border-radius:16px 16px 0 0;padding:16px 20px;">
        <div>
          <h5 class="modal-title mb-1" style="color:#fff;"><i class="fas fa-pause-circle mr-2"></i>Transaksi Tertunda</h5>
          <p class="mb-0" style="font-size:12px;color:rgba(255,255,255,0.75);">Lanjutkan atau hapus transaksi yang di-hold</p>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:.8;text-shadow:none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding:16px 16px 10px;">
        <div class="pending-modal-header-info">
          <p class="text-muted small mb-0">Klik <strong>Ambil</strong> untuk lanjutkan, atau <strong>Hapus</strong> untuk batalkan.</p>
          <span class="pending-modal-count-badge"><i class="fas fa-clock"></i> <span id="pending-modal-count-text">0 transaksi</span></span>
        </div>
        <div id="pending-transactions-empty" class="empty-cart d-none mb-0" style="border-radius:12px;">
          <div class="empty-cart-visual" aria-hidden="true">
            <i class="fas fa-inbox"></i>
          </div>
          <p class="mb-0">Belum ada transaksi tertunda.</p>
        </div>
        <div id="pending-transactions-list"></div>
      </div>
      <div class="modal-footer" style="border-top:1px solid #f1f5f9;padding:10px 16px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade payment-modal pos-modern" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-1">Pembayaran</h5>
          <div class="d-flex align-items-baseline gap-2">
            <span style="font-size:12px;opacity:.8;">Grand Total:</span>
            <span id="modal-grand-total" style="font-size:22px;font-weight:800;margin-left:6px;">Rp 0</span>
          </div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 col-md-5 mb-3 mb-md-0">
            <div class="payment-recap">
              <p class="quick-amount-note mb-1">Jumlah Bayar</p>
              <p class="payment-recap-total" id="modal-amount-display">Rp 0</p>
            </div>

            <div class="form-group">
              <label for="payment_method">Metode Bayar</label>
              <select id="payment_method" class="form-control custom-select" name="payment_method" form="checkout-form" required>
                <option value="cash">Tunai</option>
                <option value="transfer">Transfer</option>
              </select>
            </div>

            <input type="hidden" id="amount_paid_value" name="amount_paid" form="checkout-form" value="0">

            <div class="payment-recap mt-3">
              <p class="quick-amount-note mb-1">Kembalian</p>
              <p class="payment-recap-change" id="modal-change-display">Rp 0</p>
            </div>
          </div>

          <div class="col-12 col-md-7">
            <div class="quick-amount-pad d-block" id="quick-amount-pad">
              <div class="quick-amount-section">
                <div class="quick-amount-heading">
                  <p class="quick-amount-title">Quick Amount</p>
                  <p class="quick-amount-note">Untuk pembayaran tunai</p>
                </div>
                <div class="quick-amount-grid">
                  <button type="button" class="quick-amount-btn js-quick-amount" data-mode="exact">
                    <span class="quick-amount-label">Uang Pas</span>
                    <span class="quick-amount-value">Sama dengan grand total</span>
                  </button>
                  <button type="button" class="quick-amount-btn js-quick-amount" data-mode="round" data-step="5000">
                    <span class="quick-amount-label">Bulat 5rb</span>
                    <span class="quick-amount-value">Naik ke kelipatan 5.000</span>
                  </button>
                  <button type="button" class="quick-amount-btn js-quick-amount" data-mode="round" data-step="10000">
                    <span class="quick-amount-label">Bulat 10rb</span>
                    <span class="quick-amount-value">Naik ke kelipatan 10.000</span>
                  </button>
                </div>
              </div>

              <div class="quick-amount-section">
                <div class="quick-amount-heading">
                  <p class="quick-amount-title">Keypad Tunai</p>
                  <p class="quick-amount-note">Input tanpa keyboard fisik</p>
                </div>
                <div class="quick-keypad-grid">
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="1">1</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="2">2</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="3">3</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="4">4</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="5">5</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="6">6</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="7">7</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="8">8</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="9">9</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="00">00</button>
                  <button type="button" class="quick-keypad-btn keypad-action js-keypad-action" data-action="clear">C</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="0">0</button>
                  <button type="button" class="quick-keypad-btn js-keypad-digit" data-digit="000">000</button>
                  <button type="button" class="quick-keypad-btn keypad-action js-keypad-action" data-action="delete">Del</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid #f1f5f9;padding:12px 20px;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" form="checkout-form" name="checkout_action" value="save" class="btn btn-primary btn-save">
          <i class="fas fa-save mr-1"></i> Simpan Transaksi
        </button>
        <button type="submit" form="checkout-form" name="checkout_action" value="save_print" class="btn btn-outline-primary btn-save flex-grow-1">
          <i class="fas fa-print mr-1"></i> Simpan & Cetak Nota
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade scan-modal" id="cameraScanModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-camera mr-1"></i> Scan Barcode</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="camera-scan-alert" class="alert alert-danger d-none"></div>
        <div id="camera-scan-success" class="alert alert-success d-none"></div>
        <div id="camera-reader" style="width:100%;"></div>
        <p class="text-muted small mb-0 mt-2">Arahkan barcode ke kamera, item akan otomatis masuk ke cart.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="shortcutHelpModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="true" data-keyboard="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-keyboard mr-1"></i> Daftar Shortcut POS</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-3">Gunakan shortcut berikut agar transaksi lebih cepat tanpa banyak klik mouse.</p>
        <div class="shortcut-list-group">
          <div class="shortcut-item">
            <div class="kbd-group"><span class="kbd-key">Enter</span></div>
            <p class="shortcut-action">Tambah item dari pencarian lalu pindah ke cart.</p>
          </div>
          <div class="shortcut-item mt-2">
            <div class="kbd-group"><span class="kbd-key">Esc</span></div>
            <p class="shortcut-action">Fokus kembali ke kolom pencarian produk.</p>
          </div>
          <div class="shortcut-item mt-2">
            <div class="kbd-group"><span class="kbd-key">↑</span><span class="kbd-key">↓</span></div>
            <p class="shortcut-action">Pindah item aktif di cart.</p>
          </div>
          <div class="shortcut-item mt-2">
            <div class="kbd-group"><span class="kbd-key">+</span><span class="kbd-key">-</span></div>
            <p class="shortcut-action">Tambah atau kurangi qty item aktif.</p>
          </div>
          <div class="shortcut-item mt-2">
            <div class="kbd-group"><span class="kbd-key">Del</span></div>
            <p class="shortcut-action">Hapus item aktif dari cart.</p>
          </div>
          <div class="shortcut-item mt-2">
            <div class="kbd-group"><span class="kbd-key">F2</span></div>
            <p class="shortcut-action">Fokus ke input diskon.</p>
          </div>
          <div class="shortcut-item mt-2">
            <div class="kbd-group"><span class="kbd-key">F3</span></div>
            <p class="shortcut-action">Fokus ke input jumlah bayar.</p>
          </div>
          <div class="shortcut-item mt-2">
            <div class="kbd-group"><span class="kbd-key">F9</span></div>
            <p class="shortcut-action">Simpan transaksi.</p>
          </div>
          <div class="shortcut-item mt-2">
            <div class="kbd-group">
              <span class="kbd-key" id="scanner-shortcut-modifier">Ctrl</span>
              <span class="shortcut-plus">+</span>
              <span class="kbd-key">B</span>
            </div>
            <p class="shortcut-action">Buka scanner barcode.</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?php $this->section('page_js') ?>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="<?= base_url('assets/js/pos/namespace.js') ?>"></script>
<script src="<?= base_url('assets/js/pos/modules/logic.js') ?>"></script>
<script src="<?= base_url('assets/js/pos/modules/bindings.js') ?>"></script>
<?= $this->endSection() ?>