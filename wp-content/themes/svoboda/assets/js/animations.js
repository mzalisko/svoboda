/**
 * animations.js — вся інтерактивна/скролова логіка лендинга "Свобода".
 * Чистий vanilla JS, без jQuery/бібліотек анімації.
 *
 * Декоративні inline-луп анімації (плаваючий літачок у hero, "дихання" фото)
 * лишаються чистим CSS @keyframes (див. style.css) — тут лише логіка,
 * що не може бути виражена в CSS: scroll listeners, IntersectionObserver,
 * scroll-linked SVG line drawing, 3D tilt, slider, video embed.
 */

(function () {
	'use strict';

	/* ------------------------------------------------------------------ */
	/* 0. Блокування скролу: overflow:hidden прибирає скролбар і контент
	   стрибає на його ширину -- компенсуємо padding-right на body.          */
	/* ------------------------------------------------------------------ */
	function svobodaLockScroll() {
		var scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
		document.body.style.paddingRight = scrollbarWidth > 0 ? scrollbarWidth + 'px' : '';
		document.body.classList.add( 'has-mobile-nav-open' );
	}

	function svobodaUnlockScroll() {
		document.body.classList.remove( 'has-mobile-nav-open' );
		document.body.style.paddingRight = '';
	}

	/* ------------------------------------------------------------------ */
	/* 1. Header: компактність/напівпрозорість при скролі                 */
	/* ------------------------------------------------------------------ */
	function initHeaderScroll() {
		var header = document.getElementById( 'site-header' );
		if ( ! header ) {
			return;
		}

		var threshold = 40;

		function onScroll() {
			if ( window.scrollY > threshold ) {
				header.classList.add( 'is-compact' );
			} else {
				header.classList.remove( 'is-compact' );
			}
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	/* ------------------------------------------------------------------ */
	/* 1b. Мобільне меню: бургер відкриває/закриває випадну панель          */
	/* ------------------------------------------------------------------ */
	function initMobileMenu() {
		var burger = document.querySelector( '[data-burger-toggle]' );
		var nav    = document.querySelector( '[data-mobile-nav]' );
		if ( ! burger || ! nav ) {
			return;
		}

		function closeMenu() {
			burger.classList.remove( 'is-open' );
			nav.classList.remove( 'is-open' );
			burger.setAttribute( 'aria-expanded', 'false' );
			svobodaUnlockScroll();
		}

		function openMenu() {
			burger.classList.add( 'is-open' );
			nav.classList.add( 'is-open' );
			burger.setAttribute( 'aria-expanded', 'true' );
			svobodaLockScroll();
		}

		burger.addEventListener( 'click', function () {
			if ( nav.classList.contains( 'is-open' ) ) {
				closeMenu();
			} else {
				openMenu();
			}
		} );

		nav.querySelectorAll( 'a' ).forEach( function ( link ) {
			link.addEventListener( 'click', closeMenu );
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! nav.classList.contains( 'is-open' ) ) {
				return;
			}
			if ( ! nav.contains( e.target ) && ! burger.contains( e.target ) ) {
				closeMenu();
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				closeMenu();
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* 2. Fade-in по скролу (IntersectionObserver, threshold 0.2 за замовч.) */
	/* ------------------------------------------------------------------ */
	function initFadeIn() {
		var targets = document.querySelectorAll( '[data-fade-in]' );
		if ( ! targets.length || ! 'IntersectionObserver' in window ) {
			targets.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( ! entry.isIntersecting ) {
						return;
					}
					var el    = entry.target;
					var delay = parseInt( el.getAttribute( 'data-fade-delay' ) || '0', 10 );
					window.setTimeout( function () {
						el.classList.add( 'is-visible' );
					}, delay );
					observer.unobserve( el );
				} );
			},
			{ threshold: 0.2 }
		);

		targets.forEach( function ( el ) {
			var customThreshold = el.getAttribute( 'data-fade-threshold' );
			if ( customThreshold ) {
				// Окремий observer, якщо секція просить інший threshold.
				var customObserver = new IntersectionObserver(
					function ( entries ) {
						entries.forEach( function ( entry ) {
							if ( entry.isIntersecting ) {
								entry.target.classList.add( 'is-visible' );
								customObserver.unobserve( entry.target );
							}
						} );
					},
					{ threshold: parseFloat( customThreshold ) }
				);
				customObserver.observe( el );
			} else {
				observer.observe( el );
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* 3. 3D-нахил обкладинки книги / hero-літачка за курсором             */
	/* ------------------------------------------------------------------ */
	function initTilt() {
		var targets = document.querySelectorAll( '[data-tilt]' );

		targets.forEach( function ( el ) {
			var maxRotate = 12;

			el.addEventListener( 'mousemove', function ( e ) {
				var rect  = el.getBoundingClientRect();
				var x     = ( e.clientX - rect.left ) / rect.width - 0.5;
				var y     = ( e.clientY - rect.top ) / rect.height - 0.5;
				var rotX  = ( -y * maxRotate ).toFixed( 2 );
				var rotY  = ( x * maxRotate ).toFixed( 2 );

				el.style.transform = 'perspective(600px) rotateX(' + rotX + 'deg) rotateY(' + rotY + 'deg)';
			} );

			el.addEventListener( 'mouseleave', function () {
				el.style.transform = 'perspective(600px) rotateX(0deg) rotateY(0deg)';
			} );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* 4. SVG-лінія трансформації: промальовка + літачок + куля, залежно  */
	/*    від прогресу скролу секції (докладний опис — animations.md п.5) */
	/* ------------------------------------------------------------------ */
	function initScrollLine() {
		var section = document.querySelector( '[data-scroll-line-section]' );
		if ( ! section ) {
			return;
		}

		var path      = section.querySelector( '[data-scroll-line-path]' );
		var plane     = section.querySelector( '[data-scroll-line-plane]' );
		var ball      = section.querySelector( '[data-scroll-line-ball]' );
		var pointEls  = section.querySelectorAll( '[data-scroll-line-point]' );

		if ( ! path ) {
			return;
		}

		var pathLength = path.getTotalLength ? path.getTotalLength() : 1;

		function clamp( value, min, max ) {
			return Math.max( min, Math.min( max, value ) );
		}

		function onScroll() {
			var rect = section.getBoundingClientRect();
			var vh   = window.innerHeight;

			// Прогрес 0 → 1 поки секція проходить крізь viewport.
			var progress = clamp( ( vh - rect.top ) / ( rect.height + vh ), 0, 1 );

			// Промальовка лінії: stroke-dashoffset від 1 до 0 (pathLength="1" в атрибуті SVG).
			path.style.strokeDashoffset = String( 1 - progress );

			// Літачок рухається вздовж path відповідно до progress.
			if ( plane && path.getPointAtLength ) {
				var point = path.getPointAtLength( progress * pathLength );
				plane.style.transform = 'translate(' + point.x + 'px,' + point.y + 'px) translate(-50%, -50%)';
			}

			// Куля/планета проявляється в кінці (opacity keyframe kf_ball_opacity).
			if ( ball ) {
				var ballOpacity = progress > 0.9 ? ( progress - 0.9 ) / 0.1 : 0;
				ball.style.opacity = String( clamp( ballOpacity, 0, 1 ) );

				var endPoint = path.getPointAtLength ? path.getPointAtLength( pathLength ) : { x: 0, y: 0 };
				ball.style.left = endPoint.x + 'px';
				ball.style.top  = endPoint.y + 'px';
			}

			// Підсвітити пункти шляху, коли прогрес лінії досягає їхньої позиції.
			pointEls.forEach( function ( el ) {
				var index          = parseInt( el.getAttribute( 'data-scroll-line-point' ), 10 );
				var pointThreshold = ( index + 1 ) / ( pointEls.length + 1 );
				el.classList.toggle( 'is-active', progress >= pointThreshold );
			} );
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', onScroll );
		onScroll();
	}

	/* ------------------------------------------------------------------ */
	/* 5. Паралакс скетч-шарів (секція "Цитатний блок")                   */
	/* ------------------------------------------------------------------ */
	function initParallaxLayers() {
		var layers = document.querySelectorAll( '[data-parallax-layer]' );
		if ( ! layers.length ) {
			return;
		}

		function onScroll() {
			layers.forEach( function ( layer ) {
				var rect  = layer.getBoundingClientRect();
				var speed = 0.15;
				var offset = ( rect.top - window.innerHeight / 2 ) * speed;
				layer.style.transform = 'translate3d(0,' + offset.toFixed( 1 ) + 'px, 0)';
			} );
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	/* ------------------------------------------------------------------ */
	/* 6. Паралакс фінальної CTA (фон гори)                                */
	/* ------------------------------------------------------------------ */
	function initParallaxBg() {
		var el = document.querySelector( '[data-parallax-bg]' );
		if ( ! el ) {
			return;
		}

		function onScroll() {
			var rect  = el.getBoundingClientRect();
			var speed = 0.3;
			el.style.backgroundPositionY = ( rect.top * speed ) + 'px';
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	/* ------------------------------------------------------------------ */
	/* 7. Відеоблок: pulse-кнопка, заміна прев'ю на відео при кліку        */
	/* ------------------------------------------------------------------ */
	function initVideoBlock() {
		var player  = document.querySelector( '[data-video-player]' );
		var playBtn = document.querySelector( '[data-video-play-btn]' );

		if ( ! player || ! playBtn ) {
			return;
		}

		playBtn.classList.add( 'is-pulsing' );

		player.addEventListener( 'click', function () {
			var embedUrl = player.getAttribute( 'data-embed-url' );
			if ( ! embedUrl ) {
				return;
			}

			var iframe = document.createElement( 'iframe' );
			iframe.src             = embedUrl;
			iframe.width           = '100%';
			iframe.height          = '100%';
			iframe.style.border    = '0';
			iframe.allow           = 'autoplay; encrypted-media; picture-in-picture';
			iframe.allowFullscreen  = true;
			iframe.loading         = 'lazy';

			player.innerHTML = '';
			player.appendChild( iframe );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* 8. Карусель відгуків: власний vanilla JS slider (свайпи, стрілки,   */
	/*    крапки пагінації) — без Swiper/Slick.                            */
	/* ------------------------------------------------------------------ */
	function initReviewsSlider() {
		var slider = document.querySelector( '[data-reviews-slider]' );
		if ( ! slider ) {
			return;
		}

		var track = slider.querySelector( '[data-slider-track]' );
		var slides = slider.querySelectorAll( '[data-slider-slide]' );
		var dots   = slider.querySelectorAll( '[data-slider-dot]' );
		var prevBtn = slider.querySelector( '[data-slider-prev]' );
		var nextBtn = slider.querySelector( '[data-slider-next]' );

		if ( ! track || ! slides.length ) {
			return;
		}

		var current = 0;

		function goTo( index ) {
			current = ( index + slides.length ) % slides.length;
			track.style.transform = 'translateX(-' + ( current * 100 ) + '%)';

			dots.forEach( function ( dot, i ) {
				dot.classList.toggle( 'is-active', i === current );
			} );
		}

		if ( prevBtn ) {
			prevBtn.addEventListener( 'click', function () {
				goTo( current - 1 );
			} );
		}
		if ( nextBtn ) {
			nextBtn.addEventListener( 'click', function () {
				goTo( current + 1 );
			} );
		}

		dots.forEach( function ( dot, i ) {
			dot.addEventListener( 'click', function () {
				goTo( i );
			} );
		} );

		// Touch swipe.
		var touchStartX = 0;
		track.addEventListener( 'touchstart', function ( e ) {
			touchStartX = e.touches[0].clientX;
		}, { passive: true } );

		track.addEventListener( 'touchend', function ( e ) {
			var delta = e.changedTouches[0].clientX - touchStartX;
			if ( Math.abs( delta ) > 50 ) {
				goTo( delta < 0 ? current + 1 : current - 1 );
			}
		}, { passive: true } );

		goTo( 0 );
	}

	/* ------------------------------------------------------------------ */
	/* 9. Нова Пошта: автопідстановка міста/відділення через REST-проксі   */
	/* ------------------------------------------------------------------ */
	function initNovaPoshtaFields() {
		var form = document.getElementById( 'order-form' );
		if ( ! form || typeof svobodaData === 'undefined' ) {
			return;
		}

		var cityInput      = form.querySelector( '[data-np-city]' );
		var warehouseSelect = form.querySelector( '[data-np-warehouse]' );
		var paperCheckbox   = form.querySelector( 'input[data-variant="paper"]' );
		var npFieldsWrap    = form.querySelector( '[data-np-fields]' );

		if ( ! cityInput || ! warehouseSelect ) {
			return;
		}

		// Поля Нової Пошти потрібні лише коли обрана паперова версія.
		function toggleNpFields() {
			if ( npFieldsWrap ) {
				npFieldsWrap.hidden = ! ( paperCheckbox && paperCheckbox.checked );
			}
		}

		if ( paperCheckbox ) {
			paperCheckbox.addEventListener( 'change', toggleNpFields );
		}
		toggleNpFields();

		var debounceTimer;
		cityInput.addEventListener( 'input', function () {
			window.clearTimeout( debounceTimer );
			var query = cityInput.value.trim();
			if ( query.length < 2 ) {
				return;
			}

			debounceTimer = window.setTimeout( function () {
				fetch( svobodaData.restUrl + 'novaposhta/cities?query=' + encodeURIComponent( query ) )
					.then( function ( res ) { return res.json(); } )
					.then( function ( cities ) {
						// Спрощено: беремо перше знайдене місто, зберігаємо Ref в data-атрибут.
						if ( cities && cities.length ) {
							cityInput.setAttribute( 'data-city-ref', cities[0].Ref );
							loadWarehouses( cities[0].Ref );
						}
					} )
					.catch( function () { /* мовчазний фейл поля автопідстановки, форма лишається робочою вручну */ } );
			}, 350 );
		} );

		function loadWarehouses( cityRef ) {
			fetch( svobodaData.restUrl + 'novaposhta/warehouses?city_ref=' + encodeURIComponent( cityRef ) )
				.then( function ( res ) { return res.json(); } )
				.then( function ( warehouses ) {
					warehouseSelect.innerHTML = '';
					( warehouses || [] ).forEach( function ( wh ) {
						var option = document.createElement( 'option' );
						option.value = wh.Ref;
						option.textContent = wh.Description;
						warehouseSelect.appendChild( option );
					} );
					warehouseSelect.disabled = false;
				} )
				.catch( function () {
					warehouseSelect.disabled = true;
				} );
		}
	}

	/* ------------------------------------------------------------------ */
	/* 10. Замовлення: submit форми → create-invoice → редірект на оплату  */
	/* ------------------------------------------------------------------ */
	function svobodaShowOrderToast() {
		var toast = document.querySelector( '[data-order-toast]' );
		if ( ! toast ) {
			return;
		}
		toast.hidden = false;
		void toast.offsetHeight;
		toast.classList.add( 'is-visible' );
		window.setTimeout( function () {
			toast.classList.remove( 'is-visible' );
			window.setTimeout( function () { toast.hidden = true; }, 350 );
		}, 4000 );
	}

	function initOrderForm() {
		var form = document.getElementById( 'order-form' );
		if ( ! form ) {
			return;
		}

		var errorEl = form.querySelector( '[data-order-error]' );

		function showError( message, field ) {
			if ( errorEl ) {
				errorEl.textContent = message;
				errorEl.hidden = false;
			}
			if ( field ) {
				field.classList.add( 'is-invalid' );
			}
		}

		function clearErrors() {
			if ( errorEl ) {
				errorEl.hidden = true;
			}
			form.querySelectorAll( '.is-invalid' ).forEach( function ( el ) {
				el.classList.remove( 'is-invalid' );
			} );
		}

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			clearErrors();

			var checked = form.querySelectorAll( 'input[data-variant]:checked' );
			if ( ! checked.length ) {
				showError( 'Оберіть принаймні одну версію книги' );
				return;
			}

			var emailInput = form.querySelector( 'input[name="customer_email"]' );
			var email      = emailInput ? emailInput.value.trim() : '';
			if ( ! /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test( email ) ) {
				// Текст помилки — дослівно з макета (стан error модалки).
				showError( 'Переконайтесь, що пошта введена вірно', emailInput );
				return;
			}

			var payload = {
				customer_name:  ( form.querySelector( 'input[name="customer_name"]' ) || {} ).value || '',
				customer_phone: ( form.querySelector( 'input[name="customer_phone"]' ) || {} ).value || '',
				customer_email: email,
				payment:        ( form.querySelector( 'select[name="payment"]' ) || {} ).value || 'online',
				promo:          ( form.querySelector( 'input[name="promo"]' ) || {} ).value || '',
				np_city:        ( form.querySelector( 'input[name="np_city"]' ) || {} ).value || '',
				np_warehouse:   ( form.querySelector( 'select[name="np_warehouse"]' ) || {} ).value || '',
				qty_paper:      0,
				qty_ebook:      0,
			};

			checked.forEach( function ( box ) {
				var row = box.closest( '[data-variant-row]' );
				var qty = row ? parseInt( ( row.querySelector( '[data-qty-input]' ) || {} ).value || '1', 10 ) : 1;
				payload[ 'qty_' + box.getAttribute( 'data-variant' ) ] = qty;
			} );

			var submitBtn = form.querySelector( 'button[type="submit"]' );
			if ( submitBtn ) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Обробка...';
			}

			fetch( form.getAttribute( 'data-endpoint' ), {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': typeof svobodaData !== 'undefined' ? svobodaData.nonce : '',
				},
				body: JSON.stringify( payload ),
			} )
				.then( function ( res ) {
					return res.json().then( function ( data ) {
						return { ok: res.ok, data: data };
					} );
				} )
				.then( function ( result ) {
					if ( result.ok && result.data && result.data.pageUrl ) {
						window.location.href = result.data.pageUrl;
						return;
					}
					if ( result.ok && result.data && result.data.orderId ) {
						// Замовлення без онлайн-оплати: показуємо тост із макета.
						var modal = document.querySelector( '[data-order-modal]' );
						if ( modal ) {
							modal.hidden = true;
						}
						svobodaUnlockScroll();
						svobodaShowOrderToast();
						return;
					}
					throw new Error( 'order failed' );
				} )
				.catch( function () {
					showError( 'Не вдалося оформити замовлення. Спробуйте ще раз.' );
				} )
				.finally( function () {
					if ( submitBtn ) {
						submitBtn.disabled = false;
						submitBtn.textContent = 'Замовити';
					}
				} );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* 11. Замовлення: модалка (pill відкриває, X/фон/Escape закривають),  */
	/*     чекбокси варіантів + степери кількості + жива сума              */
	/* ------------------------------------------------------------------ */
	function initOrderModal() {
		var pills = document.querySelectorAll( '[data-order-open]' );
		var modal = document.querySelector( '[data-order-modal]' );
		if ( ! pills.length || ! modal ) {
			return;
		}

		var totalEl = modal.querySelector( '[data-order-total]' );

		function formatUah( amount ) {
			return '₴' + amount;
		}

		function recalc() {
			var total = 0;
			modal.querySelectorAll( '[data-variant-row]' ).forEach( function ( row ) {
				var box   = row.querySelector( 'input[data-variant]' );
				var price = parseInt( box.getAttribute( 'data-price' ), 10 ) || 0;
				var qtyEl = row.querySelector( '[data-qty-input]' );
				var qty   = parseInt( qtyEl ? qtyEl.value : '1', 10 ) || 1;
				var line  = row.querySelector( '[data-line-price]' );

				row.classList.toggle( 'is-checked', box.checked );
				if ( line ) {
					line.textContent = formatUah( price * ( box.checked ? qty : 1 ) );
				}
				if ( box.checked ) {
					total += price * qty;
				}
			} );
			if ( totalEl ) {
				totalEl.textContent = formatUah( total );
			}
		}

		function openModal() {
			modal.hidden = false;
			svobodaLockScroll();
			recalc();
		}

		function closeModal() {
			modal.hidden = true;
			svobodaUnlockScroll();
		}

		pills.forEach( function ( pill ) {
			pill.addEventListener( 'click', function () {
				var type = pill.getAttribute( 'data-order-open' );
				var box  = modal.querySelector( 'input[data-variant="' + type + '"]' );
				if ( box && ! box.checked ) {
					box.checked = true;
					box.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				}
				openModal();
			} );
		} );

		modal.querySelectorAll( '[data-order-close]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', closeModal );
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && ! modal.hidden ) {
				closeModal();
			}
		} );

		modal.querySelectorAll( 'input[data-variant]' ).forEach( function ( box ) {
			box.addEventListener( 'change', recalc );
		} );

		modal.querySelectorAll( '[data-variant-row]' ).forEach( function ( row ) {
			var qtyInput = row.querySelector( '[data-qty-input]' );
			var qtyLabel = row.querySelector( '[data-qty-label]' );

			function setQty( next ) {
				next = Math.min( 99, Math.max( 1, next ) );
				if ( qtyInput ) {
					qtyInput.value = String( next );
				}
				if ( qtyLabel ) {
					qtyLabel.textContent = next + 'шт';
				}
				recalc();
			}

			var minus = row.querySelector( '[data-qty-minus]' );
			var plus  = row.querySelector( '[data-qty-plus]' );

			if ( minus ) {
				minus.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					setQty( parseInt( qtyInput.value, 10 ) - 1 );
				} );
			}
			if ( plus ) {
				plus.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					setQty( parseInt( qtyInput.value, 10 ) + 1 );
				} );
			}
		} );

		var promoToggle = modal.querySelector( '[data-promo-toggle]' );
		var promoField  = modal.querySelector( '[data-promo-field]' );
		if ( promoToggle && promoField ) {
			promoToggle.addEventListener( 'click', function () {
				promoField.hidden = ! promoField.hidden;
			} );
		}

		recalc();
	}

	/* ------------------------------------------------------------------ */
	/* 12. Кастомні селекти: нативний popup <select> не стилізується.      */
	/*     Нативний select лишається прихованим носієм значення (NP-AJAX   */
	/*     наповнює його options), видимий UI — кнопка + власне меню, яке  */
	/*     перебудовується при кожному відкритті (динамічні options).      */
	/* ------------------------------------------------------------------ */
	function initCustomSelects() {
		var selects = document.querySelectorAll( '.order-form select' );
		if ( ! selects.length ) {
			return;
		}

		function closeAll() {
			document.querySelectorAll( '.dc-select.is-open' ).forEach( function ( openEl ) {
				openEl.classList.remove( 'is-open' );
				openEl.querySelector( '.dc-select__menu' ).hidden = true;
			} );
		}

		selects.forEach( function ( select ) {
			var wrap = document.createElement( 'div' );
			wrap.className = 'dc-select';
			select.parentNode.insertBefore( wrap, select );
			wrap.appendChild( select );

			var toggle = document.createElement( 'button' );
			toggle.type = 'button';
			toggle.className = 'dc-select__toggle';
			wrap.appendChild( toggle );

			var menu = document.createElement( 'div' );
			menu.className = 'dc-select__menu';
			menu.hidden = true;
			wrap.appendChild( menu );

			function labelOf() {
				var opt = select.options[ select.selectedIndex ];
				return opt ? opt.textContent : '';
			}

			function syncToggle() {
				toggle.textContent = labelOf();
				wrap.classList.toggle( 'is-disabled', select.disabled );
			}

			function rebuildMenu() {
				menu.replaceChildren();
				Array.prototype.forEach.call( select.options, function ( opt, i ) {
					if ( '' === opt.value && 0 === i && select.selectedIndex !== i ) {
						return; // порожній placeholder не дублюємо в меню
					}
					var item = document.createElement( 'button' );
					item.type = 'button';
					item.className = 'dc-select__option' + ( i === select.selectedIndex ? ' is-selected' : '' );
					item.textContent = opt.textContent;
					item.addEventListener( 'click', function () {
						select.selectedIndex = i;
						select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
						syncToggle();
						closeAll();
					} );
					menu.appendChild( item );
				} );
			}

			toggle.addEventListener( 'click', function () {
				if ( select.disabled ) {
					return;
				}
				var willOpen = menu.hidden;
				closeAll();
				if ( willOpen ) {
					rebuildMenu();
					menu.hidden = false;
					wrap.classList.add( 'is-open' );
				}
			} );

			// NP-AJAX вмикає/наповнює select — тримаємо кнопку синхронною.
			var observer = new MutationObserver( syncToggle );
			observer.observe( select, { attributes: true, childList: true } );
			select.addEventListener( 'change', syncToggle );

			syncToggle();
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! e.target.closest( '.dc-select' ) ) {
				closeAll();
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				closeAll();
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* 13. Hero: літак летить униз-уперед по скролу (десктоп, не reduced)   */
	/* ------------------------------------------------------------------ */
	function initHeroPlaneAnimation() {
		var fly   = document.querySelector( '[data-hero-fly]' );
		var paper = document.querySelector( '.section--hero__paper' );
		var cover = document.querySelector( '.section--order__cover img' );
		if ( ! fly ) {
			return;
		}

		var desktopMq = window.matchMedia( '(min-width: 993px)' );
		var reduceMq  = window.matchMedia( '(prefers-reduced-motion: reduce)' );

		// Посадкове місце на обкладинці = зона стертого друкованого літака
		// (виміряно з book-cover.png 1596x2240): x 478..1444, центр y 977.
		var TARGET = { fx: 0.2995, fw: 0.6053, fyc: 0.4362 };
		var SPRITE_RATIO = 1802 / 560;

		var anim = null;
		var ticking = false;

		function easeInOut( t ) {
			return t < 0.5 ? 2 * t * t : 1 - Math.pow( -2 * t + 2, 2 ) / 2;
		}

		// Запуск burst-in анімації при завантаженні (якщо ми на самому верху сторінки)
		function startBurstAnimation() {
			if ( ! fly.animate ) {
				fly.classList.add( 'is-floating' );
				return;
			}

			// Очищуємо inline стилі на всяк випадок перед стартом
			fly.style.transform = '';

			var flyRect = fly.getBoundingClientRect();
			var sx, sy;
			if ( paper ) {
				var pr = paper.getBoundingClientRect();
				// Позиціонуємо виліт глибше з дірки розриву паперу
				sx = pr.left + pr.width * 0.56 - flyRect.left;
				sy = pr.top + pr.height * 0.43 - flyRect.top;
			} else {
				sx = -flyRect.width * 0.5;
				sy = flyRect.height * 0.43;
			}

			var p1x = sx * 0.55, p1y = sy - flyRect.height * 0.9;
			var p2x = flyRect.width * 0.06, p2y = -flyRect.height * 0.35;

			var STEPS = 45;
			var frames = [];
			for ( var i = 0; i <= STEPS; i++ ) {
				var t = i / STEPS;
				var mt = 1 - t;
				var bx = mt * mt * mt * sx + 3 * mt * mt * t * p1x + 3 * mt * t * t * p2x;
				var by = mt * mt * mt * sy + 3 * mt * mt * t * p1y + 3 * mt * t * t * p2y;
				var dxdt = 3 * mt * mt * ( p1x - sx ) + 6 * mt * t * ( p2x - p1x ) + 3 * t * t * ( 0 - p2x );
				var dydt = 3 * mt * mt * ( p1y - sy ) + 6 * mt * t * ( p2y - p1y ) + 3 * t * t * ( 0 - p2y );
				var ang = Math.atan2( dydt, dxdt ) * 180 / Math.PI;
				ang = Math.max( -14, Math.min( 14, ang ) ) * ( 1 - t * t * t );
				// Починаємо з меншого масштабу (0.12 замість 0.3), створюючи ефект вильоту з глибини
				var sc = 0.12 + 0.88 * t;
				frames.push( {
					transform: 'translate3d(' + bx + 'px,' + by + 'px,0) rotate(' + ang + 'deg) scale(' + sc + ')',
					opacity: t < 0.08 ? t / 0.08 : 1,
					easing: 'ease-out',
				} );
			}

			anim = fly.animate( frames, {
				duration: 1500,
				delay: 180,
				fill: 'both',
				easing: 'cubic-bezier(0.25, 0.7, 0.3, 1)',
			} );

			anim.onfinish = function () {
				fly.classList.add( 'is-floating' );
				anim = null;
			};
		}

		function update() {
			ticking = false;

			if ( ! desktopMq.matches || reduceMq.matches ) {
				fly.style.transform = '';
				fly.classList.remove( 'is-floating' );
				if ( anim ) {
					anim.cancel();
					anim = null;
				}
				return;
			}

			var scrollY = window.scrollY;

			// Якщо користувач знаходиться на самому верху сторінки і анімація burst-in запущена чи очікується,
			// ми даємо їй пріоритет.
			if ( scrollY <= 5 ) {
				if ( anim ) {
					// анімація ще відтворюється
					return;
				}
				// Якщо анімації немає, вмикаємо floating
				fly.style.transform = '';
				fly.style.opacity = '';
				fly.classList.add( 'is-floating' );
				return;
			}

			// Якщо користувач проскролив вниз більше ніж на 5px
			if ( anim ) {
				anim.cancel();
				anim = null;
			}
			fly.classList.remove( 'is-floating' );

			// Траєкторія відльоту літака за межі екрана при скролі
			var hero = document.getElementById( 'hero' );
			var heroHeight = hero ? hero.offsetHeight : window.innerHeight;
			
			// Прогрес польоту: 0 нагорі -> 1, коли користувач проскролив всю висоту Hero
			var p = Math.min( 1, Math.max( 0, scrollY / heroHeight ) );
			var e = easeInOut( p );

			var vw = window.innerWidth;
			var vh = window.innerHeight;

			// Траєкторія відльоту літака: летить вгору-вправо, повністю залишаючись у Hero секції
			// і зникаючи дуже швидко, щоб не перетинати нижчі секції (Order тощо)
			var dx = vw * 0.55;
			var dy = -vh * 0.08; // Помірний плавний підйом вгору, як на ескізі користувача

			var p1x = dx * 0.25, p1y = dy * 0.20; // Пропорційно до dy, щоб уникнути різкого стрибка вгору
			var p2x = dx * 0.70, p2y = dy * 0.75;

			var t = e, mt = 1 - t;
			var bx = 3 * mt * mt * t * p1x + 3 * mt * t * t * p2x + t * t * t * dx;
			var by = 3 * mt * mt * t * p1y + 3 * mt * t * t * p2y + t * t * t * dy;

			// Розрахунок кута нахилу за дотичною
			var dxdt = 3 * mt * mt * p1x + 6 * mt * t * ( p2x - p1x ) + 3 * t * t * ( dx - p2x );
			var dydt = 3 * mt * mt * p1y + 6 * mt * t * ( p2y - p1y ) + 3 * t * t * ( dy - p2y );
			var ang = Math.atan2( dydt, dxdt ) * 180 / Math.PI;
			ang = Math.max( -22, Math.min( 22, ang ) );
			ang *= Math.min( 1, p * 6 ) * ( 1 - Math.pow( t, 3 ) );

			var scale = 1 - p * 0.4;
			
			// Плавне зникнення: повністю згасає до 30% прокрутки Hero
			var opacity = Math.max( 0, 1 - p / 0.3 );

			fly.style.transform = 'translate3d(' + bx + 'px,' + by + 'px,0) rotate(' + ang + 'deg) scale(' + scale + ')';
			fly.style.opacity = String( opacity );
		}

		// Запускаємо burst, якщо сторінка завантажена на початку
		if ( window.scrollY <= 5 && desktopMq.matches && ! reduceMq.matches ) {
			startBurstAnimation();
		} else {
			update();
		}

		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( update );
			}
		}, { passive: true } );

		window.addEventListener( 'resize', function () {
			update();
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Init                                                                 */
	/* ------------------------------------------------------------------ */
	function init() {
		initHeaderScroll();
		initMobileMenu();
		initFadeIn();
		initTilt();
		initScrollLine();
		initParallaxLayers();
		initParallaxBg();
		initVideoBlock();
		initReviewsSlider();
		initNovaPoshtaFields();
		initOrderForm();
		initOrderModal();
		initCustomSelects();
		initHeroPlaneAnimation();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();
