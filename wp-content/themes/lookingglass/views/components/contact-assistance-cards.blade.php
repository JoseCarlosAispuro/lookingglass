@php
    $heading = get_field('heading');
    $introText = get_field('intro_text');
    $accessibilityCards = get_field('availability_cards');
    $address = get_field('address');
    $phoneNumber = get_field('phone_number');
    $emailAddress = get_field('email_address');
    
    $background_color = $background_color ?? 'white';
    $text_color = $background_color === 'black' ? 'white' : 'black';

    $class_name = "relative py-xl md:py-sm bg-{$background_color} text-{$text_color}";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide">
        <div class="grid grid-cols-12 gap-0 md:gap-sm">
            @include('components.assistance-cards-grid', [
                    'heading' => $heading,
                    'description' => $introText,
                    'hoursCards' => $accessibilityCards,
                    'address' => $address,
                    'phone' => $phoneNumber,
                    'email' => $emailAddress
                ])
        </div>
    </div>
@endcomponent
