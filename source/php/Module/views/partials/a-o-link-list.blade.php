<ul class="c-listing c-listing--padding-4 unlist">
    @foreach ($items as $idx => $item)
        @if (($item['target'] ?? '') === '_blank')
            <li class="c-listing__item c-listing__item-{{ $idx }}">
                @link([
                    'href' => $item['href'],
                    'target' => '_blank',
                    'xfn' => 'noopener noreferrer',
                    'classList' => ['c-listing__link'],
                    'attributeList' => [
                        'aria-label' => $item['label'],
                    ],
                ])
                    <span class="c-listing__label">{{ $item['label'] }}</span>
                    @icon([
                        'icon' => 'chevron_right',
                        'size' => 'md',
                    ])
                    @endicon
                @endlink
            </li>
        @else
            <li class="c-listing__item c-listing__item-{{ $idx }}">
                @link([
                    'href' => $item['href'],
                    'classList' => ['c-listing__link'],
                    'attributeList' => [
                        'aria-label' => $item['label'],
                    ],
                ])
                    <span class="c-listing__label">{{ $item['label'] }}</span>
                    @icon([
                        'icon' => 'chevron_right',
                        'size' => 'md',
                    ])
                    @endicon
                @endlink
            </li>
        @endif
    @endforeach
</ul>
