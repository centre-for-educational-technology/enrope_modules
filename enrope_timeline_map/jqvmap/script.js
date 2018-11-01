jQuery('#vmap').vectorMap({
  map: 'europe_en',
  backgroundColor: null,
  color: '#007bff',
  hoverColor: '#868e96',
  enableZoom: false,
  selectedColor:'#e83e8c',
  selectedRegions: ['nl', 'de', 'tr', 'ee', 'gb', 'es', 'fr'],
  showTooltip: true,
  onLabelShow: function(event, label, code)
  {
    if (code == 'nl')
    {
      label.text('Fryske Akademy Leeuwarden');
    }
    else if (code == 'de')
    {
      label.html('<div class="map-tooltip">Goethe-Universität Frankfurt<br/>Humboldt-Universität zu Berlin<br/>Universität Siegen</div>');
    }
    else if (code == 'tr')
    {
      label.text('İstanbul Üniversitesi-Cerrahpaşa');
    }
    else if (code == 'ee')
    {
      label.text('Tallinna Ülikool');
    }
    else if (code == 'gb')
    {
      label.text('University of Exeter');
    }
    else if (code == 'es')
    {
      label.text('Universitat Ramon Llull Barcelona');
    }
    else if (code == 'fr')
    {
      label.text('Université Sorbonne Nouvelle Paris 3');
    }
    else {
      event.preventDefault();
    }
  },
  onRegionClick: function(event, code, region)
  {
    event.preventDefault();
  }
});