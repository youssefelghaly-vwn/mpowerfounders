{{--
    Site navbar + full-screen mobile nav. Self-contained: its own markup
    and its own behavior (scroll morph, mobile nav open/close, magnetic
    link indicator) live together here so <x-nav /> can be dropped into
    the layout (or any page) on its own.
--}}
@props([
    'links' => [
        ['href' => '#how',       'label' => 'Platform', 'active' => true],
        ['href' => '#spotlight', 'label' => 'Founders'],
        ['href' => '#metrics',   'label' => 'Metrics'],
        ['href' => '#proof',     'label' => 'Proof'],
        ['href' => '#apply',     'label' => 'Apply'],
    ],
    'ctaHref'  => '#apply',
    'ctaLabel' => 'Apply now',
])

<nav class="navbar" id="navbar">
  <a href="#" class="navbar__logo"><span class="navbar__mark"></span>MPOWER<span>&middot;</span>FOUNDERS</a>

  <div class="navbar__links" id="navLinks">
    <div class="navbar__indicator" id="navIndicator"></div>
    @foreach ($links as $link)
      <a href="{{ $link['href'] }}" class="navbar__link @if(!empty($link['active'])) is-active @endif" data-cursor="hover">{{ $link['label'] }}</a>
    @endforeach
  </div>

  <div class="navbar__cta">
    <a href="{{ $ctaHref }}" class="btn-gold" data-cursor="hover">{{ $ctaLabel }}
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
    </a>
    <button class="navbar__burger" id="burgerBtn" aria-label="Open menu"><span></span><span></span><span></span></button>
  </div>
</nav>

<div class="mobile-nav" id="mobileNav">
  <button class="mobile-nav__close" id="mobileClose" aria-label="Close menu">&#10005;</button>
  @foreach ($links as $link)
    <a href="{{ $link['href'] }}" class="mobile-nav__link">{{ $link['label'] }}</a>
  @endforeach
  <div class="mobile-nav__foot">MPOWER FOUNDERS &mdash; Est. 2026</div>
</div>

<script>
(function(){
  if (typeof gsap === 'undefined') return;
  var reduceMotion = window.reduceMotion;

  /* navbar morph on scroll */
  var navbar = document.getElementById('navbar');
  if (navbar && typeof ScrollTrigger !== 'undefined') {
    ScrollTrigger.create({
      start: 'top -80',
      onEnter: function(){ navbar.classList.add('is-scrolled'); },
      onLeaveBack: function(){ navbar.classList.remove('is-scrolled'); }
    });
  }

  /* magnetic nav indicator */
  var indicator = document.getElementById('navIndicator');
  var navLinksWrap = document.getElementById('navLinks');
  if (indicator && navLinksWrap) {
    var links = navLinksWrap.querySelectorAll('.navbar__link');
    function moveIndicator(el){
      var wrapRect = navLinksWrap.getBoundingClientRect();
      var r = el.getBoundingClientRect();
      gsap.to(indicator, {x: r.left - wrapRect.left, width: r.width, opacity:1, duration:.45, ease:'power3.out'});
    }
    links.forEach(function(link){
      link.addEventListener('mouseenter', function(){ moveIndicator(link); });
    });
    navLinksWrap.addEventListener('mouseleave', function(){
      gsap.to(indicator, {opacity:0, duration:.3});
    });
  }

  /* mobile nav open/close */
  var mobileNav = document.getElementById('mobileNav');
  var burger = document.getElementById('burgerBtn');
  var closeBtn = document.getElementById('mobileClose');
  var mobileLinks = mobileNav ? mobileNav.querySelectorAll('.mobile-nav__link') : [];

  function openMobile(){
    mobileNav.classList.add('is-open');
    gsap.to(mobileLinks, {opacity:1, y:0, duration:.5, stagger:.06, delay:.25, ease:'power3.out'});
    gsap.to('.mobile-nav__foot', {opacity:1, duration:.5, delay:.5});
  }
  function closeMobile(){
    mobileNav.classList.remove('is-open');
    gsap.set(mobileLinks, {opacity:0, y:24});
    gsap.set('.mobile-nav__foot', {opacity:0});
  }
  if (burger) burger.addEventListener('click', openMobile);
  if (closeBtn) closeBtn.addEventListener('click', closeMobile);
  mobileLinks.forEach(function(l){ l.addEventListener('click', closeMobile); });
})();
</script>