@php
    $faqs = get_field('faqs');
    $heading = get_field('heading');
    $specialRequestSection = get_field('special_requests_section');

    $class_name = "relative py-xl md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide">
        <div class="grid grid-cols-12 gap-y-20 md:gap-y-xl gap-x-0 md:gap-x-sm">
            <div class="col-span-12 md:col-span-10 col-start-1 md:col-start-2 flex flex-col gap-lg md:gap-xl">
                @if(isset($heading) && $heading)
                    <h2 class="headline-3 uppercase">{{$heading}}</h2>
                @endif
                @if(isset($faqs) && count($faqs) > 0)
                    <div class="border-t border-black-100">
                        @foreach($faqs as $faq)
                            <div
                                class="accordion-item border-b border-black-100 transition-all duration-300 ease-out overflow-hidden"
                                data-accordion data-active-background="true">
                                <button class="headline-6 font-bold py-sm w-full text-left uppercase"
                                        data-action-item aria-expanded='false'>{{$faq['question']}}</button>
                                <div class="body-md pb-sm" data-content>{!! $faq['answer'] !!}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @if($specialRequestSection['special_requests_heading'] || $specialRequestSection['special_requests_description'])
                @include('components.assistance-cards-grid', [
                    'heading' => $specialRequestSection['special_requests_heading'],
                    'description' => $specialRequestSection['special_requests_description'],
                    'hoursCards' => $specialRequestSection['office_hours_cards'],
                    'address' => $specialRequestSection['special_requests_address'],
                    'phone' => $specialRequestSection['special_requests_phone'],
                    'email' => $specialRequestSection['special_requests_email']
                ])
            @endif
        </div>
    </div>
@endcomponent
