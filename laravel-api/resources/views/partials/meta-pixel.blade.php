@php
    $pixelId = \App\Models\SiteSetting::get('meta_pixel_id', '');
@endphp

@if($pixelId)
    {{-- Meta Pixel Base Code --}}
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $pixelId }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
             src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"/>
    </noscript>

    {{-- Custom Events Container --}}
    <script>
        window.MetaPixel = {
            trackViewContent: function(contentId, value, currency) {
                if (typeof fbq !== 'undefined') {
                    fbq('track', 'ViewContent', {
                        content_ids: [contentId],
                        content_type: 'product',
                        value: value,
                        currency: currency
                    });
                }
            },
            trackAddToCart: function(contentId, value, currency) {
                if (typeof fbq !== 'undefined') {
                    fbq('track', 'AddToCart', {
                        content_ids: [contentId],
                        content_type: 'product',
                        value: value,
                        currency: currency
                    });
                }
            },
            trackPurchase: function(contentId, value, currency, eventId) {
                if (typeof fbq !== 'undefined') {
                    fbq('track', 'Purchase', {
                        content_ids: [contentId],
                        content_type: 'product',
                        value: value,
                        currency: currency
                    }, {eventID: eventId});
                }
            }
        };
    </script>
@endif
