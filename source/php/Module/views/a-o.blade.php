@php
    $moduleTitlePlain = isset($postTitle) ? trim(wp_strip_all_tags((string) $postTitle)) : '';
    $showRegionHeading = $moduleTitlePlain !== '';
@endphp

<div class="mod-a-o" id="mod-a-o-{{ $instanceId }}"
    @if (!empty($rootStyle ?? '')) style="{{ $rootStyle }}" @endif
    @if ($showRegionHeading) aria-labelledby="{{ $regionHeadingId }}" @endif>
    @if (empty($hasLinks))
        @typography([
            'element' => 'p',
            'classList' => ['mod-a-o__empty', 'u-margin__top--0'],
        ])
            {{ $i18n['empty'] ?? '' }}
        @endtypography
    @else
        @if ($showRegionHeading)
            @typography([
                'element' => 'h2',
                'variant' => 'h2',
                'id' => $regionHeadingId,
                'classList' => ['mod-a-o__region-title', 'u-margin__top--0'],
            ])
                {{ $moduleTitlePlain }}
            @endtypography
        @endif

        <nav class="mod-a-o__jump" aria-label="{{ $i18n['jumpLabel'] ?? '' }}">
            <ul class="mod-a-o__jump-list unlist u-display--flex u-flex-wrap">
                @foreach ($sections as $section)
                    <li class="mod-a-o__jump-item">
                        @link([
                            'href' => '#' . $section['sectionId'],
                            'classList' => ['mod-a-o__jump-link'],
                            'attributeList' => [
                                'aria-label' => sprintf(
                                    /* translators: %s: letter or group name */
                                    __('Jump to letter %s', 'modularity-a-o'),
                                    $section['letter']
                                ),
                            ],
                        ])
                            {{ $section['letter'] }}
                        @endlink
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="mod-a-o__body">
            @foreach ($sections as $section)
                <section class="mod-a-o__section" id="{{ $section['sectionId'] }}"
                    aria-labelledby="{{ $section['headingId'] }}">
                    @typography([
                        'element' => 'h3',
                        'variant' => 'h3',
                        'id' => $section['headingId'],
                        'classList' => ['mod-a-o__section-title', 'u-margin__top--0'],
                    ])
                        {{ $section['letter'] }}
                    @endtypography

                    @include('partials.a-o-link-list', ['items' => $section['items']])
                </section>
            @endforeach
        </div>
    @endif
</div>
