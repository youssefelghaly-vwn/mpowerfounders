@props([
    'title' => 'MPower Founders — Founder voice, built to travel',
    'navLinks' => null,
    'navCtaHref' => null,
])
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

{{-- GSAP loaded synchronously in <head> so any component/page inline <script>
     further down the document can call gsap.* immediately without waiting
     on a <script> tag placed later in the body. --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script>
    window.reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (typeof gsap !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }
</script>

<style>
  :root{
    --ink:#0d0d0f;
    --ink-soft:#161619;
    --ink-line: rgba(243,241,236,0.12);
    --paper:#f3f1ec;
    --paper-dim:#e8e4da;
    --paper-line: rgba(13,13,15,0.12);
    --gold:#caa64f;
    --gold-dim: rgba(202,166,79,0.16);
    --grow:#7d9c7f;
    --graphite:#8a8a8f;
    --graphite-dark:#5c5c60;
    --font-display:'Hanken Grotesk', sans-serif;
    --font-mono:'JetBrains Mono', monospace;
    --ease: cubic-bezier(.22,1,.36,1);
  }

  *{margin:0;padding:0;box-sizing:border-box;}

  html{scroll-behavior:auto;}

  body{
    background:var(--paper);
    color:var(--ink);
    font-family:var(--font-display);
    overflow-x:hidden;
    cursor:default;
    -webkit-font-smoothing:antialiased;
  }

  a{color:inherit;text-decoration:none;}
  button{font-family:inherit;cursor:pointer;border:none;background:none;color:inherit;}
  ul{list-style:none;}
  img{max-width:100%;display:block;}

  ::selection{background:var(--gold);color:var(--ink);}

  :focus-visible{outline:2px solid var(--gold);outline-offset:3px;}

  .eyebrow{
    font-family:var(--font-mono);
    font-size:12px;
    letter-spacing:.14em;
    text-transform:uppercase;
    display:flex;
    align-items:center;
    gap:10px;
  }
  .eyebrow::before{
    content:'';
    width:6px;height:6px;
    background:var(--gold);
    border-radius:50%;
    display:inline-block;
    flex-shrink:0;
  }

  .wrap{
    max-width:1240px;
    margin:0 auto;
    padding:0 40px;
  }

  /* grain overlay */
  .grain{
    position:fixed;inset:0;
    pointer-events:none;
    z-index:9999;
    opacity:.05;
    mix-blend-mode:overlay;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  }

  /* custom cursor */
  .cursor-dot{
    position:fixed;
    top:0;left:0;
    width:9px;height:9px;
    background:var(--ink);
    border-radius:50%;
    pointer-events:none;
    z-index:9998;
    transform:translate(-50%,-50%);
    transition:background .25s var(--ease), width .25s var(--ease), height .25s var(--ease), border .25s var(--ease);
  }
  .cursor-dot.on-dark{background:var(--paper);}
  .cursor-dot.hover{
    width:52px;height:52px;
    background:transparent;
    border:1.5px solid var(--gold);
  }
  @media (pointer:coarse){ .cursor-dot{display:none;} }

  /* ============ NAVBAR ============ */
  .navbar{
    position:fixed;
    top:22px;
    left:50%;
    transform:translateX(-50%);
    z-index:500;
    width:min(1160px, calc(100% - 40px));
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:16px 14px 16px 26px;
    border-radius:999px;
    background:rgba(13,13,15,.0);
    border:1px solid transparent;
    transition:background .5s var(--ease), border-color .5s var(--ease), width .5s var(--ease), padding .5s var(--ease), box-shadow .5s var(--ease);
  }
  .navbar.is-scrolled{
    background:rgba(13,13,15,.82);
    backdrop-filter:blur(16px) saturate(140%);
    -webkit-backdrop-filter:blur(16px) saturate(140%);
    border-color:rgba(243,241,236,.1);
    box-shadow:0 20px 50px -20px rgba(0,0,0,.5);
    width:min(880px, calc(100% - 40px));
    padding:10px 10px 10px 22px;
  }
  .navbar__logo{
    font-weight:800;
    font-size:16px;
    letter-spacing:-.01em;
    color:var(--ink);
    display:flex;
    align-items:center;
    gap:8px;
    white-space:nowrap;
    transition:color .5s var(--ease);
  }
  .navbar.is-scrolled .navbar__logo{color:var(--paper);}
  .navbar__logo span{color:var(--gold);}
  .navbar__mark{
    width:8px;height:8px;
    border-radius:50%;
    background:var(--gold);
    flex-shrink:0;
  }

  .navbar__links{
    display:flex;
    align-items:center;
    gap:4px;
    position:relative;
  }
  .navbar__link{
    position:relative;
    z-index:2;
    font-size:13.5px;
    font-weight:600;
    padding:9px 16px;
    color:var(--ink);
    border-radius:999px;
    transition:color .35s var(--ease);
    white-space:nowrap;
  }
  .navbar.is-scrolled .navbar__link{color:rgba(243,241,236,.72);}
  .navbar__link.is-active,
  .navbar__link:hover{color:var(--ink);}
  .navbar.is-scrolled .navbar__link.is-active,
  .navbar.is-scrolled .navbar__link:hover{color:var(--ink);}

  .navbar__indicator{
    position:absolute;
    top:0;left:0;
    height:100%;
    background:var(--gold);
    border-radius:999px;
    z-index:1;
    opacity:0;
  }

  .navbar__cta{
    display:flex;
    align-items:center;
    gap:14px;
  }
  /* the log-out control is a form so it can POST; keep it from breaking
     the row the way a block-level <form> would */
  .navbar__logout{display:flex;}
  .btn-gold{
    font-family:var(--font-mono);
    font-size:12.5px;
    font-weight:500;
    letter-spacing:.02em;
    background:var(--gold);
    color:var(--ink);
    padding:12px 22px;
    border-radius:999px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    transition:transform .35s var(--ease), box-shadow .35s var(--ease);
  }
  .btn-gold:hover{transform:translateY(-2px);box-shadow:0 14px 30px -10px rgba(202,166,79,.55);}
  .btn-gold svg{width:13px;height:13px;transition:transform .3s var(--ease);}
  .btn-gold:hover svg{transform:translate(2px,-2px);}

  .navbar__burger{
    display:none;
    width:38px;height:38px;
    border-radius:50%;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    gap:5px;
  }
  .navbar__burger span{
    width:16px;height:1.5px;
    background:var(--ink);
    transition:background .4s var(--ease);
  }
  .navbar.is-scrolled .navbar__burger span{background:var(--paper);}

  /* mobile full-screen nav */
  .mobile-nav{
    position:fixed;inset:0;
    background:var(--ink);
    z-index:600;
    display:flex;
    flex-direction:column;
    justify-content:center;
    padding:40px;
    clip-path:circle(0px at calc(100% - 46px) 46px);
    transition:clip-path .7s var(--ease);
    pointer-events:none;
  }
  .mobile-nav.is-open{
    clip-path:circle(150% at calc(100% - 46px) 46px);
    pointer-events:auto;
  }
  .mobile-nav__close{
    position:absolute;
    top:28px;right:28px;
    width:38px;height:38px;
    color:var(--paper);
    font-size:22px;
  }
  .mobile-nav__link{
    display:block;
    font-size:38px;
    font-weight:700;
    color:var(--paper);
    padding:14px 0;
    border-bottom:1px solid var(--ink-line);
    opacity:0;
    transform:translateY(24px);
  }
  .mobile-nav__foot{
    margin-top:30px;
    font-family:var(--font-mono);
    font-size:12px;
    color:var(--graphite);
    opacity:0;
  }

  /* ============ HERO ============ */
  .hero{
    position:relative;
    min-height:100vh;
    display:flex;
    flex-direction:column;
    justify-content:center;
    padding:160px 0 90px;
    overflow:hidden;
    background:var(--paper);
  }
  .hero__bg{
    position:absolute;
    inset:0;
    z-index:0;
    background:
      radial-gradient(ellipse 700px 500px at 82% 8%, var(--gold-dim), transparent 60%),
      radial-gradient(ellipse 900px 700px at -5% 100%, rgba(13,13,15,.05), transparent 60%);
  }
  .hero__grid-lines{
    position:absolute;inset:0;
    z-index:0;
    background-image:
      linear-gradient(var(--paper-line) 1px, transparent 1px),
      linear-gradient(90deg, var(--paper-line) 1px, transparent 1px);
    background-size: 100% 84px, 84px 100%;
    opacity:.5;
    mask-image:linear-gradient(to bottom, black, transparent 88%);
  }

  .hero__content{position:relative;z-index:2;}

  .hero__eyebrow{margin-bottom:26px;}

  .hero__title{
    font-size:clamp(42px, 7.2vw, 104px);
    font-weight:800;
    line-height:.98;
    letter-spacing:-.03em;
    max-width:16ch;
  }
  .hero__title .line{overflow:hidden;display:block;}
  .hero__title .line span{display:block;transform:translateY(110%);}
  .hero__title em{
    font-style:normal;
    color:var(--graphite-dark);
  }
  .hero__title .accent{color:var(--gold);}

  .hero__row{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:60px;
    margin-top:40px;
    flex-wrap:wrap;
  }

  .hero__sub{
    font-size:18px;
    line-height:1.55;
    color:var(--graphite-dark);
    max-width:38ch;
    font-weight:500;
  }

  .hero__actions{
    display:flex;
    align-items:center;
    gap:22px;
    flex-shrink:0;
  }
  .link-underline{
    font-size:13.5px;
    font-weight:600;
    font-family:var(--font-mono);
    display:flex;
    align-items:center;
    gap:8px;
    position:relative;
    padding-bottom:3px;
  }
  .link-underline::after{
    content:'';
    position:absolute;
    left:0;bottom:0;
    width:100%;height:1px;
    background:var(--ink);
    transform:scaleX(1);
    transform-origin:right;
    transition:transform .4s var(--ease);
  }
  .link-underline:hover::after{transform:scaleX(0);transform-origin:left;}

  /* floating video card in hero */
  .hero__card{
    position:absolute;
    right:40px;
    top:190px;
    width:270px;
    z-index:3;
    background:var(--ink);
    border-radius:20px;
    padding:16px;
    box-shadow:0 40px 80px -30px rgba(13,13,15,.5);
    opacity:0;
  }
  .hero__card-thumb{
    width:100%;
    aspect-ratio:9/11;
    border-radius:38% 62% 61% 39% / 44% 41% 59% 56%;
    background:var(--ink-soft);
    position:relative;
    overflow:hidden;
    display:flex;
    align-items:flex-end;
    padding:14px;
  }
  .hero__card-img{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    object-fit:cover;
  }
  .hero__card-thumb::before{
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(to top, rgba(13,13,15,.75) 0%, rgba(13,13,15,.05) 45%, transparent 65%);
  }
  .hero__play{
    width:34px;height:34px;
    border-radius:50%;
    background:rgba(243,241,236,.14);
    backdrop-filter:blur(6px);
    display:flex;align-items:center;justify-content:center;
    position:absolute;
    top:50%;left:50%;
    transform:translate(-50%,-50%);
  }
  .hero__play svg{width:11px;height:11px;fill:var(--paper);margin-left:2px;}
  .hero__card-tag{
    font-family:var(--font-mono);
    font-size:10.5px;
    color:var(--paper);
    background:rgba(243,241,236,.1);
    padding:5px 9px;
    border-radius:999px;
    position:relative;
  }
  .hero__card-meta{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:12px;
    padding:0 2px;
  }
  .hero__card-name{font-size:13px;font-weight:700;color:var(--paper);}
  .hero__card-role{font-family:var(--font-mono);font-size:10.5px;color:var(--graphite);margin-top:2px;}
  .hero__card-views{
    font-family:var(--font-mono);
    font-size:12px;
    color:var(--grow);
    display:flex;
    align-items:center;
    gap:4px;
    font-weight:600;
  }

  .hero__scroll{
    position:absolute;
    bottom:36px;
    left:40px;
    z-index:3;
    display:flex;
    align-items:center;
    gap:12px;
    font-family:var(--font-mono);
    font-size:11px;
    letter-spacing:.08em;
    color:var(--graphite-dark);
    text-transform:uppercase;
  }
  .hero__scroll-line{
    width:1px;height:34px;
    background:var(--paper-line);
    position:relative;
    overflow:hidden;
  }
  .hero__scroll-line::after{
    content:'';
    position:absolute;
    top:0;left:0;width:100%;height:40%;
    background:var(--ink);
    animation:scrollLine 1.8s ease-in-out infinite;
  }
  @keyframes scrollLine{
    0%{transform:translateY(-100%);}
    100%{transform:translateY(250%);}
  }

  /* ============ ILLUSTRATION SYSTEM ============ */
  .hero__card-tag{position:relative;z-index:2;}
  .hero__play{z-index:2;}

  .how__icon{
    width:32px;height:32px;
    border-radius:50%;
    border:1px solid var(--paper-line);
    color:var(--ink);
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    margin-bottom:14px;
  }
  .how__icon svg{width:16px;height:16px;}
  .how__name-row{display:flex;flex-direction:column;}

  .spot-card__img{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    object-fit:cover;
    transition:transform .6s var(--ease);
  }
  .spot-card:hover .spot-card__img{transform:scale(1.06);}
  .spot-card__scrim{
    position:absolute;
    inset:0;
    background:linear-gradient(to top, rgba(13,13,15,.94) 0%, rgba(13,13,15,.6) 30%, rgba(13,13,15,.08) 58%, rgba(13,13,15,.35) 100%);
  }
  .spot-card--gold .spot-card__scrim{background:linear-gradient(to top, rgba(13,13,15,.94) 0%, rgba(13,13,15,.6) 30%, rgba(13,13,15,.08) 58%, rgba(90,72,20,.25) 100%);}
  .spot-card--sage .spot-card__scrim{background:linear-gradient(to top, rgba(13,13,15,.94) 0%, rgba(13,13,15,.6) 30%, rgba(13,13,15,.08) 58%, rgba(30,48,32,.3) 100%);}

  .testimonial__avatar-img{
    width:42px;height:42px;
    border-radius:50%;
    object-fit:cover;
    display:block;
  }

  .cta-final__illo{
    width:104px;
    height:104px;
    margin:0 auto 22px;
    opacity:0;
    border-radius:38% 62% 61% 39% / 44% 41% 59% 56%;
    overflow:hidden;
  }
  .cta-final__illo img{width:100%;height:100%;object-fit:cover;}

  /* ============ TICKER ============ */
  .ticker-section{
    background:var(--ink);
    border-top:1px solid var(--ink-line);
    border-bottom:1px solid var(--ink-line);
    padding:20px 0;
    overflow:hidden;
    position:relative;
  }
  .ticker-track{
    display:flex;
    width:max-content;
  }
  .ticker-track span.grp{display:flex;}
  .ticker-item{
    font-family:var(--font-mono);
    font-size:13px;
    color:var(--paper);
    display:flex;
    align-items:center;
    gap:10px;
    padding:0 34px;
    white-space:nowrap;
    border-right:1px solid var(--ink-line);
  }
  .ticker-item b{color:var(--gold);font-weight:600;}
  .ticker-item .up{color:var(--grow);}
  .ticker-dot{width:4px;height:4px;border-radius:50%;background:var(--graphite);}

  /* ============ SECTION SHARED ============ */
  .section{padding:150px 0;}
  .section-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:40px;
    margin-bottom:80px;
    flex-wrap:wrap;
  }
  .section-title{
    font-size:clamp(32px,4vw,52px);
    font-weight:800;
    letter-spacing:-.02em;
    line-height:1.05;
    max-width:14ch;
  }
  .section-desc{
    max-width:34ch;
    color:var(--graphite-dark);
    font-size:15.5px;
    line-height:1.6;
    font-weight:500;
  }

  /* ============ HOW IT WORKS ============ */
  .how{background:var(--paper);}
  .how__list{
    display:flex;
    flex-direction:column;
  }
  .how__step{
    display:grid;
    grid-template-columns:120px 1fr 1fr;
    gap:40px;
    padding:44px 0;
    border-top:1px solid var(--paper-line);
    align-items:start;
  }
  .how__step:last-child{border-bottom:1px solid var(--paper-line);}
  .how__num{
    font-family:var(--font-mono);
    font-size:15px;
    color:var(--gold);
    font-weight:600;
    padding-top:4px;
  }
  .how__name{
    font-size:clamp(26px,3vw,38px);
    font-weight:700;
    letter-spacing:-.02em;
  }
  .how__body{
    color:var(--graphite-dark);
    font-size:15.5px;
    line-height:1.65;
    font-weight:500;
    max-width:44ch;
    padding-top:6px;
  }
  @media (max-width:820px){
    .how__step{grid-template-columns:50px 1fr;}
    .how__body{grid-column:2/3;}
  }

  /* ============ SPOTLIGHT GRID ============ */
  .spotlight{background:var(--ink);color:var(--paper);}
  .spotlight .section-desc{color:var(--graphite);}
  .spotlight .eyebrow{color:var(--paper);}

  .spot-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
  }
  .spot-card{
    position:relative;
    border-radius:16px;
    overflow:hidden;
    aspect-ratio:3/4;
    background:var(--ink-soft);
    display:flex;
    flex-direction:column;
    justify-content:space-between;
  }
  .spot-card__top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    position:relative;
    z-index:2;
    padding:18px 18px 0;
  }
  .spot-card__tag{
    font-family:var(--font-mono);
    font-size:10px;
    background:rgba(243,241,236,.14);
    backdrop-filter:blur(6px);
    padding:5px 9px;
    border-radius:999px;
  }
  .spot-card__play{
    width:38px;height:38px;
    border-radius:50%;
    border:1px solid rgba(243,241,236,.35);
    background:rgba(13,13,15,.25);
    backdrop-filter:blur(6px);
    display:flex;align-items:center;justify-content:center;
    transition:background .35s var(--ease), transform .35s var(--ease);
  }
  .spot-card:hover .spot-card__play{background:var(--gold);border-color:var(--gold);transform:scale(1.06);}
  .spot-card__play svg{width:10px;height:10px;fill:var(--paper);margin-left:1px;transition:fill .3s;}
  .spot-card:hover .spot-card__play svg{fill:var(--ink);}

  .spot-card__bottom{position:relative;z-index:2;padding:0 18px 18px;}
  .spot-card__name{font-size:17px;font-weight:700;}
  .spot-card__role{font-family:var(--font-mono);font-size:11px;color:var(--graphite);margin-top:3px;}
  .spot-card__stats{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:14px;
    padding-top:14px;
    border-top:1px solid rgba(243,241,236,.16);
    font-family:var(--font-mono);
    font-size:11px;
  }
  .spot-card__views{color:var(--paper);font-weight:600;}
  .spot-card__growth{color:var(--grow);font-weight:600;}

  @media (max-width:980px){ .spot-grid{grid-template-columns:repeat(2,1fr);} }
  @media (max-width:620px){ .spot-grid{grid-template-columns:1fr;} }

  /* ============ METRICS ============ */
  .metrics{background:var(--paper);}
  .metrics-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:1px;
    background:var(--paper-line);
    border:1px solid var(--paper-line);
  }
  .metric{
    background:var(--paper);
    padding:44px 30px;
  }
  .metric__num{
    font-size:clamp(36px,4vw,58px);
    font-weight:800;
    letter-spacing:-.02em;
    display:flex;
    align-items:baseline;
    gap:2px;
  }
  .metric__num .unit{font-size:.5em;color:var(--gold);font-weight:700;}
  .metric__label{
    font-family:var(--font-mono);
    font-size:12px;
    color:var(--graphite-dark);
    margin-top:10px;
    letter-spacing:.01em;
  }
  @media (max-width:820px){ .metrics-grid{grid-template-columns:repeat(2,1fr);} }

  /* ============ CLIENT PROOF ============ */
  .proof{background:var(--paper);}
  .proof__list{display:flex;flex-direction:column;}
  .proof__row{
    display:grid;
    grid-template-columns:260px 1fr;
    gap:40px;
    padding:38px 0;
    border-top:1px solid var(--paper-line);
    align-items:start;
  }
  .proof__row:last-child{border-bottom:1px solid var(--paper-line);}
  .proof__name{
    font-size:clamp(21px,2.2vw,27px);
    font-weight:700;
    letter-spacing:-.01em;
  }
  .proof__meta{
    font-family:var(--font-mono);
    font-size:11px;
    color:var(--gold);
    font-weight:600;
    margin-top:9px;
    line-height:1.5;
  }
  .proof__desc{
    color:var(--graphite-dark);
    font-size:15px;
    line-height:1.65;
    font-weight:500;
    max-width:58ch;
  }
  .proof__clips{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-top:18px;
  }
  .proof__clip{
    display:inline-flex;
    align-items:center;
    gap:9px;
    padding:9px 8px 9px 14px;
    border-radius:999px;
    border:1px solid var(--paper-line);
    font-size:12px;
    font-weight:600;
    transition:border-color .3s var(--ease), background .3s var(--ease), color .3s var(--ease);
  }
  .proof__clip:hover{border-color:var(--ink);background:var(--ink);color:var(--paper);}
  .proof__clip-title{
    max-width:230px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .proof__clip-views{
    font-family:var(--font-mono);
    color:var(--grow);
    font-weight:700;
    white-space:nowrap;
  }
  .proof__clip svg{width:11px;height:11px;flex-shrink:0;opacity:.55;transition:opacity .3s;}
  .proof__clip:hover svg{opacity:1;}

  @media (max-width:820px){
    .proof__row{grid-template-columns:1fr;gap:14px;}
    .proof__clip-title{max-width:160px;}
  }

  /* ============ TESTIMONIAL ============ */
  .testimonial{
    background:var(--ink);
    color:var(--paper);
    padding:170px 0;
    position:relative;
    overflow:hidden;
  }
  .testimonial__mark{
    font-family:var(--font-display);
    font-size:220px;
    font-weight:800;
    color:rgba(243,241,236,.05);
    position:absolute;
    top:-40px;left:50%;
    transform:translateX(-50%);
    line-height:1;
    z-index:0;
    pointer-events:none;
  }
  .testimonial__quote{
    position:relative;
    z-index:1;
    font-size:clamp(26px,3.4vw,44px);
    font-weight:600;
    line-height:1.35;
    letter-spacing:-.015em;
    max-width:20ch;
    margin:0 auto;
    text-align:center;
  }
  .testimonial__foot{
    position:relative;z-index:1;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:14px;
    margin-top:44px;
  }
  .testimonial__avatar{
    width:40px;height:40px;
    border-radius:50%;
    background:linear-gradient(160deg,var(--gold),#8a6f2f);
  }
  .testimonial__name{font-size:14px;font-weight:700;}
  .testimonial__role{font-family:var(--font-mono);font-size:11.5px;color:var(--graphite);}

  /* ============ FINAL CTA ============ */
  .cta-final{
    background:var(--paper);
    padding:150px 0 160px;
    text-align:center;
  }
  .cta-final__title{
    font-size:clamp(36px,6vw,84px);
    font-weight:800;
    letter-spacing:-.03em;
    line-height:1.02;
  }
  .cta-final__title .accent{color:var(--gold);font-style:italic;font-family:'Hanken Grotesk';}
  .cta-final__sub{
    margin-top:22px;
    color:var(--graphite-dark);
    font-size:16px;
    font-weight:500;
  }
  .cta-final__actions{
    margin-top:44px;
    display:flex;
    justify-content:center;
    gap:18px;
    flex-wrap:wrap;
  }
  .btn-ghost{
    font-family:var(--font-mono);
    font-size:12.5px;
    font-weight:500;
    padding:14px 26px;
    border-radius:999px;
    border:1px solid var(--paper-line);
  }
  .btn-gold.lg, .btn-ghost.lg{padding:16px 30px;font-size:13px;}

  /* ============ FOOTER ============ */
  .footer{
    background:var(--ink);
    color:var(--paper);
    padding:70px 0 34px;
  }
  .footer__top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:40px;
    flex-wrap:wrap;
    padding-bottom:50px;
    border-bottom:1px solid var(--ink-line);
  }
  .footer__logo{font-size:22px;font-weight:800;}
  .footer__logo span{color:var(--gold);}
  .footer__tag{
    font-family:var(--font-mono);
    font-size:12px;
    color:var(--graphite);
    margin-top:10px;
    max-width:28ch;
  }
  .footer__cols{
    display:flex;
    gap:70px;
    flex-wrap:wrap;
  }
  .footer__col-title{
    font-family:var(--font-mono);
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.1em;
    color:var(--graphite);
    margin-bottom:16px;
  }
  .footer__col a{
    display:block;
    font-size:14px;
    font-weight:500;
    color:var(--paper);
    padding:6px 0;
    opacity:.85;
    transition:opacity .3s, transform .3s;
  }
  .footer__col a:hover{opacity:1;transform:translateX(4px);}
  .footer__bottom{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding-top:28px;
    font-family:var(--font-mono);
    font-size:11.5px;
    color:var(--graphite);
    flex-wrap:wrap;
    gap:12px;
  }

  /* reveal utility */
  .reveal{opacity:0;transform:translateY(36px);}

  /* ============ RESPONSIVE ============ */
  @media (max-width:900px){
    .navbar__links{display:none;}
    .navbar__burger{display:flex;}
    .hero__card{display:none;}
    .wrap{padding:0 24px;}
    .section{padding:110px 0;}
    .hero{padding:140px 0 70px;}
    .testimonial{padding:120px 0;}
    .footer__top{flex-direction:column;}
  }

  @media (prefers-reduced-motion: reduce){
    *{animation-duration:.001ms !important; animation-iteration-count:1 !important; transition-duration:.001ms !important;}
  }

  {{ $styles ?? '' }}
</style>
{{ $head ?? '' }}
</head>
<body>

<div class="grain"></div>
<div class="cursor-dot" id="cursorDot"></div>

{{-- Global icon sprite — reusable across any page's content --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <g id="i-mic">
      <rect x="12" y="4" width="8" height="15" rx="4" fill="none" stroke="currentColor" stroke-width="2"/>
      <path d="M7 15a9 9 0 0 0 18 0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <line x1="16" y1="24" x2="16" y2="29" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <line x1="10.5" y1="29" x2="21.5" y2="29" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </g>
    <g id="i-broadcast">
      <circle cx="16" cy="21" r="2.6" fill="currentColor"/>
      <path d="M9.5 21a6.5 6.5 0 0 1 13 0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <path d="M4 21a12 12 0 0 1 24 0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </g>
    <g id="i-growth">
      <path d="M4 25l8-10 6 6 10-14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M19 7h9v9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </g>
  </defs>
</svg>

@if ($navLinks)
    <x-nav :links="$navLinks" :cta-href="$navCtaHref ?? '#apply'" />
@else
    <x-nav />
@endif

{{ $slot }}

<x-footer />

<script>
(function(){
  /* ---------- shared page chrome behavior ----------
     Custom cursor + generic `.reveal` scroll-in utility.
     Nav-specific behavior lives in the nav component's own script.
     Page-specific animation (hero timeline, ticker, metrics counters,
     etc.) lives in each page view alongside its own markup. */

  if (typeof gsap === 'undefined') {
    document.querySelectorAll('.reveal').forEach(function(el){
      el.style.opacity = 1; el.style.transform = 'none';
    });
    return;
  }

  var reduceMotion = window.reduceMotion;

  /* custom cursor */
  var dot = document.getElementById('cursorDot');
  if (window.matchMedia('(pointer:fine)').matches && !reduceMotion && dot){
    var qx = gsap.quickTo(dot, "x", {duration:0.35, ease:"power3.out"});
    var qy = gsap.quickTo(dot, "y", {duration:0.35, ease:"power3.out"});
    window.addEventListener('mousemove', function(e){ qx(e.clientX); qy(e.clientY); });
    document.addEventListener('mouseover', function(e){
      var target = e.target.closest && e.target.closest('[data-cursor="hover"]');
      if (target) dot.classList.add('hover');
    });
    document.addEventListener('mouseout', function(e){
      var target = e.target.closest && e.target.closest('[data-cursor="hover"]');
      if (target) dot.classList.remove('hover');
    });
  } else if (dot) {
    dot.style.display = 'none';
  }

  /* generic reveal-on-scroll utility, used by any `.reveal` element
     any page places inside the slot */
  document.querySelectorAll('.reveal').forEach(function(el){
    gsap.to(el, {
      opacity:1, y:0, duration:.9, ease:'power3.out',
      scrollTrigger:{trigger:el, start:'top 88%'}
    });
  });
})();
</script>

{{ $scripts ?? '' }}

</body>
</html>