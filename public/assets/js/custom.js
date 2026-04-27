/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";

(function() {
	var SIDEBAR_STATE_KEY = 'ui.sidebar.state';

	function isDesktopLayout() {
		return window.innerWidth > 1024;
	}

	function getSavedSidebarState() {
		try {
			return localStorage.getItem(SIDEBAR_STATE_KEY);
		} catch (e) {
			return null;
		}
	}

	function saveSidebarState() {
		if (!isDesktopLayout()) {
			return;
		}

		try {
			var isMini = document.body.classList.contains('sidebar-mini');
			localStorage.setItem(SIDEBAR_STATE_KEY, isMini ? 'mini' : 'full');
		} catch (e) {}
	}

	function triggerSidebarToggle() {
		var toggleButton = document.querySelector("[data-toggle='sidebar']");
		if (!toggleButton) {
			return;
		}

		toggleButton.dispatchEvent(new MouseEvent('click', {
			bubbles: true,
			cancelable: true,
			view: window,
		}));
	}

	function applySavedSidebarState() {
		if (!isDesktopLayout()) {
			return;
		}

		var savedState = getSavedSidebarState();
		if (savedState !== 'mini' && savedState !== 'full') {
			return;
		}

		var isCurrentlyMini = document.body.classList.contains('sidebar-mini');
		if (savedState === 'mini' && !isCurrentlyMini) {
			triggerSidebarToggle();
		}
		if (savedState === 'full' && isCurrentlyMini) {
			triggerSidebarToggle();
		}

		// Fallback in case toggle handler is not ready yet.
		setTimeout(function() {
			var miniNow = document.body.classList.contains('sidebar-mini');
			if (savedState === 'mini' && !miniNow) {
				document.body.classList.add('sidebar-mini');
				document.body.classList.remove('sidebar-show');
			}
			if (savedState === 'full' && miniNow) {
				document.body.classList.remove('sidebar-mini');
			}
		}, 180);
	}

	var resizeDebounceTimer = null;

	document.addEventListener('click', function(event) {
		var toggleButton = event.target.closest("[data-toggle='sidebar']");
		if (!toggleButton) {
			return;
		}

		// Wait until Stisla finishes toggling classes.
		setTimeout(saveSidebarState, 80);
	});

	window.addEventListener('resize', function() {
		if (resizeDebounceTimer) {
			clearTimeout(resizeDebounceTimer);
		}

		resizeDebounceTimer = setTimeout(function() {
			applySavedSidebarState();
		}, 150);
	});

	document.addEventListener('DOMContentLoaded', function() {
		setTimeout(applySavedSidebarState, 220);
	});

	window.addEventListener('load', function() {
		setTimeout(function() {
			var savedState = getSavedSidebarState();
			if (savedState !== 'mini' && savedState !== 'full') {
				saveSidebarState();
				return;
			}

			applySavedSidebarState();
		}, 120);
	});

	window.addEventListener('beforeunload', saveSidebarState);
})();
