<div class="card rounded-lg bg-white p-4 flex flex-col gap-y-20">
    <div>
        @if(!empty($partner['partner_logo']))
            {!! wp_get_attachment_image($partner['partner_logo'], 'medium', false, [
                'class' => 'w-auto max-h-20',
                'loading' => 'lazy',
                'aria-hidden' => 'true',
                'alt' => $partner['partner_name'].' logo'
            ]) !!}
        @endif
    </div>
    <div class="body-md text-balance">
        {{$partner['description']}}
    </div>
    <div class="mt-auto">
        @include('partials.link', [
            'url' => $partner['link']['url'],
            'label' => $partner['link']['title'],
            'target' => $partner['link']['target'],
            'link_size' => 'md'
        ])
    </div>
</div>