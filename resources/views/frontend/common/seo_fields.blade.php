

@if ($metaContent['page_title'] != null)
    
    @section('title', $metaContent['page_title'])

@endif

@if ($metaContent['meta_tag_description'] != null)
    
    @section('description', $metaContent['meta_tag_description'])

@endif

@if ($metaContent['meta_keywords'] != null)
    
    @section('keywords', $metaContent['meta_keywords'])

@endif

@if ($metaContent['canonical_url'] != null)
    
    @section('canonical_url', $metaContent['canonical_url'])

@endif
