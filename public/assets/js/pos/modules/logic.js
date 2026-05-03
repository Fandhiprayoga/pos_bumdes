(function(window) {
  const POS = window.POS || {};
  const shared = POS.shared || {};

  // ------------------------------------------------------------
  // DOM references and runtime state.
  // ------------------------------------------------------------
  const dom = {
    cards: Array.from(document.querySelectorAll('.js-product-card')),
    searchInput: document.getElementById('product-search'),
    categoryFilter: document.getElementById('product-category-filter'),
    emptyProducts: document.getElementById('empty-products'),

    cartBody: document.getElementById('cart-items-body'),
    cartTable: document.getElementById('cart-table'),
    emptyCart: document.getElementById('empty-cart'),
    hiddenItemsWrap: document.getElementById('checkout-hidden-items'),
    customerNameInput: document.getElementById('customer_name'),
    discountInput: document.getElementById('discount_amount'),
    amountPaidInput: document.getElementById('amount_paid'),
    amountPaidValueInput: document.getElementById('amount_paid_value'),
    quickAmountPad: document.getElementById('quick-amount-pad'),
    quickAmountButtons: Array.from(document.querySelectorAll('.js-quick-amount')),
    keypadDigitButtons: Array.from(document.querySelectorAll('.js-keypad-digit')),
    keypadActionButtons: Array.from(document.querySelectorAll('.js-keypad-action')),
    checkoutForm: document.getElementById('checkout-form'),
    btnPay: document.getElementById('btn-pay'),
    btnSavePending: document.getElementById('btn-save-pending'),
    btnResetCart: document.getElementById('btn-reset-cart'),
    btnPendingTransactions: document.getElementById('btn-pending-transactions'),
    paymentModal: document.getElementById('paymentModal'),
    pendingTransactionsModal: document.getElementById('pendingTransactionsModal'),
    pendingTransactionsList: document.getElementById('pending-transactions-list'),
    pendingTransactionsEmpty: document.getElementById('pending-transactions-empty'),
    pendingTransactionsCountEl: document.getElementById('pending-transactions-count'),
    modalGrandTotalEl: document.getElementById('modal-grand-total'),
    modalAmountDisplayEl: document.getElementById('modal-amount-display'),
    modalChangeDisplayEl: document.getElementById('modal-change-display'),
    currentInvoiceLabel: document.getElementById('current-invoice-label'),
    invoiceHiddenInput: document.querySelector('#checkout-form input[name="invoice_no"]'),
    csrfNameInput: document.getElementById('pos-csrf-name'),
    csrfHashInput: document.getElementById('pos-csrf-hash'),

    subtotalEl: document.getElementById('summary-subtotal'),
    discountEl: document.getElementById('summary-discount'),
    grandTotalEl: document.getElementById('summary-grand-total'),
    navPosItemCountEl: document.getElementById('nav-pos-item-count'),
    navPosGrandTotalEl: document.getElementById('nav-pos-grand-total'),
    scanButton: document.getElementById('floating-scan-btn'),
    cartToggleButton: document.getElementById('cart-toggle-btn'),
    cartToggleBadge: document.getElementById('cart-toggle-badge'),
    cartColumn: document.getElementById('cart-column'),
    cartDrawerClose: document.getElementById('cart-drawer-close'),
    cartDrawerBackdrop: document.getElementById('cart-drawer-backdrop'),
    categoryChips: Array.from(document.querySelectorAll('.category-chip')),
    cameraScanModal: document.getElementById('cameraScanModal'),
    shortcutHelpModal: document.getElementById('shortcutHelpModal'),
    cameraAlertEl: document.getElementById('camera-scan-alert'),
    cameraSuccessEl: document.getElementById('camera-scan-success'),
    paymentMethodSelect: document.getElementById('payment_method'),
    scannerShortcutModifier: document.getElementById('scanner-shortcut-modifier')
  };

  const state = {
    placeholderImg: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="480" height="320" viewBox="0 0 480 320"><defs><linearGradient id="g" x1="0" x2="1" y1="0" y2="1"><stop stop-color="%23bae6fd"/><stop offset="1" stop-color="%23fed7aa"/></linearGradient></defs><rect width="480" height="320" fill="url(%23g)"/><g fill="%230f172a" opacity="0.7"><circle cx="170" cy="132" r="34"/><rect x="220" y="108" width="110" height="14" rx="7"/><rect x="220" y="134" width="82" height="12" rx="6"/><rect x="145" y="196" width="190" height="12" rx="6"/></g></svg>',
    cart: new Map(),
    selectedCartProductId: null,
    html5QrCode: null,
    lastScannedCode: '',
    lastScannedAt: 0,
    isMac: /Mac|iPhone|iPad|iPod/i.test(navigator.platform || ''),
    modKeyLabel: /Mac|iPhone|iPad|iPod/i.test(navigator.platform || '') ? 'Cmd' : 'Ctrl'
  };

  // ------------------------------------------------------------
  // Utilities.
  // ------------------------------------------------------------
  function isTabletLayout() {
    return window.innerWidth <= 992;
  }

  function formatIDR(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function formatDateTime(value) {
    if (!value) {
      return '-';
    }

    const date = new Date(String(value).replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) {
      return String(value);
    }

    return date.toLocaleString('id-ID', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  function getCsrfTokenName() {
    return dom.csrfNameInput ? String(dom.csrfNameInput.value || '') : '';
  }

  function getCsrfTokenHash() {
    return dom.csrfHashInput ? String(dom.csrfHashInput.value || '') : '';
  }

  function setCsrfTokenHash(hash) {
    if (dom.csrfHashInput && hash) {
      dom.csrfHashInput.value = String(hash);
    }
  }

  function getCurrentInvoiceNo() {
    return dom.invoiceHiddenInput ? String(dom.invoiceHiddenInput.value || '') : '';
  }

  function setCurrentInvoiceNo(invoiceNo) {
    if (dom.invoiceHiddenInput) {
      dom.invoiceHiddenInput.value = String(invoiceNo || '');
    }

    if (dom.currentInvoiceLabel) {
      dom.currentInvoiceLabel.textContent = String(invoiceNo || '-');
    }
  }

  function notify(message, icon, title) {
    if (typeof window.swal === 'function') {
      return window.swal({
        title: title || 'Perhatian',
        text: String(message || ''),
        icon: icon || 'warning',
        button: 'OK'
      });
    }

    window.alert(String(message || ''));
    return Promise.resolve();
  }

  function roundUpTo(value, step) {
    if (step <= 0) {
      return Math.max(0, value);
    }

    return Math.ceil(Math.max(0, value) / step) * step;
  }

  function getNumericString(value) {
    return String(value || '').replace(/\D/g, '');
  }

  function getAmountPaidNumber() {
    if (dom.amountPaidValueInput) {
      return Number(dom.amountPaidValueInput.value || 0);
    }

    return Number(getNumericString(dom.amountPaidInput ? dom.amountPaidInput.value : '') || 0);
  }

  function normalize(text) {
    return String(text || '').trim().toLowerCase();
  }

  function normalizeCategoryKey(categoryName) {
    return normalize(categoryName).replace(/\s+/g, '');
  }

  function hashString(value) {
    let hash = 0;
    const source = String(value || '');

    for (let i = 0; i < source.length; i += 1) {
      hash = ((hash << 5) - hash) + source.charCodeAt(i);
      hash |= 0;
    }

    return Math.abs(hash);
  }

  function resolveCategoryBadgeStyle(categoryName) {
    const presets = {
      makanan: { bg: '#c2410c', border: '#9a3412', fg: '#fff7ed' },
      minuman: { bg: '#0369a1', border: '#075985', fg: '#f0f9ff' },
      snack: { bg: '#b45309', border: '#92400e', fg: '#fffbeb' },
      sembako: { bg: '#365314', border: '#3f6212', fg: '#f7fee7' },
      atk: { bg: '#4c1d95', border: '#5b21b6', fg: '#f5f3ff' },
      jasa: { bg: '#0f766e', border: '#115e59', fg: '#f0fdfa' },
      umum: { bg: '#334155', border: '#1e293b', fg: '#f8fafc' }
    };

    const key = normalizeCategoryKey(categoryName);
    if (presets[key]) {
      return presets[key];
    }

    const hue = hashString(key || 'kategori') % 360;
    const saturation = 62;
    const lightness = 42;

    return {
      bg: 'hsl(' + hue + ', ' + saturation + '%, ' + lightness + '%)',
      border: 'hsl(' + hue + ', ' + saturation + '%, ' + Math.max(24, lightness - 14) + '%)',
      fg: '#f8fafc'
    };
  }

  function applyCategoryBadgeColors() {
    dom.cards.forEach(function(card) {
      const badge = card.querySelector('.product-category-badge');
      if (!badge) {
        return;
      }

      const category = String(card.dataset.category || badge.textContent || 'Umum');
      const style = resolveCategoryBadgeStyle(category);

      badge.style.setProperty('--pcb-bg', style.bg);
      badge.style.setProperty('--pcb-border', style.border);
      badge.style.setProperty('--pcb-fg', style.fg);
    });
  }

  function isTypingTarget(el) {
    if (!el) {
      return false;
    }

    const tag = (el.tagName || '').toLowerCase();
    return tag === 'input' || tag === 'textarea' || tag === 'select' || el.isContentEditable;
  }

  // ------------------------------------------------------------
  // Input and amount paid sync.
  // ------------------------------------------------------------
  function syncAmountPaidDisplay(showRaw) {
    if (!dom.amountPaidInput) {
      return;
    }

    const numericValue = getAmountPaidNumber();

    if (showRaw) {
      dom.amountPaidInput.value = numericValue > 0 ? String(numericValue) : '';
      return;
    }

    dom.amountPaidInput.value = formatIDR(numericValue);
  }

  function renderSummary() {
    const summary = computeSummary();

    if (dom.subtotalEl) {
      dom.subtotalEl.textContent = formatIDR(summary.subtotal);
    }
    if (dom.discountEl) {
      dom.discountEl.textContent = formatIDR(summary.discount);
    }
    if (dom.grandTotalEl) {
      dom.grandTotalEl.textContent = formatIDR(summary.grandTotal);
    }

    if (dom.cartToggleBadge) {
      dom.cartToggleBadge.textContent = String(state.cart.size);
    }

    if (dom.navPosItemCountEl) {
      dom.navPosItemCountEl.textContent = String(state.cart.size) + ' item';
    }

    if (dom.navPosGrandTotalEl) {
      dom.navPosGrandTotalEl.textContent = formatIDR(summary.grandTotal);
    }

    if (dom.modalGrandTotalEl) {
      dom.modalGrandTotalEl.textContent = formatIDR(summary.grandTotal);
    }

    if (dom.modalAmountDisplayEl) {
      dom.modalAmountDisplayEl.textContent = formatIDR(summary.amountPaid);
    }

    if (dom.modalChangeDisplayEl) {
      dom.modalChangeDisplayEl.textContent = formatIDR(summary.change);
    }

    updateQuickAmountPad(summary);
  }

  function setAmountPaidValue(value) {
    if (!dom.amountPaidValueInput) {
      return;
    }

    dom.amountPaidValueInput.value = String(Math.max(0, value));
    syncAmountPaidDisplay(dom.amountPaidInput && document.activeElement === dom.amountPaidInput);
    renderSummary();

    if (dom.amountPaidInput) {
      dom.amountPaidInput.focus();
      dom.amountPaidInput.select();
    }
  }

  function syncAmountPaidFromDisplay() {
    if (!dom.amountPaidInput || !dom.amountPaidValueInput) {
      return;
    }

    const numeric = Number(getNumericString(dom.amountPaidInput.value) || 0);
    dom.amountPaidValueInput.value = String(numeric);
    dom.amountPaidInput.value = numeric > 0 ? String(numeric) : '';
    renderSummary();
  }

  function appendAmountPaidDigit(digit) {
    const numeric = String(getAmountPaidNumber() || 0);
    const next = numeric === '0' ? String(digit) : numeric + String(digit);
    setAmountPaidValue(Number(next || 0));
  }

  function deleteAmountPaidDigit() {
    const numeric = String(getAmountPaidNumber() || 0);
    const next = numeric.slice(0, -1);
    setAmountPaidValue(Number(next || 0));
  }

  function clearAmountPaidValue() {
    setAmountPaidValue(0);
  }

  function computeSummary() {
    let subtotal = 0;
    state.cart.forEach(function(item) {
      subtotal += item.qty * item.price;
    });

    const discount = Math.max(0, Number(dom.discountInput.value || 0));
    const grandTotal = Math.max(0, subtotal - discount);
    const amountPaid = Math.max(0, getAmountPaidNumber());
    const change = Math.max(0, amountPaid - grandTotal);

    return {
      subtotal,
      discount,
      grandTotal,
      amountPaid,
      change
    };
  }

  function openPaymentModal() {
    if (state.cart.size === 0) {
      notify('Silakan pilih minimal satu produk sebelum memproses pembayaran.', 'warning', 'Cart Kosong');
      return;
    }

    const summary = computeSummary();
    if (getAmountPaidNumber() === 0 && dom.paymentMethodSelect && dom.paymentMethodSelect.value === 'cash') {
      if (dom.amountPaidValueInput) {
        dom.amountPaidValueInput.value = String(summary.grandTotal);
      }

      syncAmountPaidDisplay(false);
      renderSummary();
    }

    if (dom.paymentModal && typeof $ !== 'undefined') {
      $(dom.paymentModal).modal('show');
    }
  }

  // ------------------------------------------------------------
  // Product catalog.
  // ------------------------------------------------------------
  function focusSearchField(selectText) {
    if (!dom.searchInput) {
      return;
    }

    dom.searchInput.focus();
    if (selectText) {
      dom.searchInput.select();
    }
  }

  function focusCartMode() {
    if (dom.searchInput) {
      dom.searchInput.blur();
    }

    if (dom.cartTable) {
      dom.cartTable.focus({
        preventScroll: true
      });
    }
  }

  function applyImageFallback() {
    document.querySelectorAll('.js-product-image').forEach(function(img) {
      if (!img.getAttribute('src')) {
        img.src = state.placeholderImg;
      }

      img.addEventListener('error', function() {
        img.src = state.placeholderImg;
      });
    });
  }

  function filterCards() {
    const query = normalize(dom.searchInput ? dom.searchInput.value : '');
    const cat = normalize(dom.categoryFilter ? dom.categoryFilter.value : '');
    let visibleCount = 0;

    dom.cards.forEach(function(card) {
      const name = normalize(card.dataset.name);
      const sku = normalize(card.dataset.sku);
      const category = normalize(card.dataset.category);

      const matchQuery = !query || name.includes(query) || sku.includes(query);
      const matchCategory = !cat || category === cat;
      const visible = matchQuery && matchCategory;

      card.classList.toggle('d-none', !visible);
      if (visible) {
        visibleCount += 1;
      }
    });

    if (dom.emptyProducts) {
      dom.emptyProducts.classList.toggle('d-none', visibleCount > 0);
    }
  }

  function syncCategoryChips() {
    const current = dom.categoryFilter ? String(dom.categoryFilter.value || '') : '';
    dom.categoryChips.forEach(function(chip) {
      chip.classList.toggle('active', String(chip.dataset.category || '') === current);
    });
  }

  function getVisibleCards() {
    return dom.cards.filter(function(card) {
      return !card.classList.contains('d-none');
    });
  }

  function getFirstVisibleCard() {
    const visibleCards = getVisibleCards();
    return visibleCards.length > 0 ? visibleCards[0] : null;
  }

  function findCardByKeyword(keyword) {
    const clean = normalize(keyword);
    if (!clean) {
      return null;
    }

    let found = dom.cards.find(function(card) {
      return normalize(card.dataset.sku) === clean;
    });
    if (found) {
      return found;
    }

    found = dom.cards.find(function(card) {
      return normalize(card.dataset.name) === clean;
    });
    if (found) {
      return found;
    }

    return dom.cards.find(function(card) {
      return normalize(card.dataset.sku).includes(clean) || normalize(card.dataset.name).includes(clean);
    }) || null;
  }

  function addProductFromSearch() {
    const query = dom.searchInput ? dom.searchInput.value : '';
    const matched = findCardByKeyword(query);
    const candidate = matched || getFirstVisibleCard();

    if (!candidate) {
      return;
    }

    const stock = Number(candidate.dataset.stock || 0);
    if (stock <= 0) {
      return;
    }

    addToCart({
      id: Number(candidate.dataset.productId),
      name: candidate.dataset.name,
      price: Number(candidate.dataset.price || 0),
      stock: stock
    });

    if (dom.searchInput) {
      dom.searchInput.value = '';
      filterCards();
    }

    focusCartMode();

    if (isTabletLayout()) {
      openCartDrawer();
    }
  }

  // ------------------------------------------------------------
  // Cart and drawer.
  // ------------------------------------------------------------
  function openCartDrawer() {
    if (!isTabletLayout() || !dom.cartColumn || !dom.cartDrawerBackdrop) {
      return;
    }

    dom.cartColumn.classList.add('is-open');
    dom.cartDrawerBackdrop.classList.add('is-open');
    document.body.classList.add('pos-cart-drawer-open');
  }

  function closeCartDrawer() {
    if (!dom.cartColumn || !dom.cartDrawerBackdrop) {
      return;
    }

    dom.cartColumn.classList.remove('is-open');
    dom.cartDrawerBackdrop.classList.remove('is-open');
    document.body.classList.remove('pos-cart-drawer-open');
  }

  function syncResponsiveState() {
    if (!isTabletLayout()) {
      closeCartDrawer();
    }
  }

  function addToCart(item) {
    const existing = state.cart.get(item.id);
    if (existing) {
      if (existing.qty < existing.stock) {
        existing.qty += 1;
      }
      state.selectedCartProductId = item.id;
      renderCart();
      return;
    }

    state.cart.set(item.id, {
      id: item.id,
      name: item.name,
      price: item.price,
      stock: item.stock,
      qty: 1
    });

    state.selectedCartProductId = item.id;
    renderCart();
  }

  function changeQty(productId, delta) {
    const item = state.cart.get(productId);
    if (!item) {
      return;
    }

    const next = item.qty + delta;
    if (next <= 0) {
      state.cart.delete(productId);
      if (state.selectedCartProductId === productId) {
        state.selectedCartProductId = null;
      }
      renderCart();
      return;
    }

    item.qty = Math.min(next, item.stock);
    renderCart();
  }

  function removeItem(productId) {
    state.cart.delete(productId);
    if (state.selectedCartProductId === productId) {
      state.selectedCartProductId = null;
    }
    renderCart();
  }

  function resetCart() {
    if (state.cart.size === 0) {
      notify('Cart sudah kosong.', 'info', 'Info Cart');
      return;
    }

    const doReset = function() {
      state.cart.clear();
      state.selectedCartProductId = null;

      if (dom.discountInput) {
        dom.discountInput.value = '0';
      }

      if (dom.amountPaidValueInput) {
        dom.amountPaidValueInput.value = '0';
      }

      syncAmountPaidDisplay(false);
      renderCart();
      notify('Data cart berhasil direset.', 'success', 'Berhasil');
      focusSearchField(true);
    };

    if (typeof window.swal === 'function') {
      window.swal({
        title: 'Reset Cart?',
        text: 'Semua item, diskon, dan input bayar akan dikosongkan.',
        icon: 'warning',
        buttons: {
          cancel: 'Batal',
          confirm: {
            text: 'Ya, Reset',
            value: true,
            visible: true
          }
        },
        dangerMode: true
      }).then(function(confirmed) {
        if (confirmed) {
          doReset();
        }
      });
      return;
    }

    if (window.confirm('Semua item, diskon, dan input bayar akan dikosongkan. Lanjut reset?')) {
      doReset();
    }
  }

  function resetTransactionState(nextInvoiceNo) {
    state.cart.clear();
    state.selectedCartProductId = null;

    if (dom.customerNameInput) {
      dom.customerNameInput.value = '';
    }

    if (dom.discountInput) {
      dom.discountInput.value = '0';
    }

    if (dom.paymentMethodSelect) {
      dom.paymentMethodSelect.value = 'cash';
    }

    if (dom.amountPaidValueInput) {
      dom.amountPaidValueInput.value = '0';
    }

    if (nextInvoiceNo) {
      setCurrentInvoiceNo(nextInvoiceNo);
    }

    syncAmountPaidDisplay(false);
    renderCart();
    focusSearchField(true);
  }

  function buildPendingRequestFormData() {
    const summary = computeSummary();
    const formData = new FormData();
    const csrfTokenName = getCsrfTokenName();
    const csrfTokenHash = getCsrfTokenHash();

    if (csrfTokenName && csrfTokenHash) {
      formData.append(csrfTokenName, csrfTokenHash);
    }

    formData.append('invoice_no', getCurrentInvoiceNo());
    formData.append('customer_name', dom.customerNameInput ? String(dom.customerNameInput.value || '') : '');
    formData.append('payment_method', dom.paymentMethodSelect ? String(dom.paymentMethodSelect.value || 'cash') : 'cash');
    formData.append('discount_amount', dom.discountInput ? String(dom.discountInput.value || '0') : '0');
    formData.append('amount_paid', dom.amountPaidValueInput ? String(dom.amountPaidValueInput.value || '0') : '0');
    formData.append('cart_payload', JSON.stringify(Array.from(state.cart.values())));
    formData.append('subtotal_amount', String(summary.subtotal));
    formData.append('grand_total', String(summary.grandTotal));

    return formData;
  }

  function getPendingListUrl() {
    return dom.checkoutForm ? String(dom.checkoutForm.dataset.pendingListUrl || '') : '';
  }

  function getPendingSaveUrl() {
    return dom.checkoutForm ? String(dom.checkoutForm.dataset.pendingSaveUrl || '') : '';
  }

  function getPendingRestoreUrl(id) {
    const baseUrl = dom.checkoutForm ? String(dom.checkoutForm.dataset.pendingRestoreBaseUrl || '') : '';
    return baseUrl ? baseUrl + '/' + String(id) + '/restore' : '';
  }

  function getPendingDeleteUrl(id) {
    const baseUrl = dom.checkoutForm ? String(dom.checkoutForm.dataset.pendingDeleteBaseUrl || '') : '';
    return baseUrl ? baseUrl + '/' + String(id) + '/delete' : '';
  }

  function renderPendingTransactions(items) {
    if (!dom.pendingTransactionsList || !dom.pendingTransactionsEmpty) {
      return;
    }

    const list = Array.isArray(items) ? items : [];
    dom.pendingTransactionsList.innerHTML = '';
    dom.pendingTransactionsEmpty.classList.toggle('d-none', list.length > 0);

    // Update modal count badge
    var modalCountEl = document.getElementById('pending-modal-count-text');
    if (modalCountEl) {
      modalCountEl.textContent = list.length + ' transaksi';
    }

    list.forEach(function(item) {
      var paymentLabel = (item.payment_method || 'cash') === 'transfer' ? 'Transfer' : 'Tunai';
      var element = document.createElement('div');
      element.className = 'pending-item';
      element.innerHTML =
        '<div class="pending-item-top">' +
        '  <div style="min-width:0;flex:1;">' +
        '    <p class="pending-item-title">' + escapeHtml(item.invoice_no) + '</p>' +
        '    <p class="pending-item-customer"><i class="fas fa-user fa-xs mr-1"></i>' + escapeHtml(item.customer_name || 'Pelanggan umum') + '</p>' +
        '  </div>' +
        '  <small class="text-muted text-nowrap" style="font-size:11px;padding-top:2px;"><i class="fas fa-clock fa-xs mr-1"></i>' + escapeHtml(formatDateTime(item.updated_at)) + '</small>' +
        '</div>' +
        '<div class="pending-item-meta">' +
        '  <div class="pending-item-meta-cell">' +
        '    <p class="pending-item-meta-label">Item</p>' +
        '    <p class="pending-item-meta-value">' + escapeHtml(String(item.item_count || 0)) + ' produk</p>' +
        '  </div>' +
        '  <div class="pending-item-meta-cell">' +
        '    <p class="pending-item-meta-label">Grand Total</p>' +
        '    <p class="pending-item-meta-value text-teal">' + escapeHtml(formatIDR(item.grand_total || 0)) + '</p>' +
        '  </div>' +
        '  <div class="pending-item-meta-cell">' +
        '    <p class="pending-item-meta-label">Metode</p>' +
        '    <p class="pending-item-meta-value">' + escapeHtml(paymentLabel) + '</p>' +
        '  </div>' +
        '</div>' +
        '<div class="pending-item-actions">' +
        '  <button type="button" class="btn btn-restore js-restore-pending" data-id="' + String(item.id) + '"><i class="fas fa-play mr-1"></i> Lanjutkan</button>' +
        '  <button type="button" class="btn btn-delete-pending js-delete-pending" data-id="' + String(item.id) + '"><i class="fas fa-trash-alt mr-1"></i> Hapus</button>' +
        '</div>';

      dom.pendingTransactionsList.appendChild(element);
    });

    if (dom.pendingTransactionsCountEl) {
      dom.pendingTransactionsCountEl.textContent = String(list.length);
    }
  }

  function fetchPendingTransactions() {
    const url = getPendingListUrl();
    if (!url) {
      return Promise.resolve([]);
    }

    return window.fetch(url, {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(function(response) {
      return response.json();
    }).then(function(payload) {
      const items = payload && Array.isArray(payload.data) ? payload.data : [];
      renderPendingTransactions(items);
      return items;
    }).catch(function() {
      renderPendingTransactions([]);
      return [];
    });
  }

  function openPendingTransactionsModal() {
    if (!dom.pendingTransactionsModal || typeof $ === 'undefined') {
      return Promise.resolve([]);
    }

    return fetchPendingTransactions().then(function(items) {
      $(dom.pendingTransactionsModal).modal('show');
      return items;
    });
  }

  function savePendingTransaction() {
    const url = getPendingSaveUrl();
    if (!url) {
      return Promise.reject(new Error('Endpoint simpan transaksi tertunda tidak tersedia.'));
    }

    return window.fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: buildPendingRequestFormData()
    }).then(function(response) {
      return response.json().then(function(payload) {
        if (!response.ok || !payload.success) {
          throw new Error(payload.message || 'Gagal menyimpan transaksi tertunda.');
        }

        setCsrfTokenHash(payload.csrfHash || '');
        resetTransactionState(payload.nextInvoiceNo || '');
        fetchPendingTransactions();
        return payload;
      });
    });
  }

  function restorePendingTransaction(id) {
    const url = getPendingRestoreUrl(id);
    const formData = new FormData();
    const csrfTokenName = getCsrfTokenName();
    const csrfTokenHash = getCsrfTokenHash();

    if (csrfTokenName && csrfTokenHash) {
      formData.append(csrfTokenName, csrfTokenHash);
    }

    return window.fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    }).then(function(response) {
      return response.json().then(function(payload) {
        if (!response.ok || !payload.success || !payload.data) {
          throw new Error(payload.message || 'Gagal memuat transaksi tertunda.');
        }

        setCsrfTokenHash(payload.csrfHash || '');

        resetTransactionState('');
        state.cart.clear();
        payload.data.cart_items.forEach(function(item) {
          state.cart.set(Number(item.product_id || item.id), {
            id: Number(item.product_id || item.id),
            name: String(item.product_name || item.name || 'Produk'),
            price: Number(item.price || 0),
            stock: Number(item.stock || 0),
            qty: Number(item.qty || 0)
          });
        });

        if (dom.customerNameInput) {
          dom.customerNameInput.value = String(payload.data.customer_name || '');
        }
        if (dom.discountInput) {
          dom.discountInput.value = String(payload.data.discount_amount || 0);
        }
        if (dom.paymentMethodSelect) {
          dom.paymentMethodSelect.value = String(payload.data.payment_method || 'cash');
        }
        if (dom.amountPaidValueInput) {
          dom.amountPaidValueInput.value = String(payload.data.amount_paid || 0);
        }
        setCurrentInvoiceNo(payload.data.invoice_no || payload.nextInvoiceNo || '');
        renderCart();
        syncAmountPaidDisplay(false);
        fetchPendingTransactions();

        if (dom.pendingTransactionsModal && typeof $ !== 'undefined') {
          $(dom.pendingTransactionsModal).modal('hide');
        }

        // Tampilkan peringatan stok jika ada item yang dikurangi/dihapus
        if (payload.stockWarnings && payload.stockWarnings.length > 0) {
          var warningLines = payload.stockWarnings.map(function(w) {
            if (w.available === 0) {
              return '<li><strong>' + escapeHtml(w.name) + '</strong> — stok habis (dihapus dari keranjang)</li>';
            }
            return '<li><strong>' + escapeHtml(w.name) + '</strong> — diminta: ' + w.requested + ', tersedia: ' + w.available + ' (qty disesuaikan)</li>';
          }).join('');
          window.swal({
            title: 'Peringatan Stok',
            content: (function() {
              var el = document.createElement('div');
              el.style.textAlign = 'left';
              el.innerHTML = '<p style="margin-bottom:8px">Beberapa item tidak mencukupi stok:</p><ul style="padding-left:18px;margin:0">' + warningLines + '</ul>';
              return el;
            })(),
            icon: 'warning',
            button: 'Mengerti'
          });
        }

        return payload;
      });
    });
  }

  function deletePendingTransaction(id) {
    const url = getPendingDeleteUrl(id);
    const formData = new FormData();
    const csrfTokenName = getCsrfTokenName();
    const csrfTokenHash = getCsrfTokenHash();

    if (csrfTokenName && csrfTokenHash) {
      formData.append(csrfTokenName, csrfTokenHash);
    }

    return window.fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    }).then(function(response) {
      return response.json().then(function(payload) {
        if (!response.ok || !payload.success) {
          throw new Error(payload.message || 'Gagal menghapus transaksi tertunda.');
        }

        setCsrfTokenHash(payload.csrfHash || '');
        fetchPendingTransactions();
        return payload;
      });
    });
  }

  function selectCartProduct(productId) {
    if (!state.cart.has(productId)) {
      return;
    }

    state.selectedCartProductId = productId;
    renderCart();
  }

  function moveCartSelection(step) {
    const ids = Array.from(state.cart.keys());
    if (ids.length === 0) {
      state.selectedCartProductId = null;
      return;
    }

    if (state.selectedCartProductId === null || !state.cart.has(state.selectedCartProductId)) {
      state.selectedCartProductId = ids[0];
      renderCart();
      return;
    }

    const currentIndex = ids.indexOf(state.selectedCartProductId);
    const nextIndex = (currentIndex + step + ids.length) % ids.length;
    state.selectedCartProductId = ids[nextIndex];
    renderCart();
  }

  function buildHiddenInputs() {
    dom.hiddenItemsWrap.innerHTML = '';

    state.cart.forEach(function(item) {
      const productInput = document.createElement('input');
      productInput.type = 'hidden';
      productInput.name = 'product_id[]';
      productInput.value = String(item.id);

      const qtyInput = document.createElement('input');
      qtyInput.type = 'hidden';
      qtyInput.name = 'qty[]';
      qtyInput.value = String(item.qty);

      dom.hiddenItemsWrap.appendChild(productInput);
      dom.hiddenItemsWrap.appendChild(qtyInput);
    });
  }

  function renderCart() {
    dom.cartBody.innerHTML = '';

    if (state.cart.size === 0) {
      dom.emptyCart.classList.remove('d-none');
      state.selectedCartProductId = null;
      buildHiddenInputs();
      renderSummary();
      return;
    }

    dom.emptyCart.classList.add('d-none');

    if (state.selectedCartProductId === null || !state.cart.has(state.selectedCartProductId)) {
      state.selectedCartProductId = Array.from(state.cart.keys())[0];
    }

    state.cart.forEach(function(item) {
      const tr = document.createElement('tr');
      tr.className = 'js-cart-row' + (item.id === state.selectedCartProductId ? ' cart-row-active' : '');
      tr.setAttribute('data-id', String(item.id));
      tr.innerHTML = '' +
        '<td>' +
        '<div class="font-weight-600">' + item.name + '</div>' +
        '<small class="text-muted">' + formatIDR(item.price) + ' / item</small>' +
        '</td>' +
        '<td class="text-center">' +
        '<div class="qty-control">' +
        '<button type="button" class="js-qty-minus" data-id="' + item.id + '">-</button>' +
        '<span>' + item.qty + '</span>' +
        '<button type="button" class="js-qty-plus" data-id="' + item.id + '">+</button>' +
        '</div>' +
        '</td>' +
        '<td class="text-right font-weight-600">' + formatIDR(item.qty * item.price) + '</td>' +
        '<td class="text-right">' +
        '<button type="button" class="btn btn-sm btn-outline-danger js-remove-item" data-id="' + item.id + '"><i class="fas fa-trash"></i></button>' +
        '</td>';

      dom.cartBody.appendChild(tr);
    });

    buildHiddenInputs();
    renderSummary();
  }

  // ------------------------------------------------------------
  // Quick amount / keypad / suggestion rendering.
  // ------------------------------------------------------------
  function updateQuickAmountPad(summary) {
    if (!dom.quickAmountPad || !dom.paymentMethodSelect) {
      return;
    }

    const currentSummary = summary || computeSummary();
    const isCashPayment = dom.paymentMethodSelect.value === 'cash';
    const hasGrandTotal = currentSummary.grandTotal > 0;

    dom.quickAmountPad.classList.toggle('d-none', !isCashPayment);

    dom.quickAmountButtons.forEach(function(button) {
      const mode = String(button.dataset.mode || '');
      const valueEl = button.querySelector('.quick-amount-value');
      let value = 0;

      if (mode === 'exact') {
        value = currentSummary.grandTotal;
      } else if (mode === 'round') {
        value = roundUpTo(currentSummary.grandTotal, Number(button.dataset.step || 0));
      } else if (mode === 'fixed') {
        value = Number(button.dataset.amount || 0);
      }

      button.disabled = !isCashPayment || !hasGrandTotal;

      if (!valueEl) {
        return;
      }

      if (!hasGrandTotal) {
        valueEl.textContent = 'Belum ada total transaksi';
        return;
      }

      valueEl.textContent = formatIDR(value);
    });

  }

  // ------------------------------------------------------------
  // Scanner handlers.
  // ------------------------------------------------------------
  function playScanBeep() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) {
        return;
      }

      const ctx = new AudioCtx();
      const oscillator = ctx.createOscillator();
      const gainNode = ctx.createGain();

      oscillator.type = 'sine';
      oscillator.frequency.setValueAtTime(880, ctx.currentTime);
      gainNode.gain.setValueAtTime(0.001, ctx.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.12, ctx.currentTime + 0.01);
      gainNode.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);

      oscillator.connect(gainNode);
      gainNode.connect(ctx.destination);
      oscillator.start(ctx.currentTime);
      oscillator.stop(ctx.currentTime + 0.12);

      oscillator.onended = function() {
        ctx.close().catch(function() {});
      };
    } catch (e) {}
  }

  function showCameraAlert(message) {
    dom.cameraAlertEl.textContent = message;
    dom.cameraAlertEl.classList.remove('d-none');
    dom.cameraSuccessEl.classList.add('d-none');
  }

  function showCameraSuccess(message) {
    dom.cameraSuccessEl.textContent = message;
    dom.cameraSuccessEl.classList.remove('d-none');
    dom.cameraAlertEl.classList.add('d-none');
  }

  function stopCamera() {
    if (!state.html5QrCode) {
      return;
    }

    state.html5QrCode.stop().catch(function() {}).finally(function() {
      state.html5QrCode.clear();
      state.html5QrCode = null;
    });
  }

  function startCamera() {
    if (typeof Html5Qrcode === 'undefined') {
      showCameraAlert('Library scan tidak tersedia. Periksa koneksi internet.');
      return;
    }

    dom.cameraAlertEl.classList.add('d-none');
    dom.cameraSuccessEl.classList.add('d-none');
    state.lastScannedCode = '';

    state.html5QrCode = new Html5Qrcode('camera-reader');

    Html5Qrcode.getCameras().then(function(cameras) {
      if (!cameras || cameras.length === 0) {
        showCameraAlert('Kamera tidak ditemukan pada perangkat ini.');
        return;
      }

      let selectedCamera = cameras[cameras.length - 1];
      cameras.forEach(function(cam) {
        const label = normalize(cam.label || '');
        if (label.includes('back') || label.includes('rear') || label.includes('belakang') || label.includes('environment')) {
          selectedCamera = cam;
        }
      });

      state.html5QrCode.start(
        selectedCamera.id,
        {
          fps: 10,
          qrbox: {
            width: 260,
            height: 100
          }
        },
        function onScanSuccess(decodedText) {
          const now = Date.now();
          if (decodedText === state.lastScannedCode && (now - state.lastScannedAt) < 3000) {
            return;
          }

          state.lastScannedCode = decodedText;
          state.lastScannedAt = now;

          const matchedCard = findCardByKeyword(decodedText);
          if (!matchedCard) {
            showCameraAlert('Produk tidak ditemukan: ' + decodedText);
            return;
          }

          const stock = Number(matchedCard.dataset.stock || 0);
          if (stock <= 0) {
            showCameraAlert('Stok habis untuk produk ini.');
            return;
          }

          addToCart({
            id: Number(matchedCard.dataset.productId),
            name: matchedCard.dataset.name,
            price: Number(matchedCard.dataset.price || 0),
            stock: stock
          });

          if (isTabletLayout()) {
            openCartDrawer();
          }

          playScanBeep();
          showCameraSuccess('Ditambahkan: ' + matchedCard.dataset.name);
        },
        function onScanError() {}
      ).catch(function(err) {
        showCameraAlert('Gagal mengakses kamera: ' + err);
      });
    }).catch(function(err) {
      showCameraAlert('Izin kamera ditolak atau tidak tersedia: ' + err);
    });
  }

  // Export to namespace.
  shared.dom = dom;
  shared.state = state;
  shared.actions = {
    isTabletLayout,
    formatIDR,
    notify,
    roundUpTo,
    getNumericString,
    getAmountPaidNumber,
    getCurrentInvoiceNo,
    applyCategoryBadgeColors,
    normalize,
    isTypingTarget,
    syncAmountPaidDisplay,
    setAmountPaidValue,
    syncAmountPaidFromDisplay,
    appendAmountPaidDigit,
    deleteAmountPaidDigit,
    clearAmountPaidValue,
    computeSummary,
    renderSummary,
    openPaymentModal,
    focusSearchField,
    focusCartMode,
    applyImageFallback,
    filterCards,
    syncCategoryChips,
    getVisibleCards,
    getFirstVisibleCard,
    findCardByKeyword,
    addProductFromSearch,
    openCartDrawer,
    closeCartDrawer,
    syncResponsiveState,
    addToCart,
    changeQty,
    removeItem,
    resetCart,
    selectCartProduct,
    moveCartSelection,
    buildHiddenInputs,
    renderCart,
    setCurrentInvoiceNo,
    fetchPendingTransactions,
    openPendingTransactionsModal,
    savePendingTransaction,
    restorePendingTransaction,
    deletePendingTransaction,
    updateQuickAmountPad,
    playScanBeep,
    showCameraAlert,
    showCameraSuccess,
    stopCamera,
    startCamera
  };

  window.POS = POS;
})(window);
