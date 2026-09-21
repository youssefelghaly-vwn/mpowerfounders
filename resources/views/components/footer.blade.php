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
      </div>
    </div>
    <div class="footer__bottom">
      <span>{!! $copyright !!}</span>
      <span>{{ $network }}</span>
    </div>
  </div>
</footer>