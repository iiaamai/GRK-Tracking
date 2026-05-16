(function () {
  'use strict';

  function pageSizeFor(table) {
    var n = parseInt(table.getAttribute('data-page-size') || '5', 10);
    return Number.isFinite(n) && n > 0 ? n : 5;
  }

  function filterHidden(tr) {
    return tr.style.display === 'none';
  }

  function dataRows(table) {
    return Array.prototype.slice.call(table.querySelectorAll('tbody tr')).filter(function (tr) {
      return !tr.classList.contains('table-pager-skip');
    });
  }

  function visibleRows(table) {
    return dataRows(table).filter(function (tr) {
      return !filterHidden(tr);
    });
  }

  function ensurePager(wrap) {
    var next = wrap.nextElementSibling;
    if (next && next.classList.contains('table-pager')) {
      return next;
    }
    var nav = document.createElement('nav');
    nav.className = 'table-pager';
    nav.setAttribute('aria-label', 'Table pagination');
    nav.innerHTML =
      '<button type="button" class="btn btn--ghost table-pager__prev">Previous</button>' +
      '<span class="table-pager__info" aria-live="polite"></span>' +
      '<button type="button" class="btn btn--ghost table-pager__next">Next</button>';
    wrap.parentNode.insertBefore(nav, wrap.nextSibling);
    return nav;
  }

  function initTable(table) {
    var pageSize = pageSizeFor(table);
    var currentPage = 0;
    var wrap = table.closest('.table-wrap');
    if (!wrap) {
      return;
    }

    var pager = ensurePager(wrap);
    var prevBtn = pager.querySelector('.table-pager__prev');
    var nextBtn = pager.querySelector('.table-pager__next');
    var info = pager.querySelector('.table-pager__info');
    if (!prevBtn || !nextBtn || !info) {
      return;
    }

    function render() {
      var visible = visibleRows(table);
      var total = visible.length;
      var totalPages = total === 0 ? 0 : Math.ceil(total / pageSize);
      if (currentPage >= totalPages) {
        currentPage = Math.max(0, totalPages - 1);
      }

      var rows = dataRows(table);
      var idx = 0;
      var start = currentPage * pageSize;
      var end = Math.min(start + pageSize, total);

      rows.forEach(function (tr) {
        if (filterHidden(tr)) {
          tr.classList.remove('is-pager-hidden');
          return;
        }
        var onPage = idx >= start && idx < end;
        idx += 1;
        tr.classList.toggle('is-pager-hidden', !onPage);
      });

      if (total === 0) {
        info.textContent = '0 of 0';
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        prevBtn.setAttribute('aria-disabled', 'true');
        nextBtn.setAttribute('aria-disabled', 'true');
        return;
      }

      info.textContent = String(start + 1) + '\u2013' + String(end) + ' of ' + String(total);
      var atStart = currentPage <= 0;
      var atEnd = currentPage >= totalPages - 1;
      prevBtn.disabled = atStart;
      nextBtn.disabled = atEnd;
      prevBtn.setAttribute('aria-disabled', atStart ? 'true' : 'false');
      nextBtn.setAttribute('aria-disabled', atEnd ? 'true' : 'false');
    }

    prevBtn.addEventListener('click', function () {
      if (currentPage > 0) {
        currentPage -= 1;
        render();
      }
    });

    nextBtn.addEventListener('click', function () {
      var total = visibleRows(table).length;
      var totalPages = Math.ceil(total / pageSize);
      if (currentPage < totalPages - 1) {
        currentPage += 1;
        render();
      }
    });

    table.addEventListener('paginate-refresh', function () {
      currentPage = 0;
      render();
    });

    render();
  }

  function init() {
    document.querySelectorAll('table.js-paginated-table').forEach(initTable);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
