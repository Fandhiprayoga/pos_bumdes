(function(window) {
  const POS = window.POS || {};
  const shared = POS.shared || {};
  const dom = shared.dom || {};
  const state = shared.state || {};
  const actions = shared.actions || {};

  // ------------------------------------------------------------
  // Event bindings and initialization flow.
  // ------------------------------------------------------------
  function bindCatalogEvents() {
    dom.cards.forEach(function(card) {
      const addButton = card.querySelector('.js-add-to-cart');
      addButton.addEventListener('click', function() {
        const stock = Number(card.dataset.stock || 0);
        if (stock <= 0) {
          return;
        }

        actions.addToCart({
          id: Number(card.dataset.productId),
          name: card.dataset.name,
          price: Number(card.dataset.price || 0),
          stock: stock
        });

        if (actions.isTabletLayout()) {
          actions.openCartDrawer();
        }
      });
    });

    dom.cartBody.addEventListener('click', function(event) {
      const row = event.target.closest('.js-cart-row');
      if (row && row.dataset.id) {
        actions.selectCartProduct(Number(row.dataset.id));
      }

      const minusBtn = event.target.closest('.js-qty-minus');
      if (minusBtn) {
        actions.changeQty(Number(minusBtn.dataset.id), -1);
        return;
      }

      const plusBtn = event.target.closest('.js-qty-plus');
      if (plusBtn) {
        actions.changeQty(Number(plusBtn.dataset.id), 1);
        return;
      }

      const removeBtn = event.target.closest('.js-remove-item');
      if (removeBtn) {
        actions.removeItem(Number(removeBtn.dataset.id));
      }
    });

    if (dom.searchInput) {
      dom.searchInput.addEventListener('input', actions.filterCards);
      dom.searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          actions.addProductFromSearch();
        }
      });
    }

    if (dom.categoryFilter) {
      dom.categoryFilter.addEventListener('change', actions.filterCards);
      dom.categoryFilter.addEventListener('change', actions.syncCategoryChips);
    }

    dom.categoryChips.forEach(function(chip) {
      chip.addEventListener('click', function() {
        if (!dom.categoryFilter) {
          return;
        }

        dom.categoryFilter.value = String(chip.dataset.category || '');
        actions.syncCategoryChips();
        actions.filterCards();
      });
    });
  }

  function bindPaymentEvents() {
    let isSubmittingCheckout = false;

    dom.discountInput.addEventListener('input', actions.renderSummary);

    if (dom.amountPaidInput) {
      dom.amountPaidInput.addEventListener('focus', function() {
        actions.syncAmountPaidDisplay(true);
        dom.amountPaidInput.select();
      });

      dom.amountPaidInput.addEventListener('blur', function() {
        actions.syncAmountPaidDisplay(false);
      });

      dom.amountPaidInput.addEventListener('input', actions.syncAmountPaidFromDisplay);
    }

    dom.quickAmountButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        const summary = actions.computeSummary();
        const mode = String(button.dataset.mode || '');
        let value = 0;

        if (mode === 'exact') {
          value = summary.grandTotal;
        } else if (mode === 'round') {
          value = actions.roundUpTo(summary.grandTotal, Number(button.dataset.step || 0));
        } else if (mode === 'fixed') {
          value = Number(button.dataset.amount || 0);
        }

        actions.setAmountPaidValue(value);
      });
    });

    dom.keypadDigitButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        actions.appendAmountPaidDigit(String(button.dataset.digit || ''));
      });
    });

    dom.keypadActionButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        const action = String(button.dataset.action || '');

        if (action === 'clear') {
          actions.clearAmountPaidValue();
          return;
        }

        if (action === 'delete') {
          actions.deleteAmountPaidDigit();
        }
      });
    });

    if (dom.paymentMethodSelect) {
      dom.paymentMethodSelect.addEventListener('change', function() {
        if (dom.paymentMethodSelect.value === 'cash' && actions.getAmountPaidNumber() === 0) {
          if (dom.amountPaidValueInput) {
            dom.amountPaidValueInput.value = String(actions.computeSummary().grandTotal);
          }
          actions.syncAmountPaidDisplay(dom.amountPaidInput && document.activeElement === dom.amountPaidInput);
          actions.renderSummary();
        }

        actions.updateQuickAmountPad();
      });
    }

    if (dom.btnPay) {
      dom.btnPay.addEventListener('click', actions.openPaymentModal);
    }

    if (dom.btnSavePending) {
      dom.btnSavePending.addEventListener('click', function() {
        if (state.cart.size === 0) {
          actions.notify('Silakan pilih minimal satu produk sebelum menyimpan transaksi tertunda.', 'warning', 'Cart Kosong');
          return;
        }

        const summary = actions.computeSummary();
        const confirmSave = function() {
          actions.savePendingTransaction().then(function(payload) {
            actions.notify(payload.message || 'Transaksi tertunda berhasil disimpan.', 'success', 'Berhasil');
          }).catch(function(error) {
            actions.notify(error.message || 'Gagal menyimpan transaksi tertunda.', 'error', 'Gagal');
          });
        };

        if (typeof window.swal === 'function') {
          window.swal({
            title: 'Simpan transaksi tertunda?',
            text: 'Invoice ' + actions.getCurrentInvoiceNo() + ' dengan total ' + actions.formatIDR(summary.grandTotal) + ' akan disimpan dan cart saat ini dikosongkan.',
            icon: 'warning',
            buttons: {
              cancel: {
                text: 'Batal',
                visible: true
              },
              confirm: {
                text: 'Ya, simpan',
                visible: true
              }
            }
          }).then(function(confirmed) {
            if (confirmed) {
              confirmSave();
            }
          });
          return;
        }

        if (window.confirm('Simpan transaksi tertunda dan kosongkan cart saat ini?')) {
          confirmSave();
        }
      });
    }

    if (dom.btnResetCart) {
      dom.btnResetCart.addEventListener('click', actions.resetCart);
    }

    if (dom.btnPendingTransactions) {
      dom.btnPendingTransactions.addEventListener('click', function() {
        actions.openPendingTransactionsModal();
      });
    }

    dom.checkoutForm.addEventListener('submit', function(event) {
      const summary = actions.computeSummary();
      if (state.cart.size === 0) {
        event.preventDefault();
        actions.notify('Silakan pilih minimal satu produk sebelum menyimpan transaksi.', 'warning', 'Cart Kosong');
        return;
      }

      if (summary.amountPaid < summary.grandTotal) {
        event.preventDefault();
        actions.notify('Jumlah bayar kurang dari grand total transaksi.', 'error', 'Pembayaran Belum Cukup').then(function() {
          if (dom.amountPaidInput) {
            dom.amountPaidInput.focus();
            dom.amountPaidInput.select();
          }
        });
        return;
      }

      if (isSubmittingCheckout) {
        return;
      }

      event.preventDefault();

      const submitter = event.submitter || document.activeElement;
      const checkoutAction = submitter && submitter.name === 'checkout_action'
        ? String(submitter.value || 'save')
        : 'save';

      const hiddenActionName = 'checkout_action';
      let hiddenActionInput = dom.checkoutForm.querySelector('input[type="hidden"][name="' + hiddenActionName + '"]');
      if (!hiddenActionInput) {
        hiddenActionInput = document.createElement('input');
        hiddenActionInput.type = 'hidden';
        hiddenActionInput.name = hiddenActionName;
        dom.checkoutForm.appendChild(hiddenActionInput);
      }
      hiddenActionInput.value = checkoutAction;

      const isPrintAction = checkoutAction === 'save_print';
      const confirmTitle = isPrintAction ? 'Simpan dan cetak nota?' : 'Simpan transaksi?';
      const confirmButtonText = isPrintAction ? 'Ya, simpan & cetak' : 'Ya, simpan';
      const invoiceInput = dom.checkoutForm.querySelector('input[name="invoice_no"]');
      const invoiceNo = invoiceInput ? String(invoiceInput.value || '-') : '-';
      const paymentMethodValue = dom.paymentMethodSelect ? String(dom.paymentMethodSelect.value || '') : '';
      const paymentMethodLabel = paymentMethodValue === 'transfer'
        ? 'Transfer'
        : paymentMethodValue === 'cash'
          ? 'Tunai'
          : '-';
      const grandTotalLabel = actions.formatIDR(summary.grandTotal);

      const buildConfirmContent = function() {
        const wrapper = document.createElement('div');
        wrapper.style.textAlign = 'left';
        wrapper.style.fontSize = '14px';

        const intro = document.createElement('div');
        intro.textContent = isPrintAction
          ? 'Transaksi akan disimpan lalu halaman nota cetak akan dibuka.'
          : 'Pastikan data transaksi dan pembayaran sudah benar.';
        intro.style.marginBottom = '12px';
        intro.style.color = '#4b5563';
        wrapper.appendChild(intro);

        const summaryBox = document.createElement('div');
        summaryBox.style.border = '1px solid #e5e7eb';
        summaryBox.style.borderRadius = '8px';
        summaryBox.style.padding = '10px 12px';
        summaryBox.style.background = '#f8fafc';

        [
          ['Invoice', invoiceNo],
          ['Metode Bayar', paymentMethodLabel],
          ['Grand Total', grandTotalLabel]
        ].forEach(function(entry, index) {
          const row = document.createElement('div');
          row.style.display = 'flex';
          row.style.justifyContent = 'space-between';
          row.style.alignItems = 'center';
          row.style.gap = '12px';
          if (index > 0) {
            row.style.marginTop = '8px';
          }

          const label = document.createElement('span');
          label.textContent = entry[0];
          label.style.color = '#6b7280';

          const value = document.createElement('strong');
          value.textContent = entry[1];
          value.style.color = '#111827';
          value.style.textAlign = 'right';

          row.appendChild(label);
          row.appendChild(value);
          summaryBox.appendChild(row);
        });

        wrapper.appendChild(summaryBox);
        return wrapper;
      };

      if (typeof window.swal === 'function') {
        window.swal({
          title: confirmTitle,
          icon: 'warning',
          content: buildConfirmContent(),
          buttons: {
            cancel: {
              text: 'Batal',
              visible: true
            },
            confirm: {
              text: confirmButtonText,
              visible: true
            }
          },
          dangerMode: false
        }).then(function(confirmed) {
          if (!confirmed) {
            return;
          }

          isSubmittingCheckout = true;
          dom.checkoutForm.submit();
        });

        return;
      }

      const confirmText = 'Invoice: ' + invoiceNo + '\n' +
        'Metode Bayar: ' + paymentMethodLabel + '\n' +
        'Grand Total: ' + grandTotalLabel + '\n\n' +
        (isPrintAction
          ? 'Transaksi akan disimpan lalu halaman nota cetak akan dibuka.'
          : 'Pastikan data transaksi dan pembayaran sudah benar.');

      if (window.confirm(confirmText)) {
        isSubmittingCheckout = true;
        dom.checkoutForm.submit();
      }
    });
  }

  function bindDrawerAndKeyboardEvents() {
    if (dom.scannerShortcutModifier) {
      dom.scannerShortcutModifier.textContent = state.modKeyLabel;
    }

    if (dom.cartToggleButton) {
      dom.cartToggleButton.addEventListener('click', function() {
        if (dom.cartColumn && dom.cartColumn.classList.contains('is-open')) {
          actions.closeCartDrawer();
          return;
        }

        actions.openCartDrawer();
      });
    }

    if (dom.cartDrawerClose) {
      dom.cartDrawerClose.addEventListener('click', actions.closeCartDrawer);
    }

    if (dom.cartDrawerBackdrop) {
      dom.cartDrawerBackdrop.addEventListener('click', actions.closeCartDrawer);
    }

    document.addEventListener('keydown', function(event) {
      const key = String(event.key || '').toLowerCase();

      if (key === 'escape') {
        event.preventDefault();

        if (dom.cartColumn && dom.cartColumn.classList.contains('is-open')) {
          actions.closeCartDrawer();
          return;
        }

        actions.focusSearchField(true);
        return;
      }

      const target = event.target;
      const typing = actions.isTypingTarget(target);
      const pressedMod = state.isMac ? event.metaKey : event.ctrlKey;

      if (!pressedMod) {
        if (!typing) {
          if (key === 'arrowdown') {
            event.preventDefault();
            actions.moveCartSelection(1);
            return;
          }

          if (key === 'arrowup') {
            event.preventDefault();
            actions.moveCartSelection(-1);
            return;
          }

          if (key === '+' || key === '=') {
            event.preventDefault();
            if (state.selectedCartProductId !== null) {
              actions.changeQty(state.selectedCartProductId, 1);
            }
            return;
          }

          if (key === '-') {
            event.preventDefault();
            if (state.selectedCartProductId !== null) {
              actions.changeQty(state.selectedCartProductId, -1);
            }
            return;
          }

          if (key === 'delete' || key === 'backspace') {
            event.preventDefault();
            if (state.selectedCartProductId !== null) {
              actions.removeItem(state.selectedCartProductId);
            }
            return;
          }

          if (key === 'f2') {
            event.preventDefault();
            if (dom.discountInput) {
              dom.discountInput.focus();
              dom.discountInput.select();
            }
            return;
          }

          if (key === 'f3') {
            event.preventDefault();
            if (dom.amountPaidInput) {
              dom.amountPaidInput.focus();
              dom.amountPaidInput.select();
            }
            return;
          }

          if (key === 'f9') {
            event.preventDefault();
            if (dom.checkoutForm) {
              dom.checkoutForm.requestSubmit();
            }
          }
        }

        return;
      }

      if (key === 'b') {
        event.preventDefault();
        if (dom.scanButton) {
          dom.scanButton.click();
        }
      }
    });
  }

  function bindModalEvents() {
    if (dom.scanButton && dom.cameraScanModal && typeof $ !== 'undefined') {
      if (dom.cameraScanModal.parentElement !== document.body) {
        document.body.appendChild(dom.cameraScanModal);
      }

      dom.scanButton.addEventListener('click', function() {
        $(dom.cameraScanModal).modal('show');
      });

      $(dom.cameraScanModal).on('shown.bs.modal', function() {
        actions.startCamera();
      });

      $(dom.cameraScanModal).on('hidden.bs.modal', function() {
        actions.stopCamera();
        dom.cameraAlertEl.classList.add('d-none');
        dom.cameraSuccessEl.classList.add('d-none');
      });
    }

    if (dom.shortcutHelpModal && typeof $ !== 'undefined') {
      if (dom.shortcutHelpModal.parentElement !== document.body) {
        document.body.appendChild(dom.shortcutHelpModal);
      }
    }

    if (dom.paymentModal && typeof $ !== 'undefined') {
      if (dom.paymentModal.parentElement !== document.body) {
        document.body.appendChild(dom.paymentModal);
      }

      $(dom.paymentModal).on('shown.bs.modal', function() {
        if (dom.amountPaidInput) {
          dom.amountPaidInput.focus();
          dom.amountPaidInput.select();
        }
      });
    }

    if (dom.pendingTransactionsModal && typeof $ !== 'undefined') {
      if (dom.pendingTransactionsModal.parentElement !== document.body) {
        document.body.appendChild(dom.pendingTransactionsModal);
      }
    }

    if (dom.pendingTransactionsList) {
      dom.pendingTransactionsList.addEventListener('click', function(event) {
        const restoreButton = event.target.closest('.js-restore-pending');
        if (restoreButton) {
          const pendingId = Number(restoreButton.dataset.id || 0);
          if (!pendingId) {
            return;
          }

          actions.restorePendingTransaction(pendingId).then(function(payload) {
            actions.notify(payload.message || 'Transaksi tertunda berhasil dimuat.', 'success', 'Berhasil');
          }).catch(function(error) {
            actions.notify(error.message || 'Gagal memuat transaksi tertunda.', 'error', 'Gagal');
          });
          return;
        }

        const deleteButton = event.target.closest('.js-delete-pending');
        if (!deleteButton) {
          return;
        }

        const pendingId = Number(deleteButton.dataset.id || 0);
        if (!pendingId) {
          return;
        }

        const doDelete = function() {
          actions.deletePendingTransaction(pendingId).then(function(payload) {
            actions.notify(payload.message || 'Transaksi tertunda berhasil dihapus.', 'success', 'Berhasil');
          }).catch(function(error) {
            actions.notify(error.message || 'Gagal menghapus transaksi tertunda.', 'error', 'Gagal');
          });
        };

        if (typeof window.swal === 'function') {
          window.swal({
            title: 'Hapus transaksi tertunda?',
            text: 'Data transaksi tertunda ini akan dihapus permanen.',
            icon: 'warning',
            buttons: {
              cancel: {
                text: 'Batal',
                visible: true
              },
              confirm: {
                text: 'Ya, hapus',
                visible: true
              }
            },
            dangerMode: true
          }).then(function(confirmed) {
            if (confirmed) {
              doDelete();
            }
          });
          return;
        }

        if (window.confirm('Hapus transaksi tertunda ini?')) {
          doDelete();
        }
      });
    }
  }

  function init() {
    bindCatalogEvents();
    bindPaymentEvents();
    bindDrawerAndKeyboardEvents();
    bindModalEvents();

    window.addEventListener('resize', actions.syncResponsiveState);

    actions.applyImageFallback();
    actions.applyCategoryBadgeColors();
    actions.syncCategoryChips();
    actions.syncResponsiveState();
    actions.syncAmountPaidDisplay(false);
    actions.filterCards();
    actions.renderCart();
    actions.fetchPendingTransactions();
  }

  init();
})(window);
