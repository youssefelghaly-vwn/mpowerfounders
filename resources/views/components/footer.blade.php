@props([
    'tag'      => 'Founder-grade distribution for the people who\'d rather build than post.',
    'columns'  => [
        'Platform' => [
            ['href' => '#how',       'label' => 'How it works'],
            ['href' => '#spotlight', 'label' => 'Founders'],
            ['href' => '#metrics',   'label' => 'Metrics'],
        ],
        'Company' => [
            ['href' => '#', 'label' => 'About'],
            ['href' => '#', 'label' => 'Careers'],
            ['href' => '#', 'label' => 'Contact'],
        ],
        'Connect' => [
            ['href' => '#', 'label' => 'X / Twitter'],
            ['href' => '#', 'label' => 'LinkedIn'],
        ],
    ],
    'copyright' => '&copy; ' . date('Y') . ' MPower Founders',
    'network'   => 'MPower Venture Club network',
])

<footer class="footer">
  <div class="wrap">
    <div class="footer__top">
      <div>
        <div class="footer__logo">MPOWER<span>&middot;</span>FOUNDERS</div>
        <p class="footer__tag">{{ $tag }}</p>
      </div>
      <div class="footer__cols">
        @foreach ($columns as $title => $links)
          <div class="footer__col">
            <div class="footer__col-title">{{ $title }}</div>
            @foreach ($links as $link)
              <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
            @endforeach
          </div>
        @endforeach

        {{-- Account column, built here rather than passed in: the same
             footer ships on the marketing site and the auth pages. --}}
        <div class="footer__col">
          <div class="footer__col-title">Account</div>
          @auth
            <a href="{{ auth()->user()->dashboardUrl() }}">Dashboard</a>
            @if (auth()->user()->isClient())
              <a href="{{ route('portal.projects.index') }}">My projects</a>
              <a href="{{ route('portal.projects.create') }}">New upload</a>
            @endif
          @else
            <a href="{{ route('login') }}">Sign in</a>
            <a href="{{ route('register') }}">Apply for access</a>
            <a href="{{ route('password.request') }}">Forgot password</a>
          @endauth
        </div>
      </div>
    </div>
    <div class="footer__bottom">
      <span>{!! $copyright !!}</span>
      <span>{{ $network }}</span>
    </div>
  </div>
</footer>