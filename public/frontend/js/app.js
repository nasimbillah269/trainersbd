/* TrainersBD storefront
 * Cart is stored in the browser for the mockup phase. When the order tables
 * are wired up, replace Cart.read / Cart.write with calls to the server.
 */
(function () {
    'use strict';

    var $  = function (sel, ctx) { return (ctx || document).querySelector(sel); };
    var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

    function money(n) { return '৳' + Number(n || 0).toLocaleString('en-US'); }
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    /* ---------------- Cart store ---------------- */
    var KEY = 'tbd_cart_v1';
    var memory = [];
    var Cart = {
        read: function () {
            try {
                var raw = window.localStorage.getItem(KEY);
                var list = raw ? JSON.parse(raw) : [];
                return Array.isArray(list) ? list : [];
            } catch (e) { return memory.slice(); }
        },
        write: function (list) {
            memory = list.slice();
            try { window.localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) { /* private mode */ }
            Cart.sync();
        },
        has: function (id) { return Cart.read().some(function (i) { return i.id === id; }); },
        add: function (item) {
            var list = Cart.read();
            if (!list.some(function (i) { return i.id === item.id; })) { list.push(item); }
            Cart.write(list);
        },
        remove: function (id) { Cart.write(Cart.read().filter(function (i) { return i.id !== id; })); },
        clear: function () { Cart.write([]); },
        totals: function () {
            var list = Cart.read();
            var original = 0, total = 0;
            list.forEach(function (i) {
                var price = Number(i.price) || 0;
                var old = Number(i.old_price) > price ? Number(i.old_price) : price;
                original += old; total += price;
            });
            return { count: list.length, original: original, total: total, discount: original - total };
        },
        sync: function () {
            var t = Cart.totals();
            $$('[data-cart-count]').forEach(function (el) {
                el.textContent = t.count;
                el.hidden = t.count === 0;
            });
            $$('[data-sum-original]').forEach(function (el) { el.textContent = money(t.original); });
            $$('[data-sum-discount]').forEach(function (el) { el.textContent = '-' + money(t.discount); });
            $$('[data-sum-total]').forEach(function (el) { el.textContent = money(t.total); });
            var save = $('[data-sum-save]');
            if (save) { save.hidden = t.discount <= 0; $('strong', save).textContent = money(t.discount); }
            var heading = $('[data-cart-heading-count]');
            if (heading) { heading.textContent = t.count ? '(' + t.count + (t.count > 1 ? ' courses)' : ' course)') : ''; }
            markButtons();
        }
    };
    window.TBDCart = Cart;

    function parseData(el, attr) {
        try { return JSON.parse(el.getAttribute(attr)); } catch (e) { return null; }
    }

    function markButtons() {
        $$('[data-add-to-cart]').forEach(function (btn) {
            var d = parseData(btn, 'data-add-to-cart');
            if (!d) { return; }
            var inCart = Cart.has(d.id);
            btn.classList.toggle('is-in-cart', inCart);
            var label = $('span', btn);
            var icon = $('i', btn);
            if (label) { label.textContent = inCart ? 'Go to Cart' : 'Add to Cart'; }
            if (icon) { icon.className = inCart ? 'bi bi-bag-check-fill' : 'bi bi-bag-plus'; }
        });
    }

    /* ---------------- Toast ---------------- */
    function toast(html, type) {
        var stack = $('#toastStack');
        if (!stack) { return; }
        var el = document.createElement('div');
        el.className = 'toast-item toast-item--' + (type || 'success');
        el.innerHTML = html;
        stack.appendChild(el);
        requestAnimationFrame(function () { el.classList.add('is-in'); });
        setTimeout(function () {
            el.classList.remove('is-in');
            setTimeout(function () { el.remove(); }, 300);
        }, 3200);
    }

    /* ---------------- Add to cart / buy now ---------------- */
    document.addEventListener('click', function (e) {
        var add = e.target.closest('[data-add-to-cart]');
        if (add) {
            var d = parseData(add, 'data-add-to-cart');
            if (!d) { return; }
            if (Cart.has(d.id)) { window.location.href = TBD.routes.cart; return; }
            Cart.add(d);
            toast('<i class="bi bi-bag-check-fill"></i><div><strong>Added to cart</strong><span>' + esc(d.title) +
                  '</span></div><a href="' + TBD.routes.cart + '">View cart</a>');
            return;
        }
        var buy = e.target.closest('[data-buy-now]');
        if (buy) {
            var b = parseData(buy, 'data-buy-now');
            if (b) { Cart.add(b); window.location.href = TBD.routes.checkout; }
            return;
        }
        var copy = e.target.closest('[data-copy]');
        if (copy) {
            var text = copy.getAttribute('data-copy');
            var done = function () {
                copy.innerHTML = '<i class="bi bi-check2"></i> Copied';
                setTimeout(function () { copy.innerHTML = '<i class="bi bi-copy"></i> Copy'; }, 1800);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(done, done);
            } else {
                var ta = document.createElement('textarea');
                ta.value = text; document.body.appendChild(ta); ta.select();
                try { document.execCommand('copy'); } catch (err) { /* ignore */ }
                ta.remove(); done();
            }
        }
    });

    /* ---------------- YouTube player modal ---------------- */
    var videoModalEl = $('#videoModal');
    if (videoModalEl && window.bootstrap) {
        var videoModal = new bootstrap.Modal(videoModalEl);
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-video]');
            if (!trigger) { return; }
            var id = trigger.getAttribute('data-video');
            if (!id || !/^[A-Za-z0-9_-]{6,20}$/.test(id)) { return; }
            e.preventDefault();
            $('#videoModalTitle').textContent = trigger.getAttribute('data-video-title') || 'Lesson';
            $('#videoModalFrame').innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/' + id +
                '?autoplay=1&rel=0&modestbranding=1" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            videoModal.show();
        });
        videoModalEl.addEventListener('hidden.bs.modal', function () { $('#videoModalFrame').innerHTML = ''; });
    }

    /* ---------------- Header shadow ---------------- */
    var header = $('#siteHeader');
    if (header) {
        var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 10); };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    /* ---------------- Home: course filters ---------------- */
    var grid = $('[data-course-grid]');
    if (grid) {
        var state = { cat: 'all', q: '' };
        var items = $$('.course-grid__item', grid);
        var empty = $('[data-empty-filter]');

        var apply = function () {
            var shown = 0;
            items.forEach(function (item) {
                var card = $('[data-course-card]', item);
                var okCat = state.cat === 'all' || card.dataset.category === state.cat;
                var okQ = !state.q || card.dataset.title.indexOf(state.q) !== -1;
                item.hidden = !(okCat && okQ);
                if (!item.hidden) { shown++; }
            });
            if (empty) { empty.hidden = shown > 0; }
        };
        var setCat = function (cat) {
            state.cat = cat;
            $$('[data-filter]').forEach(function (b) { b.classList.toggle('active', b.dataset.filter === cat); });
            var active = $('[data-filter].active');
            if (active && active.scrollIntoView) { active.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' }); }
            apply();
        };

        $$('[data-filter]').forEach(function (b) { b.addEventListener('click', function () { setCat(b.dataset.filter); }); });
        $$('[data-category-link]').forEach(function (a) { a.addEventListener('click', function () { setCat(a.dataset.categoryLink); }); });

        var search = $('[data-course-search]');
        if (search) { search.addEventListener('input', function () { state.q = search.value.trim().toLowerCase(); apply(); }); }

        var heroSearch = $('[data-hero-search]');
        if (heroSearch) {
            heroSearch.addEventListener('submit', function (e) {
                e.preventDefault();
                var v = $('input', heroSearch).value.trim();
                if (search) { search.value = v; }
                state.q = v.toLowerCase(); setCat('all');
                $('#courses').scrollIntoView({ behavior: 'smooth' });
            });
        }

        $$('[data-hero-tag]').forEach(function (tag) {
            tag.addEventListener('click', function () {
                var v = tag.getAttribute('data-hero-tag');
                if (search) { search.value = v; }
                state.q = v.toLowerCase(); setCat('all');
            });
        });

        var reset = $('[data-filter-reset]');
        if (reset) { reset.addEventListener('click', function () { if (search) { search.value = ''; } state.q = ''; setCat('all'); }); }

        var sort = $('[data-course-sort]');
        if (sort) {
            sort.addEventListener('change', function () {
                var key = sort.value;
                items.sort(function (a, b) {
                    if (key === 'rating') { return b.dataset.sortRating - a.dataset.sortRating; }
                    if (key === 'price-asc') { return a.dataset.sortPrice - b.dataset.sortPrice; }
                    if (key === 'price-desc') { return b.dataset.sortPrice - a.dataset.sortPrice; }
                    return b.dataset.sortPopular - a.dataset.sortPopular;
                });
                items.forEach(function (i) { grid.appendChild(i); });
            });
        }

        var params = new URLSearchParams(window.location.search);
        if (params.get('category')) { setCat(params.get('category')); }
        if (params.get('q') && search) { search.value = params.get('q'); state.q = params.get('q').toLowerCase(); apply(); }
    }

    /* ---------------- Course page ---------------- */
    var readmore = $('[data-readmore]');
    var readBtn = $('[data-readmore-toggle]');
    if (readmore && readBtn) {
        if (readmore.scrollHeight <= 260) { readBtn.hidden = true; readmore.classList.add('is-open'); }
        readBtn.addEventListener('click', function () {
            var open = readmore.classList.toggle('is-open');
            readBtn.innerHTML = open ? 'Show less <i class="bi bi-chevron-up"></i>' : 'Show more <i class="bi bi-chevron-down"></i>';
        });
    }

    var expandAll = $('[data-expand-all]');
    if (expandAll && window.bootstrap) {
        var expanded = false;
        expandAll.addEventListener('click', function () {
            expanded = !expanded;
            $$('#curriculumAcc .accordion-collapse').forEach(function (el) {
                bootstrap.Collapse.getOrCreateInstance(el, { toggle: false })[expanded ? 'show' : 'hide']();
            });
            expandAll.textContent = expanded ? 'Collapse all' : 'Expand all';
        });
    }

    var spyNav = $('[data-scrollspy-nav]');
    if (spyNav && 'IntersectionObserver' in window) {
        var links = $$('a', spyNav);
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) {
                    links.forEach(function (l) { l.classList.toggle('active', l.getAttribute('href') === '#' + en.target.id); });
                }
            });
        }, { rootMargin: '-30% 0px -60% 0px' });
        links.forEach(function (l) { var t = $(l.getAttribute('href')); if (t) { io.observe(t); } });
    }

    var reviewForm = $('[data-review-form]');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!reviewForm.checkValidity()) {
                reviewForm.classList.add('was-validated');
                if (!$('input[name="rating"]:checked', reviewForm)) { toast('<i class="bi bi-star"></i><div><strong>Select a rating</strong><span>Tap the stars to rate this course.</span></div>', 'warn'); }
                return;
            }
            var fd = new FormData(reviewForm);
            var submit = $('[type="submit"]', reviewForm);
            submit.disabled = true;
            post(reviewForm.getAttribute('action'), {
                name: fd.get('name'), email: fd.get('email'), rating: fd.get('rating'), review: fd.get('review')
            }).then(function (r) {
                submit.disabled = false;
                if (r.ok && r.body.success) {
                    reviewForm.reset();
                    reviewForm.classList.remove('was-validated');
                    toast('<i class="bi bi-check-circle-fill"></i><div><strong>Thank you!</strong><span>Your review will appear after approval.</span></div>');
                } else {
                    var errs = (r.body && r.body.errors) || {};
                    var m = r.status === 429 ? 'Please wait a minute before trying again.' : ((Object.values(errs)[0] || ['Please check the form and try again.'])[0]);
                    toast('<i class="bi bi-exclamation-circle"></i><div><strong>Review not sent</strong><span>' + esc(m) + '</span></div>', 'warn');
                }
            }).catch(function () { submit.disabled = false; });
        });
    }

    var reviewToggle = $('[data-review-toggle]');
    if (reviewToggle) {
        reviewToggle.addEventListener('click', function () {
            $$('[data-review-more]').forEach(function (el) { el.hidden = false; });
            reviewToggle.remove();
        });
    }

    /* ---------------- Cart page ---------------- */
    function thumb(item, cls) {
        var inner = item.image
            ? '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="' + esc(item.image) + '" alt="" class="lazy" decoding="async">'
            : '<i class="bi ' + esc(item.icon) + ' course-thumb__icon"></i>';
        return '<a href="' + esc(item.url) + '" class="course-thumb ' + (item.image ? 'course-thumb--img ' : '') + (cls || '') + '">' + inner + '</a>';
    }

    function post(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': TBD.csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(data)
        }).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (body) { return { ok: res.ok, status: res.status, body: body }; });
        });
    }

    /* Refresh the browser cart from the server: drops removed courses and updates prices. */
    function syncCart() {
        var local = Cart.read();
        if (!local.length) { return Promise.resolve(local); }
        return post(TBD.routes.cartSync, { ids: local.map(function (i) { return i.id; }) })
            .then(function (r) {
                if (r.ok && Array.isArray(r.body.items)) {
                    if (r.body.items.length < local.length) {
                        toast('<i class="bi bi-info-circle"></i><div><strong>Cart updated</strong><span>Some courses are no longer available.</span></div>', 'warn');
                    }
                    Cart.write(r.body.items);
                }
                return Cart.read();
            })
            .catch(function () { return local; });
    }

    function renderCart() {
        var list = $('[data-cart-list]');
        if (!list) { return; }
        var items = Cart.read();
        $('[data-cart-loading]').hidden = true;
        $('[data-cart-filled]').hidden = items.length === 0;
        $('[data-cart-empty]').hidden = items.length > 0;
        list.innerHTML = items.map(function (i) {
            var hasOld = Number(i.old_price) > Number(i.price);
            return '<li class="cart-item">' + thumb(i, 'cart-item__thumb') +
                '<div class="cart-item__info">' +
                    '<h3><a href="' + esc(i.url) + '">' + esc(i.title) + '</a></h3>' +
                    '<p>By ' + esc(i.instructor) + '</p>' +
                    '<div class="course-meta"><span><i class="bi bi-play-circle"></i> ' + esc(i.lessons) + ' lessons</span><span><i class="bi bi-clock"></i> ' + esc(i.duration) + '</span></div>' +
                '</div>' +
                '<div class="cart-item__side">' +
                    '<div class="price"><span class="price__now">' + money(i.price) + '</span>' + (hasOld ? '<span class="price__old">' + money(i.old_price) + '</span>' : '') + '</div>' +
                    '<button type="button" class="cart-item__remove" data-cart-remove="' + esc(i.id) + '"><i class="bi bi-trash3"></i> Remove</button>' +
                '</div></li>';
        }).join('');
        observeLazy(list);
    }

    if ($('[data-cart-list]')) {
        syncCart().then(renderCart);
        document.addEventListener('click', function (e) {
            var rm = e.target.closest('[data-cart-remove]');
            if (rm) {
                var li = rm.closest('.cart-item');
                li.classList.add('is-removing');
                setTimeout(function () { Cart.remove(Number(rm.getAttribute('data-cart-remove'))); renderCart(); }, 250);
            }
            if (e.target.closest('[data-cart-clear]')) {
                if (window.confirm('Remove all courses from your cart?')) { Cart.clear(); renderCart(); }
            }
            if (e.target.closest('[data-add-to-cart]')) { renderCart(); }
        });
    }

    /* ---------------- Checkout page ---------------- */
    var form = $('[data-checkout-form]');
    if (form) { syncCart().then(function () { initCheckout(form); }); }

    function initCheckout(form) {
        var items = Cart.read();
        $('[data-cart-loading]').hidden = true;
        form.hidden = items.length === 0;
        $('[data-checkout-empty]').hidden = items.length > 0;

        $('[data-checkout-items]').innerHTML = items.map(function (i) {
            return '<li>' + thumb(i, 'mini-cart__thumb') + '<div><a href="' + esc(i.url) + '">' + esc(i.title) + '</a><span>' + money(i.price) + '</span></div></li>';
        }).join('');
        observeLazy($('[data-checkout-items]'));

        var senderLabel = $('[data-sender-label]');
        var sender = $('#ckSender');
        var showPanel = function () {
            var key = ($('[data-pay-radio]:checked') || {}).value;
            $$('[data-pay-panel]').forEach(function (p) { p.hidden = p.getAttribute('data-pay-panel') !== key; });
            if (key === 'bank') { senderLabel.textContent = 'Sender account number'; sender.placeholder = 'Account you transferred from'; }
            else { senderLabel.textContent = 'Sender number'; sender.placeholder = '01XXXXXXXXX'; }
        };
        $$('[data-pay-radio]').forEach(function (r) { r.addEventListener('change', showPanel); });
        showPanel();

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                var bad = $(':invalid', form);
                if (bad) { bad.scrollIntoView({ behavior: 'smooth', block: 'center' }); bad.focus({ preventScroll: true }); }
                return;
            }
            var btn = $('[data-place-order]');
            var btnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Placing order...';

            var data = new FormData(form);
            var payload = {
                name: data.get('name'), mobile: data.get('mobile'), email: data.get('email'),
                payment_method: data.get('payment_method'), sender_number: data.get('sender_number'),
                transaction_id: data.get('transaction_id'), note: data.get('note'),
                password: data.get('password'),
                items: Cart.read().map(function (i) { return i.id; })
            };

            post(TBD.routes.checkoutPlace, payload).then(function (r) {
                if (r.ok && r.body.success) {
                    Cart.clear();
                    btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Order placed! Redirecting…';
                    window.location.replace(r.body.redirect);
                    return;
                }
                btn.disabled = false;
                btn.innerHTML = btnHtml;
                var errors = (r.body && r.body.errors) || {};
                var first = null;
                Object.keys(errors).forEach(function (field) {
                    var input = form.querySelector('[name="' + field + '"]');
                    if (input) {
                        input.setCustomValidity(errors[field][0]);
                        var fb = input.closest('.col-12, .col-md-6');
                        var msg = fb && $('.invalid-feedback', fb);
                        if (msg) { msg.textContent = errors[field][0]; }
                        input.addEventListener('input', function clear() { input.setCustomValidity(''); input.removeEventListener('input', clear); });
                        first = first || input;
                    }
                });
                form.classList.add('was-validated');
                var msgText = r.status === 429 ? 'Too many attempts. Please wait a minute and try again.'
                    : (Object.values(errors)[0] || ['Something went wrong. Please try again.'])[0];
                var loginLink = r.body && r.body.login ? ' <a href="' + esc(r.body.login) + '?redirect=checkout">Log in now</a>' : '';
                toast('<i class="bi bi-exclamation-circle"></i><div><strong>Could not place order</strong><span>' + esc(msgText) + loginLink + '</span></div>', 'warn');
                if (first) { first.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
            }).catch(function () {
                btn.disabled = false;
                btn.innerHTML = btnHtml;
                toast('<i class="bi bi-wifi-off"></i><div><strong>Network error</strong><span>Please check your connection and try again.</span></div>', 'warn');
            });
        });
    }


    /* ---------------- Motion & performance ---------------- */
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var hasIO = 'IntersectionObserver' in window;

    /* Lazy images: <img class="lazy" data-src="..."> load just before they scroll into view,
       with a shimmer skeleton until the file has arrived. */
    function loadImage(img) {
        var src = img.getAttribute('data-src');
        if (!src) { return; }
        img.removeAttribute('data-src');
        var done = function () { img.classList.add('is-loaded'); };
        img.addEventListener('load', done, { once: true });
        img.addEventListener('error', done, { once: true });
        img.src = src;
    }
    var lazyIO = hasIO ? new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
            if (en.isIntersecting) { lazyIO.unobserve(en.target); loadImage(en.target); }
        });
    }, { rootMargin: '300px 0px' }) : null;
    function observeLazy(ctx) {
        $$('img.lazy[data-src]', ctx).forEach(function (img) {
            if (lazyIO) { lazyIO.observe(img); } else { loadImage(img); }
        });
    }
    window.TBD.observeLazy = observeLazy;
    observeLazy();

    /* Scroll reveal: elements fade/slide in as they enter the viewport.
       Only elements that start below the fold are hidden, so nothing visible ever flickers. */
    var revealSelectors = [
        '.section-head', '.course-card', '.free-card', '.stat', '.step', '.cat-card', '.category-card',
        '.testimonial', '.review-card', '.faq-item', '.accordion-item', '.feature', '.why-card',
        '.footer-cta', '.site-footer .col-lg-4', '.site-footer .col-6', '.contact-tile', '.page-content',
        '.success-card', '.track', '.sk-card', '[data-reveal]'
    ].join(',');
    function setupReveal(ctx) {
        if (reduceMotion || !hasIO) { return; }
        var fold = window.innerHeight;
        var groups = new Map();
        $$(revealSelectors, ctx).forEach(function (el) {
            if (el.hasAttribute('data-revealed') || el.closest('.offcanvas, .modal, .sub-menu, .sk-wrap')) { return; }
            el.setAttribute('data-revealed', '');
            if (el.getBoundingClientRect().top < fold - 40) { return; }
            // Stagger siblings that sit in the same row/grid
            var parent = el.parentElement && el.parentElement.closest('.row, .course-grid, .stats-strip .container, ul, .container') || document.body;
            var i = groups.get(parent) || 0;
            groups.set(parent, i + 1);
            el.style.setProperty('--reveal-delay', Math.min(i % 4, 3) * 90 + 'ms');
            el.classList.add('reveal');
            revealIO.observe(el);
        });
    }
    var revealIO = hasIO ? new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
            if (en.isIntersecting) {
                en.target.classList.add('is-visible');
                revealIO.unobserve(en.target);
                en.target.addEventListener('transitionend', function () {
                    en.target.classList.remove('reveal', 'is-visible');
                    en.target.style.removeProperty('--reveal-delay');
                }, { once: true });
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 }) : null;
    setupReveal();

    /* Count-up numbers in the stats strip when they first appear */
    if (hasIO && !reduceMotion) {
        var countIO = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (!en.isIntersecting) { return; }
                countIO.unobserve(en.target);
                var el = en.target, text = el.textContent;
                var m = text.match(/^([^\d]*)([\d,]+(?:\.\d+)?)(.*)$/);
                if (!m) { return; }
                var target = parseFloat(m[2].replace(/,/g, '')), decimals = (m[2].split('.')[1] || '').length;
                var start = performance.now(), dur = 1400;
                var step = function (now) {
                    var p = Math.min((now - start) / dur, 1), eased = 1 - Math.pow(1 - p, 3);
                    var val = (target * eased).toFixed(decimals);
                    el.textContent = m[1] + (decimals ? val : Number(val).toLocaleString('en-US')) + m[3];
                    if (p < 1) { requestAnimationFrame(step); } else { el.textContent = text; }
                };
                requestAnimationFrame(step);
            });
        }, { threshold: 0.6 });
        $$('.stat strong, [data-count-up]').forEach(function (el) { countIO.observe(el); });
    }

    /* Smooth, inertia-style scrolling (desktop mouse/trackpad only; touch keeps native scrolling) */
    if (window.Lenis && !reduceMotion && window.matchMedia('(pointer: fine)').matches) {
        var lenis = new window.Lenis({ duration: 1.1, smoothWheel: true, wheelMultiplier: 1, easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); } });
        window.TBD.lenis = lenis;
        var raf = function (time) { lenis.raf(time); requestAnimationFrame(raf); };
        requestAnimationFrame(raf);

        // Pause page scrolling while a modal or the mobile menu is open
        ['show.bs.modal', 'show.bs.offcanvas'].forEach(function (ev) { document.addEventListener(ev, function () { lenis.stop(); }); });
        ['hidden.bs.modal', 'hidden.bs.offcanvas'].forEach(function (ev) { document.addEventListener(ev, function () { lenis.start(); }); });

        // Same-page anchor links glide to their section (below the sticky header)
        document.addEventListener('click', function (e) {
            var a = e.target.closest('a[href*="#"]');
            if (!a || e.defaultPrevented || a.hasAttribute('data-bs-toggle')) { return; }
            var url = new URL(a.href, location.href);
            if (url.pathname !== location.pathname || !url.hash || url.hash === '#') { return; }
            var target = document.getElementById(decodeURIComponent(url.hash.slice(1)));
            if (!target) { return; }
            e.preventDefault();
            lenis.scrollTo(target, { offset: -90 });
            history.pushState(null, '', url.hash);
        });
    }

    Cart.sync();
})();
