	function initHeroPlaneFly() {
		var fly   = document.querySelector( '[data-hero-fly]' );
		var cover = document.querySelector( '.section--order__cover img' );
		if ( ! fly ) {
			return;
		}

		// Посадкове місце на обкладинці = зона стертого друкованого літака
		// (виміряно з book-cover.png 1596x2240): x 478..1444, центр y 977.
		var TARGET = { fx: 0.2995, fw: 0.6053, fyc: 0.4362 };
		var SPRITE_RATIO = 1802 / 560;

		var desktopMq = window.matchMedia( '(min-width: 993px)' );
		var reduceMq  = window.matchMedia( '(prefers-reduced-motion: reduce)' );
		var ticking   = false;

		function easeInOut( t ) {
			return t < 0.5 ? 2 * t * t : 1 - Math.pow( -2 * t + 2, 2 ) / 2;
		}

		function update() {
			ticking = false;

			if ( ! desktopMq.matches || reduceMq.matches ) {
				fly.style.transform = '';
				return;
			}

			// База = layout-позиція спрайта (transform не впливає на layout).
			var prev = fly.style.transform;
			fly.style.transform = '';
			var base = fly.getBoundingClientRect();
			fly.style.transform = prev;

			var baseX = base.left + window.scrollX;
			var baseY = base.top + window.scrollY;

			if ( ! cover ) {
				// Без обкладинки (секцію вимкнено) -- просто відлітає вниз-уперед.
				var hero = document.getElementById( 'hero' );
				var ph = Math.min( 1, window.scrollY / ( hero ? hero.offsetHeight : 1000 ) );
				fly.style.transform = 'translate3d(' + ( ph * 36 ) + 'vw,' + ( ph * 80 ) + 'vh,0)';
				return;
			}

			var cRect = cover.getBoundingClientRect();
			var coverX = cRect.left + window.scrollX;
			var coverY = cRect.top + window.scrollY;

			// Ціль: спрайт шириною зони друкованого літака, центр по її центру.
			var targetW = cRect.width * TARGET.fw;
			var targetH = targetW / SPRITE_RATIO;
			var targetX = coverX + cRect.width * TARGET.fx;
			var targetY = coverY + cRect.height * TARGET.fyc - targetH / 2;

			// Прогрес: 0 нагорі сторінки -> 1, коли центр обкладинки в центрі екрана.
			var endScroll = ( coverY + cRect.height / 2 ) - window.innerHeight / 2;
			var p = endScroll > 0 ? Math.min( 1, Math.max( 0, window.scrollY / endScroll ) ) : 0;
			var e = easeInOut( p );

			// Траєкторія -- кубічна Безьє (не пряма діагональ): виліт з отвору
			// трохи ВГОРУ (P1 над стартом), планерування, пікірування нижче лінії
			// посадки (P2 під ціллю) і фінальний підліт-вирівнювання на книжку.
			var dx = targetX - baseX;
			var dy = targetY - baseY;
			var vh = window.innerHeight;

			var p1x = dx * 0.30, p1y = -vh * 0.16;
			var p2x = dx * 0.72, p2y = dy + vh * 0.10;

			var t = e, mt = 1 - t;
			var bx = 3 * mt * mt * t * p1x + 3 * mt * t * t * p2x + t * t * t * dx;
			var by = 3 * mt * mt * t * p1y + 3 * mt * t * t * p2y + t * t * t * dy;

			// Ніс за дотичною кривої (банкування по курсу), з вирівнюванням:
			// на старті (ще не летимо) і при посадці кут гаситься до нуля.
			var dxdt = 3 * mt * mt * p1x + 6 * mt * t * ( p2x - p1x ) + 3 * t * t * ( dx - p2x );
			var dydt = 3 * mt * mt * p1y + 6 * mt * t * ( p2y - p1y ) + 3 * t * t * ( dy - p2y );
			var ang = Math.atan2( dydt, dxdt ) * 180 / Math.PI;
			ang = Math.max( -16, Math.min( 16, ang ) );
			ang *= Math.min( 1, p * 6 ) * ( 1 - Math.pow( t, 3 ) );

			var scale = 1 + ( targetW / base.width - 1 ) * e;

			fly.style.transform =
				'translate3d(' + bx + 'px,' + by + 'px,0) rotate(' + ang + 'deg) scale(' + scale + ')';
		}

		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( update );
			}
		}, { passive: true } );

		window.addEventListener( 'resize', update );
		update();
	}
