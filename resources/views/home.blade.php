<x-app-layout title="MPower Founders — Founder voice, built to travel">

<!-- HERO -->
<header class="hero">
  <div class="hero__bg"></div>
  <div class="hero__grid-lines"></div>

  <div class="wrap hero__content">
    <div class="hero__eyebrow eyebrow">Founder media, not founder content</div>

    <h1 class="hero__title">
      <div class="line"><span>Founders build</span></div>
      <div class="line"><span>companies. <em class="accent">We</em></span></div>
      <div class="line"><span>build their <em>reach.</em></span></div>
    </h1>

    <div class="hero__row">
      <p class="hero__sub reveal">MPower Founders turns your raw podcast sit-downs and keynote moments into clips built to travel — cut, placed, and tracked, while you get back to building.</p>
      <div class="hero__actions reveal">
        <a href="#apply" class="link-underline" data-cursor="hover">Apply as a founder &rarr;</a>
        <a href="#how" class="link-underline" data-cursor="hover">See how it works</a>
      </div>
    </div>
  </div>

  <div class="hero__card" id="heroCard">
    <div class="hero__card-thumb">
      <img class="hero__card-img" src="https://assets.lummi.ai/assets/QmciKoyLeSxMNmq2bvntSEh6MuWWBwG4PfyqQ9aF5VwoTP?auto=format&amp;w=700" alt="Illustrated portrait of a founder" loading="lazy">
      <span class="hero__card-tag">Podcast clip &middot; 0:47</span>
      <span class="hero__play"><svg viewBox="0 0 24 24"><path d="M6 4l14 8-14 8V4z"/></svg></span>
    </div>
    <div class="hero__card-meta">
      <div>
        <div class="hero__card-name">D. Kessler</div>
        <div class="hero__card-role">Fintech &middot; Seed</div>
      </div>
      <div class="hero__card-views">&uarr; <span id="cardCounter">0</span>K</div>
    </div>
  </div>

  <div class="hero__scroll">
    <div class="hero__scroll-line"></div>
    Scroll
  </div>
</header>

<!-- TICKER -->
<div class="ticker-section">
  <div class="ticker-track" id="tickerTrack">
    <span class="grp">
      <span class="ticker-item"><span class="ticker-dot"></span> <b>1,204</b> founders onboarded</span>
      <span class="ticker-item"><span class="up">&uarr; 312%</span> avg. 90-day reach</span>
      <span class="ticker-item"><b>2.1B</b> clip views generated</span>
      <span class="ticker-item">48 industries represented</span>
      <span class="ticker-item"><span class="up">&uarr; 44%</span> avg. audience growth / mo</span>
      <span class="ticker-item"><b>$2.8B</b> combined portfolio value</span>
    </span>
    <span class="grp" aria-hidden="true">
      <span class="ticker-item"><span class="ticker-dot"></span> <b>1,204</b> founders onboarded</span>
      <span class="ticker-item"><span class="up">&uarr; 312%</span> avg. 90-day reach</span>
      <span class="ticker-item"><b>2.1B</b> clip views generated</span>
      <span class="ticker-item">48 industries represented</span>
      <span class="ticker-item"><span class="up">&uarr; 44%</span> avg. audience growth / mo</span>
      <span class="ticker-item"><b>$2.8B</b> combined portfolio value</span>
    </span>
  </div>
</div>

<!-- HOW IT WORKS -->
<section class="section how" id="how">
  <div class="wrap">
    <div class="section-head">
      <div>
        <div class="eyebrow" style="margin-bottom:18px;">The process</div>
        <h2 class="section-title reveal">Three steps. No production team required.</h2>
      </div>
      <p class="section-desc reveal">You talk. We handle everything between the raw footage and the audience that's actually watching.</p>
    </div>

    <div class="how__list">
      <div class="how__step reveal">
        <div class="how__num">01</div>
        <div class="how__name-row">
          <span class="how__icon"><svg viewBox="0 0 32 32"><use href="#i-mic"/></svg></span>
          <div class="how__name">Record</div>
        </div>
        <div class="how__body">Send us your raw footage — a podcast sit-down, a keynote, a hallway conversation. Phone footage is fine. No production required on your end.</div>
      </div>
      <div class="how__step reveal">
        <div class="how__num">02</div>
        <div class="how__name-row">
          <span class="how__icon"><svg viewBox="0 0 32 32"><use href="#i-broadcast"/></svg></span>
          <div class="how__name">Distribute</div>
        </div>
        <div class="how__body">Our team cuts it into clips built for the way each platform actually gets watched, then places them where your specific audience already spends time.</div>
      </div>
      <div class="how__step reveal">
        <div class="how__num">03</div>
        <div class="how__name-row">
          <span class="how__icon"><svg viewBox="0 0 32 32"><use href="#i-growth"/></svg></span>
          <div class="how__name">Compound</div>
        </div>
        <div class="how__body">Every clip reports back — reach, retention, who's watching — so your presence compounds instead of resetting every time you post.</div>
      </div>
    </div>
  </div>
</section>

<!-- SPOTLIGHT -->
<section class="section spotlight" id="spotlight">
  <div class="wrap">
    <div class="section-head">
      <div>
        <div class="eyebrow" style="margin-bottom:18px;">Founder spotlight</div>
        <h2 class="section-title reveal">Currently compounding</h2>
      </div>
      <p class="section-desc reveal">A sample of founders whose back-catalogue of conversations is now doing the work of a full-time content team.</p>
    </div>

    @php
      $spotlights = [
        ['variant' => 'gold', 'img' => 'QmPi5pHScBVbgq45GreEcvh8bryprcpQKPFQQDX7Vh1g9o', 'tag' => 'Keynote clip', 'name' => 'R. Adeyemi', 'role' => 'Logistics · Series A', 'views' => '890K views', 'growth' => '↑ 268%'],
        ['variant' => 'sage', 'img' => 'QmcCMMMaTJ3fRSFCYp6DZD5uHapWxffZRgjB8Wodwss8hd', 'tag' => 'Podcast clip', 'name' => 'L. Moreau', 'role' => 'Climate · Seed', 'views' => '1.4M views', 'growth' => '↑ 401%'],
        ['variant' => 'paper', 'img' => 'QmXapKUCS1XjpQ8bYFX2xuHBJtHnf5GjHz87WZXEEp5KwX', 'tag' => 'Panel clip', 'name' => 'S. Okafor', 'role' => 'Healthtech · Series B', 'views' => '2.1M views', 'growth' => '↑ 189%'],
        ['variant' => 'sage', 'img' => 'Qmcfk726zK7fkrDwEtDjWkeg7PqZWbo21vgVM1PLUe2zXE', 'tag' => 'Podcast clip', 'name' => 'T. Bergström', 'role' => 'Devtools · Seed', 'views' => '612K views', 'growth' => '↑ 522%'],
        ['variant' => 'gold', 'img' => 'QmWuvkwWDboBeWvU3pGyroSsCzHxaah4ih21H6YRo9NQsZ', 'tag' => 'Fireside clip', 'name' => 'N. Haddad', 'role' => 'Fintech · Series A', 'views' => '3.3M views', 'growth' => '↑ 244%'],
        ['variant' => 'paper', 'img' => 'QmTaDWVvY87xYx184XZzcFdyd9C8e1E9TxSkdjY7EZb6MB', 'tag' => 'Podcast clip', 'name' => 'A. Petrova', 'role' => 'AI infra · Seed', 'views' => '1.9M views', 'growth' => '↑ 356%'],
      ];
    @endphp

    <div class="spot-grid" id="spotGrid">
      @foreach ($spotlights as $s)
        <div class="spot-card spot-card--{{ $s['variant'] }} reveal" data-cursor="hover">
          <img class="spot-card__img" src="https://assets.lummi.ai/assets/{{ $s['img'] }}?auto=format&amp;w=600" alt="Illustrated portrait of founder {{ $s['name'] }}" loading="lazy">
          <div class="spot-card__scrim"></div>
          <div class="spot-card__top"><span class="spot-card__tag">{{ $s['tag'] }}</span><span class="spot-card__play"><svg viewBox="0 0 24 24"><path d="M6 4l14 8-14 8V4z"/></svg></span></div>
          <div class="spot-card__bottom">
            <div class="spot-card__name">{{ $s['name'] }}</div>
            <div class="spot-card__role">{{ $s['role'] }}</div>
            <div class="spot-card__stats"><span class="spot-card__views">{{ $s['views'] }}</span><span class="spot-card__growth">{{ $s['growth'] }}</span></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- METRICS -->
<section class="section metrics" id="metrics">
  <div class="wrap">
    <div class="section-head">
      <div>
        <div class="eyebrow" style="margin-bottom:18px;">By the numbers</div>
        <h2 class="section-title reveal">A network that already moves.</h2>
      </div>
    </div>

    <div class="metrics-grid">
      <div class="metric reveal">
        <div class="metric__num"><span class="count" data-target="1200">0</span><span class="unit">+</span></div>
        <div class="metric__label">Founders on the network</div>
      </div>
      <div class="metric reveal">
        <div class="metric__num"><span class="count" data-target="2100">0</span><span class="unit">M</span></div>
        <div class="metric__label">Cumulative clip views</div>
      </div>
      <div class="metric reveal">
        <div class="metric__num"><span class="count" data-target="340">0</span><span class="unit">%</span></div>
        <div class="metric__label">Avg. reach growth, 6 months</div>
      </div>
      <div class="metric reveal">
        <div class="metric__num"><span class="count" data-target="48">0</span></div>
        <div class="metric__label">Industries represented</div>
      </div>
    </div>
  </div>
</section>

<!-- CLIENT PROOF -->
<section class="section proof" id="proof">
  <div class="wrap">
    <div class="section-head">
      <div>
        <div class="eyebrow" style="margin-bottom:18px;">Client proof</div>
        <h2 class="section-title reveal">Numbers, not testimonials.</h2>
      </div>
      <p class="section-desc reveal">A sample of the creators and public figures this team has edited for directly, with the actual clips and the view counts behind them.</p>
    </div>

    <div class="proof__list">
      <div class="proof__row reveal">
        <div class="proof__name-col">
          <div class="proof__name">Lewis Howes</div>
          <div class="proof__meta">2.5 years · 800M+ views · 1.9M → 4.9M followers</div>
        </div>
        <div class="proof__body-col">
          <p class="proof__desc">I worked with Lewis for 2.5 years. During that time, my edits generated over 800M views. When we started, he had 1.9M followers, and today he has 4.9M. One of my clips reached 23M views, and many others passed 5M views.</p>
          <div class="proof__clips">
            <a href="https://www.youtube.com/shorts/I1PlTex-Dc0" target="_blank" rel="noopener" class="proof__clip" data-cursor="hover">
              <span class="proof__clip-title">You DON'T Have to Cut Out Sugar...</span>
              <span class="proof__clip-views">23M</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
            </a>
            <a href="https://www.youtube.com/shorts/4VbXOmG-8Q8" target="_blank" rel="noopener" class="proof__clip" data-cursor="hover">
              <span class="proof__clip-title">When Money is Lacking... | Dean Graziosi</span>
              <span class="proof__clip-views">10.9M</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
            </a>
            <a href="https://www.youtube.com/shorts/f2PXHvFUTaI" target="_blank" rel="noopener" class="proof__clip" data-cursor="hover">
              <span class="proof__clip-title">Eat These 5 Foods! | Dr. Will Bulsiewicz</span>
              <span class="proof__clip-views">4.6M</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
            </a>
          </div>
        </div>
      </div>

      <div class="proof__row reveal">
        <div class="proof__name-col">
          <div class="proof__name">Tai Lopez</div>
          <div class="proof__meta">500M+ views generated</div>
        </div>
        <div class="proof__body-col">
          <p class="proof__desc">I edited content for Tai that generated over 500M views. One clip reached 23M views, and many others got over 5M views.</p>
          <div class="proof__clips">
            <a href="https://www.facebook.com/reel/1785292172120149" target="_blank" rel="noopener" class="proof__clip" data-cursor="hover">
              <span class="proof__clip-title">Facebook Reel — Clip 1</span>
              <span class="proof__clip-views">25M FB · 18M IG · 11M TT</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
            </a>
            <a href="https://www.facebook.com/reel/2133299934187779" target="_blank" rel="noopener" class="proof__clip" data-cursor="hover">
              <span class="proof__clip-title">Facebook Reel — Clip 2</span>
              <span class="proof__clip-views">6.4M</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
            </a>
            <a href="https://www.facebook.com/reel/1435295087884482" target="_blank" rel="noopener" class="proof__clip" data-cursor="hover">
              <span class="proof__clip-title">Facebook Reel — Clip 3</span>
              <span class="proof__clip-views">2.5M</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
            </a>
          </div>
        </div>
      </div>

      <div class="proof__row reveal">
        <div class="proof__name-col">
          <div class="proof__name">David Meltzer</div>
          <div class="proof__meta">3 years · millions of views</div>
        </div>
        <div class="proof__body-col">
          <p class="proof__desc">I worked with David for 3 years, editing clips that got millions of views across his social media.</p>
          <div class="proof__clips">
            <a href="https://www.instagram.com/reel/C-_PsvEPf7V/" target="_blank" rel="noopener" class="proof__clip" data-cursor="hover">
              <span class="proof__clip-title">Instagram Reel — Clip 1</span>
              <span class="proof__clip-views">1.1M</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
            </a>
            <a href="https://www.instagram.com/reel/DAyQdgmRAZ1/" target="_blank" rel="noopener" class="proof__clip" data-cursor="hover">
              <span class="proof__clip-title">Instagram Reel — Clip 2</span>
              <span class="proof__clip-views">4.7M</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
            </a>
          </div>
        </div>
      </div>

      <div class="proof__row reveal">
        <div class="proof__name-col">
          <div class="proof__name">Logan Cuffari</div>
          <div class="proof__meta">0 → 7K subscribers in 2 months</div>
        </div>
        <div class="proof__body-col">
          <p class="proof__desc">I helped Logan grow his YouTube channel from 0 to 7K subscribers in just 2 months. I also edited ads for his webinars.</p>
        </div>
      </div>

      <div class="proof__row reveal">
        <div class="proof__name-col">
          <div class="proof__name">Marczell Klein</div>
          <div class="proof__meta">500K+ views</div>
        </div>
        <div class="proof__body-col">
          <p class="proof__desc">I worked with Marczell for a short time, editing clips that reached 500K+ views.</p>
        </div>
      </div>

      <div class="proof__row reveal">
        <div class="proof__name-col">
          <div class="proof__name">Sean Kelly</div>
          <div class="proof__meta">3 months · millions of views</div>
        </div>
        <div class="proof__body-col">
          <p class="proof__desc">I worked with Sean for 3 months, editing content that generated millions of views.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIAL -->
<section class="testimonial">
  <div class="testimonial__mark">&ldquo;</div>
  <div class="wrap">
    <p class="testimonial__quote reveal">We stopped treating content as a chore and started treating it as a compounding asset — without adding a single person to the team.</p>
    <div class="testimonial__foot reveal">
      <img class="testimonial__avatar-img" src="https://assets.lummi.ai/assets/QmXTgga3jVMW3YVjjec42QZq9VwXGsUHQFSiWjuxXnd975?auto=format&amp;w=200" alt="Illustrated portrait of a founder" loading="lazy">
      <div>
        <div class="testimonial__name">Placeholder Founder</div>
        <div class="testimonial__role">Swap in a real quote here — Seed-stage, B2B SaaS</div>
      </div>
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section class="cta-final" id="apply">
  <div class="wrap">
    <div class="cta-final__illo reveal">
      <img src="https://assets.lummi.ai/assets/QmULXhoHvfb6h8dRbfjm7SMTi65xTw4rR1A8HSCz4uebQE?auto=format&amp;w=300" alt="Illustration of two founders in conversation" loading="lazy">
    </div>
    <h2 class="cta-final__title reveal">Let's make your <span class="accent">next clip</span><br>your best-performing one.</h2>
    <p class="cta-final__sub reveal">Applications are reviewed by the team, not a form. We take a limited number of founders per quarter.</p>
    <div class="cta-final__actions reveal">
      {{-- The apply funnel actually lands somewhere now: registration
           creates a pending account for the team to review. --}}
      @auth
        <a href="{{ auth()->user()->dashboardUrl() }}" class="btn-gold lg" data-cursor="hover">Go to your dashboard
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
        </a>
      @else
        <a href="{{ route('register') }}" class="btn-gold lg" data-cursor="hover">Apply as a founder
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M7 17L17 7M7 7h10v10"/></svg>
        </a>
        <a href="{{ route('login') }}" class="btn-ghost lg" data-cursor="hover">Sign in</a>
      @endauth
    </div>
  </div>
</section>

<x-slot:scripts>
<script>
(function(){
  if (typeof gsap === 'undefined') {
    document.querySelectorAll('.hero__title .line span').forEach(function(el){
      el.style.opacity = 1; el.style.transform = 'none';
    });
    return;
  }

  /* hero image magnetic tilt */
  var illo = document.querySelector('.hero__card-img');
  if (illo && window.matchMedia('(pointer:fine)').matches && !window.reduceMotion){
    var ix = gsap.quickTo(illo, "x", {duration:0.8, ease:"power3.out"});
    var iy = gsap.quickTo(illo, "y", {duration:0.8, ease:"power3.out"});
    window.addEventListener('mousemove', function(e){
      var mx = (e.clientX / window.innerWidth - 0.5) * 14;
      var my = (e.clientY / window.innerHeight - 0.5) * 14;
      ix(mx); iy(my);
    });
  }

  /* hero load-in timeline */
  var tl = gsap.timeline({defaults:{ease:'power4.out'}});
  gsap.set('.hero__row .reveal', {opacity:0, y:20});
  gsap.set('.hero__scroll', {opacity:0});
  tl.to('.hero__title .line span', {y:'0%', duration:1, stagger:.12})
    .to('.hero__row .reveal', {opacity:1, y:0, duration:.8, stagger:.12}, '-=.5')
    .to('.hero__card', {opacity:1, duration:.9}, '-=.7')
    .to('.hero__scroll', {opacity:1, duration:.6}, '-=.5');

  /* hero card float + counter */
  gsap.to('#heroCard', {y:-14, duration:2.6, ease:'sine.inOut', repeat:-1, yoyo:true, delay:1.4});
  var cardCounterEl = document.getElementById('cardCounter');
  if (cardCounterEl) {
    var cardCounterObj = {v:0};
    gsap.to(cardCounterObj, {
      v: 428, duration: 2, delay: 1.3, ease:'power2.out',
      onUpdate: function(){ cardCounterEl.textContent = Math.round(cardCounterObj.v); }
    });
  }

  /* hero parallax on scroll */
  gsap.to('.hero__grid-lines', {yPercent: 18, ease:'none', scrollTrigger:{trigger:'.hero', start:'top top', end:'bottom top', scrub:true}});
  gsap.to('.hero__card', {yPercent: -12, ease:'none', scrollTrigger:{trigger:'.hero', start:'top top', end:'bottom top', scrub:true}});

  /* ticker marquee */
  var track = document.getElementById('tickerTrack');
  if (track && !window.reduceMotion){
    var tickerTween = gsap.to(track, {xPercent:-50, duration:26, ease:'none', repeat:-1});
    var tickerSection = document.querySelector('.ticker-section');
    if (tickerSection) {
      tickerSection.addEventListener('mouseenter', function(){ tickerTween.timeScale(0.15); });
      tickerSection.addEventListener('mouseleave', function(){ tickerTween.timeScale(1); });
    }
  }

  /* how steps: number parallax */
  document.querySelectorAll('.how__step').forEach(function(step){
    var num = step.querySelector('.how__num');
    gsap.to(num, {yPercent:-40, ease:'none', scrollTrigger:{trigger:step, start:'top bottom', end:'bottom top', scrub:true}});
  });

  /* spotlight cards: staggered reveal + column parallax */
  gsap.utils.toArray('.spot-card').forEach(function(card, i){
    gsap.fromTo(card, {opacity:0, y:60}, {
      opacity:1, y:0, duration:.9, ease:'power3.out', delay:(i%3)*0.08,
      scrollTrigger:{trigger:card, start:'top 90%'}
    });
    var depth = (i % 3 === 1) ? -26 : 0;
    if (depth){
      gsap.to(card, {yPercent: depth, ease:'none', scrollTrigger:{trigger:'.spot-grid', start:'top bottom', end:'bottom top', scrub:true}});
    }
  });

  /* metrics counters */
  document.querySelectorAll('.count').forEach(function(el){
    var target = parseFloat(el.getAttribute('data-target'));
    var obj = {v:0};
    ScrollTrigger.create({
      trigger: el, start:'top 88%', once:true,
      onEnter: function(){
        gsap.to(obj, {v: target, duration:1.8, ease:'power2.out', onUpdate:function(){
          el.textContent = Math.round(obj.v).toLocaleString();
        }});
      }
    });
  });

  /* testimonial mark parallax */
  gsap.to('.testimonial__mark', {yPercent:20, ease:'none', scrollTrigger:{trigger:'.testimonial', start:'top bottom', end:'bottom top', scrub:true}});
})();
</script>
</x-slot:scripts>

</x-app-layout>