@props(['title' => 'Sign in'])

{{--
    Registered as <x-guest-layout>. These pages (login, register,
    password reset) are user-facing, not just an admin backend gate — so
    rather than a separate Tailwind shell, this wraps the site's own
    <x-app-layout>: same nav, footer, grain, fonts, and ink/paper/gold
    system as the homepage.

    Nav anchor links (#how, #apply, etc.) only make sense on the
    homepage itself, so they're overridden here to point at /#section —
    same links, still work from any page, no need to duplicate the nav.
--}}
<x-app-layout :title="$title . ' — MPower Founders'" :nav-links="[
    ['href' => '/#how', 'label' => 'Platform'],
    ['href' => '/#spotlight', 'label' => 'Founders'],
    ['href' => '/#metrics', 'label' => 'Metrics'],
    ['href' => '/#proof', 'label' => 'Proof'],
    ['href' => '/#apply', 'label' => 'Apply'],
]" nav-cta-href="/#apply">

    <x-slot:styles>
        .auth-section{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:180px 24px 100px;
        }
        .auth-card{
            width:100%;
            max-width:400px;
            background:var(--paper);
            border:1px solid var(--paper-line);
            border-radius:20px;
            padding:44px 38px;
        }
        .auth-eyebrow{margin-bottom:22px;}
        .auth-title{
            font-size:clamp(24px,3vw,30px);
            font-weight:800;
            letter-spacing:-.02em;
            margin-bottom:8px;
        }
        .auth-sub{
            color:var(--graphite-dark);
            font-size:14.5px;
            line-height:1.5;
            font-weight:500;
            margin-bottom:32px;
        }
        .auth-status{
            margin-bottom:24px;
            padding:12px 14px;
            border-radius:10px;
            border:1px solid var(--grow);
            background:rgba(125,156,127,.12);
            color:#3f5c41;
            font-size:13px;
            font-weight:500;
        }
        .auth-field{margin-bottom:20px;}
        .auth-field label{
            display:block;
            font-family:var(--font-mono);
            font-size:11px;
            letter-spacing:.06em;
            text-transform:uppercase;
            color:var(--graphite-dark);
            margin-bottom:8px;
        }
        .auth-field input{
            width:100%;
            padding:12px 14px;
            border:1px solid var(--paper-line);
            border-radius:10px;
            background:var(--paper-dim);
            font-family:var(--font-display);
            font-size:14.5px;
            color:var(--ink);
            transition:border-color .3s var(--ease), background .3s var(--ease);
        }
        .auth-field input:focus{
            outline:none;
            border-color:var(--gold);
            background:var(--paper);
        }
        .auth-error{
            margin-top:6px;
            font-size:12.5px;
            color:#b5453f;
        }
        .auth-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:28px;
            font-size:13px;
        }
        .auth-check{
            display:flex;
            align-items:center;
            gap:8px;
            color:var(--graphite-dark);
            cursor:pointer;
        }
        .auth-check input{ accent-color:var(--gold); }
        .auth-submit{
            width:100%;
            justify-content:center;
            border:none;
            cursor:pointer;
        }
        .auth-foot{
            margin-top:28px;
            text-align:center;
            font-size:13.5px;
            color:var(--graphite-dark);
        }
        .auth-foot a{
            color:var(--ink);
            font-weight:700;
        }
    </x-slot:styles>

    <section class="auth-section">
        <div class="auth-card reveal">
            <div class="eyebrow auth-eyebrow">MPower Founders</div>

            @if (session('status'))
                <div class="auth-status">{{ session('status') }}</div>
            @endif

            {{ $slot }}
        </div>
    </section>

</x-app-layout>