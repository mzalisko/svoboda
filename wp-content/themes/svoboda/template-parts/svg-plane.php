<?php
/**
 * Кастомний SVG паперового літачка, перевикористовується в Hero і в секції
 * "Трансформація" (де він "долітає" до планети).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" class="svg-plane">
	<path d="M5,50 L95,20 L60,50 L95,80 Z" fill="var(--bg-base)" stroke="var(--black)" stroke-width="1.5" stroke-linejoin="round"/>
	<path d="M60,50 L95,20 L60,50 L95,80 Z" fill="var(--bg-alt)" stroke="var(--black)" stroke-width="1.5" stroke-linejoin="round"/>
</svg>
