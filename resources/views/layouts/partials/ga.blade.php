{{-- Google Analytics (GA4) --}}
@if(app()->environment('production'))
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.ga.measurement_id', 'G-5GSGQCL3WR') }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  // Consent Mode básico (nega por padrão; você pode liberar no banner)
  gtag('consent', 'default', { analytics_storage: 'denied' });

  gtag('config', '{{ config('services.ga.measurement_id', 'G-5GSGQCL3WR') }}', { anonymize_ip: true });
</script>
@endif
